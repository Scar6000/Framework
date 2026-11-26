<?php

namespace App\Services;

use App\Enums\Decision;
use App\Imports\ResultatsImport;
use App\Models\Etudiant;
use App\Models\Pv;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class ResultatsImportService
{
    /**
     * @param  string  $mode  'tout_ou_rien' | 'valides_seulement'
     * @return array{importees:int, ignorees:int, refuse:bool, mode:string, erreurs:array}
     */
    public function importer(Pv $pv, UploadedFile $fichier, string $mode): array
    {
        $feuilles = Excel::toArray(new ResultatsImport, $fichier);
        $rows = $feuilles[0] ?? [];

        $valides = [];
        $erreurs = [];
        $vus = [];

        foreach ($rows as $i => $row) {
            $numero = $i + 2; // ligne 1 = en-têtes
            if (collect($row)->filter(fn ($v) => $v !== null && $v !== '')->isEmpty()) {
                continue; // ligne vide
            }

            $data = $this->normaliser($row);
            $v = Validator::make($data, [
                'matricule' => ['required', 'string', 'max:30'],
                'nom' => ['required', 'string', 'max:150'],
                'prenoms' => ['required', 'string', 'max:150'],
                'moyenne_generale' => ['required', 'numeric', 'between:0,20'],
                'decision' => ['required', Rule::enum(Decision::class)],
            ], [], [
                'moyenne_generale' => 'moyenne générale',
            ]);

            $messages = $v->fails() ? $v->errors()->all() : [];

            if (empty($messages)) {
                if (isset($vus[$data['matricule']])) {
                    $messages[] = "Matricule {$data['matricule']} déjà présent à la ligne {$vus[$data['matricule']]}.";
                } else {
                    $vus[$data['matricule']] = $numero;
                }
            }

            if ($messages) {
                $erreurs[] = ['ligne' => $numero, 'messages' => $messages];
            } else {
                $valides[] = $data;
            }
        }

        if ($erreurs && $mode === 'tout_ou_rien') {
            return ['importees' => 0, 'ignorees' => count($erreurs), 'refuse' => true, 'mode' => $mode, 'erreurs' => $erreurs];
        }

        DB::transaction(function () use ($pv, $valides) {
            foreach ($valides as $d) {
                $etudiant = Etudiant::ouCreer($d['matricule'], $d['nom'], $d['prenoms']);
                $pv->lignes()->updateOrCreate(
                    ['etudiant_id' => $etudiant->id],
                    ['moyenne_generale' => $d['moyenne_generale'], 'decision' => $d['decision']]
                );
            }
        });

        ActivityLogger::log('import_resultats', $pv, null, ['importees' => count($valides), 'ignorees' => count($erreurs)]);

        return ['importees' => count($valides), 'ignorees' => count($erreurs), 'refuse' => false, 'mode' => $mode, 'erreurs' => $erreurs];
    }

    private function normaliser(array $row): array
    {
        $decision = Str::of((string) ($row['decision'] ?? ''))->trim()->lower()->ascii()->toString();
        $moyenne = str_replace(',', '.', trim((string) ($row['moyenne_generale'] ?? $row['moyenne'] ?? '')));

        return [
            'matricule' => Str::upper(trim((string) ($row['matricule'] ?? ''))),
            'nom' => trim((string) ($row['nom'] ?? '')),
            'prenoms' => trim((string) ($row['prenoms'] ?? $row['prenom'] ?? '')),
            'moyenne_generale' => $moyenne === '' ? null : $moyenne,
            'decision' => $decision === '' ? null : $decision,
        ];
    }
}
