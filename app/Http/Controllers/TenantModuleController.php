<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Services\ModuleToggleService;
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
        $wasEnabled = in_array($module, $enabledModules);

        // Bascule avec cascade parent/enfants
        $result = ModuleToggleService::toggle($enabledModules, $module, !$wasEnabled);
        $newModules = $result['modules'];

        $action = $wasEnabled ? 'désactivé' : 'activé';
        $message = "Module '{$availableModules[$module]['label']}' {$action}.";

        if (!empty($result['cascaded'])) {
            $cascadedLabels = array_map(
                fn($key) => $availableModules[$key]['label'] ?? $key,
                $result['cascaded']
            );
            $message .= $wasEnabled
                ? ' (' . implode(', ', $cascadedLabels) . ' également désactivé' . (count($cascadedLabels) > 1 ? 's' : '') . ')'
                : ' (' . implode(', ', $cascadedLabels) . ' également activé)';
        }

        // Provisionnement des pages par défaut à la première activation du site public
        // (directe ou via l'activation d'un sous-module)
        $publicSiteWasEnabled = in_array('public_site', $enabledModules);
        $publicSiteNowEnabled = in_array('public_site', $newModules);

        if (!$publicSiteWasEnabled && $publicSiteNowEnabled) {
            $created = app(\App\Services\PublicSiteProvisioner::class)
                ->provisionDefaultPages($tenant);

            if (!empty($created)) {
                $message .= ' ' . count($created) . ' page(s) par défaut créée(s).';
            }
        }

        $tenant->update(['modules_enabled' => $newModules]);

        return back()->with('success', $message);
    }
}
