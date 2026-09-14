<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Family;
use App\Models\SchoolClass;
use App\Models\Setting;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OnboardingController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            return redirect()->route('central.dashboard');
        }

        $tenant = Tenant::find($user->tenant_id);
        $step = $this->getCurrentStep($tenant);

        $classes = SchoolClass::orderBy('name')->get();

        return view('onboarding.wizard', compact('tenant', 'step', 'classes'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $tenant = Tenant::find($user->tenant_id);
        $step = (int) $request->input('step', 1);

        switch ($step) {
            case 1:
                return $this->saveStep1($request, $tenant);
            case 2:
                return $this->saveStep2($request, $tenant);
            case 3:
                return $this->saveStep3($request, $tenant);
            case 4:
                return $this->saveStep4($request, $tenant);
            default:
                return redirect()->route('onboarding.index');
        }
    }

    public function skip()
    {
        $user = auth()->user();
        $tenant = Tenant::find($user->tenant_id);

        $settings = $tenant->settings ?? [];
        $settings['onboarding_completed'] = true;
        $settings['onboarding_skipped'] = true;
        $tenant->settings = $settings;
        $tenant->save();

        return redirect()->route('dashboard')->with('success', 'Bienvenue sur DailyDesk ! Vous pouvez configurer votre espace à tout moment dans les paramètres.');
    }

    private function saveStep1(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $tenant->logo_path = $path;
        }

        $tenant->name = $validated['name'];
        $tenant->phone = $validated['phone'] ?? $tenant->phone;
        $tenant->address = $validated['address'] ?? $tenant->address;
        $tenant->city = $validated['city'] ?? $tenant->city;
        $tenant->postal_code = $validated['postal_code'] ?? $tenant->postal_code;

        $this->setOnboardingStep($tenant, 1);
        $tenant->save();

        return redirect()->route('onboarding.index')->with('step_completed', 1);
    }

    private function saveStep2(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'classes' => 'nullable|array',
            'classes.*.name' => 'required|string|max:100',
            'classes.*.teacher_name' => 'nullable|string|max:100',
            'school_year' => 'nullable|string|max:20',
        ]);

        $schoolYear = $validated['school_year'] ?? date('Y') . '-' . (date('Y') + 1);

        if (!empty($validated['classes'])) {
            foreach ($validated['classes'] as $classData) {
                if (!empty($classData['name'])) {
                    SchoolClass::create([
                        'name' => $classData['name'],
                        'teacher_name' => $classData['teacher_name'] ?? null,
                        'school_year' => $schoolYear,
                        'is_active' => true,
                    ]);
                }
            }
        }

        Setting::set('school_year', $schoolYear, 'string', 'general');

        $this->setOnboardingStep($tenant, 2);
        $tenant->save();

        return redirect()->route('onboarding.index')->with('step_completed', 2);
    }

    private function saveStep3(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'app_name' => 'nullable|string|max:50',
        ]);

        $tenant->primary_color = $validated['primary_color'];
        $tenant->secondary_color = $validated['secondary_color'];

        if (!empty($validated['app_name'])) {
            Setting::set('app_name', $validated['app_name'], 'string', 'general');
        }

        $this->setOnboardingStep($tenant, 3);
        $tenant->save();

        return redirect()->route('onboarding.index')->with('step_completed', 3);
    }

    private function saveStep4(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'notify_arrival' => 'boolean',
            'notify_departure' => 'boolean',
            'notify_absence' => 'boolean',
            'notify_event' => 'boolean',
        ]);

        Setting::set('notify_arrival', $request->boolean('notify_arrival'), 'boolean', 'notifications');
        Setting::set('notify_departure', $request->boolean('notify_departure'), 'boolean', 'notifications');
        Setting::set('notify_absence', $request->boolean('notify_absence'), 'boolean', 'notifications');
        Setting::set('notify_event', $request->boolean('notify_event'), 'boolean', 'notifications');

        $settings = $tenant->settings ?? [];
        $settings['onboarding_completed'] = true;
        $tenant->settings = $settings;
        $tenant->save();

        return redirect()->route('dashboard')->with('success', 'Votre espace DailyDesk est configuré ! Bienvenue 🎉');
    }

    private function getCurrentStep(Tenant $tenant): int
    {
        $settings = $tenant->settings ?? [];

        if (isset($settings['onboarding_completed']) && $settings['onboarding_completed']) {
            return 5;
        }

        return $settings['onboarding_step'] ?? 1;
    }

    private function setOnboardingStep(Tenant $tenant, int $step): void
    {
        $settings = $tenant->settings ?? [];
        $settings['onboarding_step'] = $step + 1;
        $tenant->settings = $settings;
    }
}
