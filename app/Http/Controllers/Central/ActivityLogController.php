<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }

        $query = ActivityLog::with(['user', 'tenant'])->latest();

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50)->withQueryString();
        $tenants = Tenant::orderBy('name')->get();
        $actions = ['created', 'updated', 'deleted', 'login', 'logout'];

        return view('central.logs.index', compact('logs', 'tenants', 'actions'));
    }

    public function show(ActivityLog $log)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }

        $log->load(['user', 'tenant']);

        return view('central.logs.show', compact('log'));
    }
}
