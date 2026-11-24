<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RendersView;
use App\Models\ActivityLog;
use App\Models\Pv;
use App\Models\User;
use Illuminate\Http\Request;

/** Journal d'activité : lecture seule, admin uniquement. */
class JournalController extends Controller
{
    use RendersView;

    public function index(Request $request)
    {
        $logs = ActivityLog::with('user:id,name,prenoms,role')
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->input('user_id')))
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->input('action')))
            ->when($request->filled('pv_id'), fn ($q) => $q->where('subject_type', (new Pv)->getMorphClass())->where('subject_id', $request->input('pv_id')))
            ->when($request->filled('du'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('du')))
            ->when($request->filled('au'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('au')))
            ->orderByDesc('id')->paginate(50)->withQueryString();

        return $this->render('admin.journal.index', [
            'logs' => $logs,
            'utilisateurs' => User::orderBy('name')->get(['id', 'name', 'prenoms']),
            'actions' => ActivityLog::query()->distinct()->orderBy('action')->pluck('action'),
        ]);
    }

    public function show(ActivityLog $log)
    {
        return $this->render('admin.journal.show', ['log' => $log->load('user:id,name,prenoms,role')]);
    }
}
