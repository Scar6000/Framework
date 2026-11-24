<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Lecture brute (en-têtes : matricule, nom, prenoms, moyenne, credits_valides, decision).
 * Le traitement et la validation sont faits par ResultatsImportService.
 */
class ResultatsImport implements ToArray, WithHeadingRow
{
    public function array(array $array): void
    {
        // Rien : on utilise Excel::toArray() puis ResultatsImportService.
    }
}
