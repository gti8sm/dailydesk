<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantModuleController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole(['admin', 'admin_mairie'])) {
            abort(403, 'Accès non autorisé');
        }

        $tenant = Tenant::find(auth()->user()->tenant_id);
        $modules = config('modules', []);

        return view('settings.modules', compact('tenant', 'modules'));
    }

    public function toggle(Request $request, string $module)
    {
        if (!auth()->user()->hasRole(['admin', 'admin_mairie'])) {
            abort(403, 'Accès non autorisé');
        }

        $availableModules = config('modules', []);

        if (!array_key_exists($module, $availableModules)) {
            return back()->with('error', 'Module inconnu.');
        }

        $tenant = Tenant::find(auth()->user()->tenant_id);
        $enabledModules = $tenant->modules_enabled ?? [];

        if (in_array($module, $enabledModules)) {
            $enabledModules = array_values(array_diff($enabledModules, [$module]));
            $message = "Module '{$availableModules[$module]['label']}' désactivé.";
        } else {
            $enabledModules[] = $module;
            $message = "Module '{$availableModules[$module]['label']}' activé.";
        }

        $tenant->update(['modules_enabled' => $enabledModules]);

        return back()->with('success', $message);
    }
}
