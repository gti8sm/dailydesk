<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Setting;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    protected array $availableModules = [
        'garderie' => [
            'label' => 'Garderie',
            'icon' => 'fas fa-child',
            'color' => 'blue',
            'description' => 'Gestion de la garderie matin et soir',
        ],
        'cantine' => [
            'label' => 'Cantine',
            'icon' => 'fas fa-utensils',
            'color' => 'orange',
            'description' => 'Gestion des présences cantine et repas',
        ],
    ];

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
        ],
        'notifications' => [
            'notify_arrival' => ['label' => 'Notifier arrivée garderie', 'type' => 'boolean', 'default' => '0'],
            'notify_departure' => ['label' => 'Notifier départ garderie', 'type' => 'boolean', 'default' => '0'],
            'notify_absence' => ['label' => 'Notifier absence', 'type' => 'boolean', 'default' => '0'],
            'notify_event' => ['label' => 'Notifier événements', 'type' => 'boolean', 'default' => '1'],
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

        if (in_array($module, $enabledModules)) {
            $enabledModules = array_values(array_diff($enabledModules, [$module]));
            $message = "Module '{$this->availableModules[$module]['label']}' désactivé pour {$tenant->name}.";
        } else {
            $enabledModules[] = $module;
            $message = "Module '{$this->availableModules[$module]['label']}' activé pour {$tenant->name}.";
        }

        $tenant->update(['modules_enabled' => $enabledModules]);

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
