<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RendersView;
use App\Models\Pv;
use App\Services\PvWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/** Transitions d'état d'un PV : soumettre · valider · rejeter · archiver. */
class PvWorkflowController extends Controller
{
    use RendersView;

    public function __construct(private PvWorkflow $workflow) {}

    public function soumettre(Request $request, Pv $pv)
    {
        Gate::authorize('soumettre', $pv);
        $this->workflow->soumettre($pv, $request->user());

        return $this->done('pv.show', ['pv' => $pv], 'PV soumis à validation.', ['pv' => $pv->fresh()]);
    }

    public function valider(Request $request, Pv $pv)
    {
        Gate::authorize('valider', $pv);
        $this->workflow->valider($pv, $request->user());

        return $this->done('pv.show', ['pv' => $pv], 'PV validé.', ['pv' => $pv->fresh()]);
    }

    public function rejeter(Request $request, Pv $pv)
    {
        Gate::authorize('rejeter', $pv);
        $data = $request->validate(['motif' => ['required', 'string', 'min:5', 'max:1000']]);
        $this->workflow->rejeter($pv, $request->user(), $data['motif']);

        return $this->done('dashboard', [], 'PV renvoyé en brouillon avec votre motif.', ['pv' => $pv->fresh()]);
    }

    public function archiver(Request $request, Pv $pv)
    {
        Gate::authorize('archiver', $pv);
        $this->workflow->archiver($pv, $request->user());

        return $this->done('pv.show', ['pv' => $pv], 'PV archivé.', ['pv' => $pv->fresh()]);
    }
}
