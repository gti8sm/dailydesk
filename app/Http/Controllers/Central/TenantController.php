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

            // Créer la base de données
            $dbName = 'tenant_' . str_replace('-', '_', $tenant->id);
            DB::connection('central')->statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // Créer les tables manuellement (car tenants:migrate ne fonctionne pas bien)
            $this->createTenantTables($tenant);

            // Créer l'admin du tenant
            $this->createTenantAdmin($tenant, $validated);

            // Seeder les settings par défaut
            $this->seedTenantSettings($tenant);

            return redirect()
                ->route('dashboard')
                ->with('success', "Tenant '{$tenant->name}' créé avec succès ! Domaine : {$domain}");

        } catch (\Exception $e) {
            // En cas d'erreur, supprimer le tenant si créé
            if (isset($tenant)) {
                $tenant->delete();
                if (isset($dbName)) {
                    DB::connection('central')->statement("DROP DATABASE IF EXISTS `{$dbName}`");
                }
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
        
        // Récupérer l'admin du tenant
        $dbName = 'tenant_' . str_replace('-', '_', $tenant->id);
        config(['database.connections.tenant_temp' => [
            'driver' => 'mysql',
            'host' => config('database.connections.central.host'),
            'port' => config('database.connections.central.port'),
            'database' => $dbName,
            'username' => config('database.connections.central.username'),
            'password' => config('database.connections.central.password'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ]]);
        
        DB::purge('tenant_temp');
        
        $admin = DB::connection('tenant_temp')
            ->table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'admin')
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->select('users.*')
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
            $this->updateTenantAdminLogin($tenant, $request->input('admin_login'));
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
            
            // Supprimer la base de données
            $dbName = 'tenant_' . str_replace('-', '_', $tenant->id);
            DB::connection('central')->statement("DROP DATABASE IF EXISTS `{$dbName}`");

            // Supprimer le tenant (les domaines seront supprimés en cascade)
            $tenant->delete();

            return redirect()
                ->route('dashboard')
                ->with('success', "Tenant '{$tenantName}' et sa base de données ont été supprimés avec succès !");

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

    private function createTenantTables(Tenant $tenant)
    {
        $dbName = 'tenant_' . str_replace('-', '_', $tenant->id);
        
        config(['database.connections.tenant_temp' => [
            'driver' => 'mysql',
            'host' => config('database.connections.central.host'),
            'port' => config('database.connections.central.port'),
            'database' => $dbName,
            'username' => config('database.connections.central.username'),
            'password' => config('database.connections.central.password'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ]]);

        DB::purge('tenant_temp');

        $connection = DB::connection('tenant_temp');

        // Table families
        $connection->statement("
            CREATE TABLE IF NOT EXISTS `families` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `family_name` varchar(255) NOT NULL,
                `address` varchar(255) DEFAULT NULL,
                `postal_code` varchar(255) DEFAULT NULL,
                `city` varchar(255) DEFAULT NULL,
                `phone` varchar(255) DEFAULT NULL,
                `email` varchar(255) DEFAULT NULL,
                `is_active` tinyint(1) NOT NULL DEFAULT '1',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                `deleted_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Table children
        $connection->statement("
            CREATE TABLE IF NOT EXISTS `children` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `family_id` bigint unsigned NOT NULL,
                `first_name` varchar(255) NOT NULL,
                `last_name` varchar(255) NOT NULL,
                `birth_date` date NOT NULL,
                `gender` enum('M','F') NOT NULL,
                `class` varchar(255) DEFAULT NULL,
                `medical_notes` text,
                `is_active` tinyint(1) NOT NULL DEFAULT '1',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                `deleted_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `children_family_id_foreign` (`family_id`),
                CONSTRAINT `children_family_id_foreign` FOREIGN KEY (`family_id`) REFERENCES `families` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Table settings
        $connection->statement("
            CREATE TABLE IF NOT EXISTS `settings` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `key` varchar(255) NOT NULL,
                `value` text,
                `type` varchar(255) NOT NULL DEFAULT 'string',
                `group` varchar(255) NOT NULL DEFAULT 'general',
                `description` varchar(255) DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `settings_key_unique` (`key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Table garderie_presences
        $connection->statement("
            CREATE TABLE IF NOT EXISTS `garderie_presences` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `child_id` bigint unsigned NOT NULL,
                `date` date NOT NULL,
                `arrival_time` time DEFAULT NULL,
                `departure_time` time DEFAULT NULL,
                `period` enum('morning','evening') NOT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `garderie_presences_child_id_foreign` (`child_id`),
                CONSTRAINT `garderie_presences_child_id_foreign` FOREIGN KEY (`child_id`) REFERENCES `children` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Table cantine_presences
        $connection->statement("
            CREATE TABLE IF NOT EXISTS `cantine_presences` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `child_id` bigint unsigned NOT NULL,
                `date` date NOT NULL,
                `present` tinyint(1) NOT NULL DEFAULT '0',
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `cantine_presences_child_id_foreign` (`child_id`),
                CONSTRAINT `cantine_presences_child_id_foreign` FOREIGN KEY (`child_id`) REFERENCES `children` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Table permissions
        $connection->statement("
            CREATE TABLE IF NOT EXISTS `permissions` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `guard_name` varchar(255) NOT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Table roles
        $connection->statement("
            CREATE TABLE IF NOT EXISTS `roles` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `guard_name` varchar(255) NOT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Table model_has_roles
        $connection->statement("
            CREATE TABLE IF NOT EXISTS `model_has_roles` (
                `role_id` bigint unsigned NOT NULL,
                `model_type` varchar(255) NOT NULL,
                `model_id` bigint unsigned NOT NULL,
                PRIMARY KEY (`role_id`,`model_id`,`model_type`),
                KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
                CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Table users
        $connection->statement("
            CREATE TABLE IF NOT EXISTS `users` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `password` varchar(255) NOT NULL,
                `is_active` tinyint(1) NOT NULL DEFAULT '1',
                `remember_token` varchar(100) DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                `deleted_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `users_email_unique` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private function createTenantAdmin(Tenant $tenant, array $data)
    {
        $dbName = 'tenant_' . str_replace('-', '_', $tenant->id);
        
        config(['database.connections.tenant_temp' => [
            'driver' => 'mysql',
            'host' => config('database.connections.central.host'),
            'port' => config('database.connections.central.port'),
            'database' => $dbName,
            'username' => config('database.connections.central.username'),
            'password' => config('database.connections.central.password'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ]]);

        DB::purge('tenant_temp');

        // Créer l'admin
        $adminId = DB::connection('tenant_temp')->table('users')->insertGetId([
            'name' => $data['admin_name'],
            'email' => $data['admin_email'],
            'login' => $data['admin_login'] ?? null,
            'password' => Hash::make($data['admin_password']),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Créer le rôle admin
        $adminRoleId = DB::connection('tenant_temp')->table('roles')->insertGetId([
            'name' => 'admin',
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assigner le rôle
        DB::connection('tenant_temp')->table('model_has_roles')->insert([
            'role_id' => $adminRoleId,
            'model_type' => 'App\Models\User',
            'model_id' => $adminId,
        ]);

        // Créer les permissions
        $permissions = [
            'view_garderie', 'manage_garderie',
            'view_cantine', 'manage_cantine',
            'manage_families', 'manage_children',
            'view_garderie_events', 'view_cantine_events',
            'manage_settings',
        ];

        foreach ($permissions as $permission) {
            DB::connection('tenant_temp')->table('permissions')->insert([
                'name' => $permission,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedTenantSettings(Tenant $tenant)
    {
        $dbName = 'tenant_' . str_replace('-', '_', $tenant->id);
        
        config(['database.connections.tenant_temp' => [
            'driver' => 'mysql',
            'host' => config('database.connections.central.host'),
            'port' => config('database.connections.central.port'),
            'database' => $dbName,
            'username' => config('database.connections.central.username'),
            'password' => config('database.connections.central.password'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ]]);

        DB::purge('tenant_temp');

        $settings = [
            ['key' => 'app_name', 'value' => $tenant->name, 'type' => 'string', 'group' => 'general'],
            ['key' => 'garderie_morning_start', 'value' => '07:00', 'type' => 'string', 'group' => 'garderie'],
            ['key' => 'garderie_morning_end', 'value' => '09:00', 'type' => 'string', 'group' => 'garderie'],
            ['key' => 'garderie_evening_start', 'value' => '16:30', 'type' => 'string', 'group' => 'garderie'],
            ['key' => 'garderie_evening_end', 'value' => '19:00', 'type' => 'string', 'group' => 'garderie'],
        ];

        foreach ($settings as $setting) {
            DB::connection('tenant_temp')->table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    private function updateTenantAdminLogin(Tenant $tenant, ?string $login)
    {
        $dbName = 'tenant_' . str_replace('-', '_', $tenant->id);
        
        config(['database.connections.tenant_temp' => [
            'driver' => 'mysql',
            'host' => config('database.connections.central.host'),
            'port' => config('database.connections.central.port'),
            'database' => $dbName,
            'username' => config('database.connections.central.username'),
            'password' => config('database.connections.central.password'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ]]);

        DB::purge('tenant_temp');

        // Récupérer l'ID de l'admin
        $adminId = DB::connection('tenant_temp')
            ->table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'admin')
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->value('users.id');

        if ($adminId) {
            DB::connection('tenant_temp')
                ->table('users')
                ->where('id', $adminId)
                ->update([
                    'login' => $login,
                    'updated_at' => now(),
                ]);
        }
    }
}
