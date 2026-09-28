<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Setting;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    protected array $availableModules;

    public function __construct()
    {
        $this->availableModules = config('modules', []);
    }

    protected array $globalSettings = [
        'garderie' => [
            'garderie_morning_start' => ['label' => 'Début garderie matin', 'type' => 'time', 'default' => '07:00'],
            'garderie_morning_end' => ['label' => 'Fin garderie matin', 'type' => 'time', 'default' => '08:40'],
            'garderie_evening_start' => ['label' => 'Début garderie soir', 'type' => 'time', 'default' => '16:30'],
            'garderie_evening_end' => ['label' => 'Fin garderie soir', 'type' => 'time', 'default' => '18:30'],
        ],
        'cantine' => [
            'cantine_default_meal_type' => ['label' => 'Type de repas par défaut', 'type' => 'select', 'default' => 'midi', 'options' => [
                'midi' => 'Midi',
                'soir' => 'Soir',
            ]],
            'cantine_enable_snack' => ['label' => 'Activer le goûter', 'type' => 'boolean', 'default' => '0'],
            'cantine_menu_mode' => ['label' => 'Mode de gestion des menus', 'type' => 'select', 'default' => 'global', 'options' => [
                'global' => 'Menu global (une cuisine pour toutes les écoles)',
                'per_school' => 'Menu par école (chaque école a son menu)',
            ]],
        ],
        'notifications' => [
            'notify_arrival' => ['label' => 'Notifier arrivée garderie', 'type' => 'boolean', 'default' => '0'],
            'notify_departure' => ['label' => 'Notifier départ garderie', 'type' => 'boolean', 'default' => '0'],
            'notify_absence' => ['label' => 'Notifier absence', 'type' => 'boolean', 'default' => '0'],
            'notify_event' => ['label' => 'Notifier événements', 'type' => 'boolean', 'default' => '1'],
        ],
        'stock' => [
            'stock_default_min_quantity' => ['label' => 'Seuil minimum par défaut', 'type' => 'integer', 'default' => '5'],
            'stock_enable_alerts' => ['label' => 'Activer les alertes email', 'type' => 'boolean', 'default' => '1'],
        ],
    ];

    public function overview()
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }

        $tenants = Tenant::with('domains')->orderBy('name')->get();
        $modules = $this->availableModules;

        return view('central.modules.overview', compact('tenants', 'modules'));
    }

    public function toggleModule(Request $request, Tenant $tenant, string $module)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }

        if (!array_key_exists($module, $this->availableModules)) {
            return back()->with('error', 'Module inconnu.');
        }

        $enabledModules = $tenant->modules_enabled ?? [];
        $wasEnabled = in_array($module, $enabledModules);

        $result = \App\Services\ModuleToggleService::toggle($enabledModules, $module, !$wasEnabled);

        $tenant->update(['modules_enabled' => $result['modules']]);

        $action = $wasEnabled ? 'désactivé' : 'activé';
        $message = "Module '{$this->availableModules[$module]['label']}' {$action} pour {$tenant->name}.";

        if (!empty($result['cascaded'])) {
            $cascadedLabels = array_map(
                fn($key) => $this->availableModules[$key]['label'] ?? $key,
                $result['cascaded']
            );
            $message .= ' (' . implode(', ', $cascadedLabels) . ' également touché)';
        }

        return back()->with('success', $message);
    }

    /**
     * Active ou désactive un module pour TOUS les tenants.
     * La cascade parent/enfants s'applique pour chaque tenant.
     */
    public function toggleModuleForAll(Request $request, string $module)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }

        if (!array_key_exists($module, $this->availableModules)) {
            return back()->with('error', 'Module inconnu.');
        }

        $action = $request->input('action');

        if (!in_array($action, ['enable', 'disable'])) {
            return back()->with('error', 'Action invalide.');
        }

        $enable = $action === 'enable';
        $count = 0;

        Tenant::chunk(100, function ($tenants) use ($module, $enable, &$count) {
            foreach ($tenants as $tenant) {
                $enabledModules = $tenant->modules_enabled ?? [];
                $alreadyEnabled = in_array($module, $enabledModules);

                if ($enable === $alreadyEnabled) {
                    continue; // déjà dans l'état voulu
                }

                $result = \App\Services\ModuleToggleService::toggle($enabledModules, $module, $enable);
                $tenant->update(['modules_enabled' => $result['modules']]);
                $count++;

                // Provisionne les pages par défaut si le site public vient d'être activé
                if ($enable && $module === 'public_site' && $tenant->slug) {
                    tenancy()->initialize($tenant);
                    app(\App\Services\PublicSiteProvisioner::class)->provisionDefaultPages($tenant);
                }
            }
        });

        $actionLabel = $enable ? 'activé' : 'désactivé';

        if ($count === 0) {
            $message = "Le module '{$this->availableModules[$module]['label']}' était déjà {$actionLabel} pour tous les tenants.";
        } else {
            $message = "Module '{$this->availableModules[$module]['label']}' {$actionLabel} pour {$count} tenant(s).";
        }

        return back()->with('success', $message);
    }

    public function globalSettings()
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }

        $settingsGroups = $this->globalSettings;
        $currentValues = [];

        foreach ($settingsGroups as $group => $settings) {
            foreach ($settings as $key => $config) {
                $currentValues[$key] = Setting::whereNull('tenant_id')->where('key', $key)->value('value') ?? $config['default'];
            }
        }

        $modules = $this->availableModules;

        return view('central.modules.settings', compact('settingsGroups', 'currentValues', 'modules'));
    }

    public function updateGlobalSettings(Request $request)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }

        foreach ($this->globalSettings as $group => $settings) {
            foreach ($settings as $key => $config) {
                $value = $request->input($key, $config['default']);

                if ($config['type'] === 'boolean') {
                    $value = $request->has($key) ? '1' : '0';
                }

                Setting::whereNull('tenant_id')->where('key', $key)->delete();
                Setting::create([
                    'key' => $key,
                    'value' => $value,
                    'type' => $config['type'],
                    'group' => $group,
                    'tenant_id' => null,
                ]);
            }
        }

        return back()->with('success', 'Paramètres globaux mis à jour avec succès.');
    }
}
