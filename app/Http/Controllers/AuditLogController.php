<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')
            ->orderByDesc('id')
            ->when($request->filled('action'), fn ($builder) => $builder->where('action', (string) $request->string('action')))
            ->when($request->filled('date_from'), fn ($builder) => $builder->whereDate('created_at', '>=', (string) $request->string('date_from')))
            ->when($request->filled('date_to'), fn ($builder) => $builder->whereDate('created_at', '<=', (string) $request->string('date_to')));

        $logs = $query->paginate(20)->withQueryString();

        $actions = AuditLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('audit.index', [
            'logs' => $logs,
            'actions' => $actions,
            'filters' => $request->only(['action', 'date_from', 'date_to']),
        ]);
    }
}
