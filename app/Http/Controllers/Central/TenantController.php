<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Central\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

class TenantController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }
        
        $tenants = Tenant::with('domains')->latest()->get();
        return view('central.tenants.index', compact('tenants'));
    }

    public function create()
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }
        
        $plans = SubscriptionPlan::where('is_active', true)->get();
        return view('central.tenants.create', compact('plans'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tenants,slug|alpha_dash',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'subscription_plan' => 'required|in:starter,pro,premium',
            'trial_days' => 'nullable|integer|min:0|max:90',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255',
            'admin_login' => 'nullable|string|max:255|regex:/^[a-zA-Z0-9_-]+$/',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        try {
            // Créer le tenant
            $tenant = Tenant::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'city' => $validated['city'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'status' => 'active',
                'subscription_plan' => $validated['subscription_plan'],
                'subscription_starts_at' => now(),
                'subscription_expires_at' => now()->addYear(),
                'trial_ends_at' => (!empty($validated['trial_days']) && $validated['trial_days'] > 0) ? now()->addDays((int)$validated['trial_days']) : null,
                'primary_color' => '#3B82F6',
                'secondary_color' => '#6366F1',
                'modules_enabled' => $this->getModulesForPlan($validated['subscription_plan']),
                'max_children' => $this->getMaxChildrenForPlan($validated['subscription_plan']),
            ]);

            // Créer le domaine
            $domain = $validated['slug'] . '.localhost';
            $tenant->domains()->create(['domain' => $domain]);

            // En single-DB : pas de création de base, juste l'admin et les settings
            $this->createTenantAdmin($tenant, $validated);
            $this->seedTenantSettings($tenant);

            return redirect()
                ->route('dashboard')
                ->with('success', "Tenant '{$tenant->name}' créé avec succès ! Domaine : {$domain}");

        } catch (\Exception $e) {
            if (isset($tenant)) {
                $tenant->delete();
            }
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création du tenant : ' . $e->getMessage());
        }
    }

    public function show(Tenant $tenant)
    {
        $tenant->load('domains');
        return view('central.tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        $plans = SubscriptionPlan::where('is_active', true)->get();
        
        // Récupérer l'admin du tenant (single-DB: filtrer par tenant_id)
        $admin = \App\Models\User::where('tenant_id', $tenant->id)
            ->whereHas('roles', fn($q) => $q->where('name', 'admin'))
            ->first();
        
        return view('central.tenants.edit', compact('tenant', 'plans', 'admin'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'subscription_plan' => 'required|in:starter,pro,premium',
            'status' => 'required|in:active,suspended,cancelled',
            'admin_login' => 'nullable|string|max:255|regex:/^[a-zA-Z0-9_-]+$/',
        ]);

        // Si le plan change, mettre à jour les modules et max_children
        if ($validated['subscription_plan'] !== $tenant->subscription_plan) {
            $validated['modules_enabled'] = $this->getModulesForPlan($validated['subscription_plan']);
            $validated['max_children'] = $this->getMaxChildrenForPlan($validated['subscription_plan']);
        }

        $tenant->update($validated);

        // Mettre à jour le login de l'admin si fourni
        if ($request->has('admin_login')) {
            $admin = \App\Models\User::where('tenant_id', $tenant->id)
                ->whereHas('roles', fn($q) => $q->where('name', 'admin'))
                ->first();
            if ($admin) {
                $admin->update(['login' => $request->input('admin_login')]);
            }
        }

        return redirect()
            ->route('central.tenants.show', $tenant)
            ->with('success', 'Tenant mis à jour avec succès !');
    }

    public function toggleStatus(Tenant $tenant)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }

        try {
            $newStatus = $tenant->status === 'active' ? 'suspended' : 'active';
            $tenant->update(['status' => $newStatus]);

            $message = $newStatus === 'active' 
                ? "Tenant '{$tenant->name}' activé avec succès !" 
                : "Tenant '{$tenant->name}' suspendu avec succès !";

            return redirect()
                ->back()
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors du changement de statut : ' . $e->getMessage());
        }
    }

    public function destroy(Tenant $tenant)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }

        try {
            $tenantName = $tenant->name;
            
            // Single-DB: supprimer les données du tenant
            $tenantId = $tenant->id;
            \App\Models\User::where('tenant_id', $tenantId)->delete();
            \App\Models\Family::withoutGlobalScope('tenant')->where('tenant_id', $tenantId)->delete();
            \App\Models\Child::withoutGlobalScope('tenant')->where('tenant_id', $tenantId)->delete();
            \App\Models\ParentModel::withoutGlobalScope('tenant')->where('tenant_id', $tenantId)->delete();
            \App\Models\SchoolClass::withoutGlobalScope('tenant')->where('tenant_id', $tenantId)->delete();
            \App\Models\Setting::withoutGlobalScope('tenant')->where('tenant_id', $tenantId)->delete();
            \App\Models\FamilyInvitation::withoutGlobalScope('tenant')->where('tenant_id', $tenantId)->delete();
            \App\Modules\Garderie\Models\GarderiePresence::withoutGlobalScope('tenant')->where('tenant_id', $tenantId)->delete();
            \App\Modules\Garderie\Models\GarderieEvent::withoutGlobalScope('tenant')->where('tenant_id', $tenantId)->delete();
            \App\Modules\Cantine\Models\CantinePresence::withoutGlobalScope('tenant')->where('tenant_id', $tenantId)->delete();
            \App\Modules\Cantine\Models\CantineEvent::withoutGlobalScope('tenant')->where('tenant_id', $tenantId)->delete();

            // Supprimer le tenant (les domaines seront supprimés en cascade)
            $tenant->delete();

            return redirect()
                ->route('dashboard')
                ->with('success', "Tenant '{$tenantName}' supprimé avec succès !");

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    private function getModulesForPlan($plan)
    {
        return match($plan) {
            'starter' => ['garderie'],
            'pro' => ['garderie', 'cantine'],
            'premium' => ['garderie', 'cantine', 'communication'],
            default => ['garderie'],
        };
    }

    private function getMaxChildrenForPlan($plan)
    {
        return match($plan) {
            'starter' => 50,
            'pro' => 150,
            'premium' => 999999,
            default => 50,
        };
    }

    // In single-DB mode, tables are created via migrations, not per-tenant

    private function createTenantAdmin(Tenant $tenant, array $data)
    {
        // Single-DB: create user with tenant_id, assign admin role within tenant context
        $admin = \App\Models\User::create([
            'name' => $data['admin_name'],
            'email' => $data['admin_email'],
            'login' => $data['admin_login'] ?? null,
            'password' => Hash::make($data['admin_password']),
            'is_active' => true,
            'tenant_id' => $tenant->id,
        ]);

        // Initialize tenancy to assign roles within tenant context
        tenancy()->initialize($tenant);
        
        // Ensure roles and permissions exist
        $this->seedRolesAndPermissions();
        
        $admin->assignRole('admin');
        
        tenancy()->end();
    }

    private function seedRolesAndPermissions(): void
    {
        $permissions = [
            'view_garderie', 'manage_garderie',
            'view_cantine', 'manage_cantine',
            'manage_families', 'manage_children',
            'view_garderie_events', 'view_cantine_events',
            'manage_settings',
        ];

        foreach ($permissions as $perm) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($permissions);
        
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'alsh', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'cantine', 'guard_name' => 'web']);
    }

    private function seedTenantSettings(Tenant $tenant)
    {
        tenancy()->initialize($tenant);

        $settings = [
            ['key' => 'app_name', 'value' => $tenant->name, 'type' => 'string', 'group' => 'general'],
            ['key' => 'garderie_morning_start', 'value' => '07:00', 'type' => 'string', 'group' => 'garderie'],
            ['key' => 'garderie_morning_end', 'value' => '09:00', 'type' => 'string', 'group' => 'garderie'],
            ['key' => 'garderie_evening_start', 'value' => '16:30', 'type' => 'string', 'group' => 'garderie'],
            ['key' => 'garderie_evening_end', 'value' => '19:00', 'type' => 'string', 'group' => 'garderie'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::create($setting);
        }

        tenancy()->end();
    }

    // updateTenantAdminLogin removed — handled inline in update() method
}
