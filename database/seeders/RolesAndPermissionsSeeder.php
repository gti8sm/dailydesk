<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view_dashboard',
            'manage_users',
            'manage_families',
            'manage_children',
            'import_families',
            'export_data',
            'manage_licenses',
            'manage_settings',
            'view_garderie',
            'record_garderie_presence',
            'create_garderie_event',
            'view_garderie_events',
            'view_cantine',
            'record_cantine_presence',
            'create_cantine_event',
            'view_cantine_events',
            'view_own_children',
            'view_own_events',
            'manage_notifications',
            'notify_event_parents',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $superAdmin = Role::create(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $adminMairie = Role::create(['name' => 'admin_mairie']);
        $adminMairie->givePermissionTo([
            'view_dashboard',
            'manage_users',
            'manage_families',
            'manage_children',
            'import_families',
            'export_data',
            'manage_settings',
            'view_garderie',
            'record_garderie_presence',
            'create_garderie_event',
            'view_garderie_events',
            'view_cantine',
            'record_cantine_presence',
            'create_cantine_event',
            'view_cantine_events',
            'manage_notifications',
            'notify_event_parents',
        ]);

        $personnelMairie = Role::create(['name' => 'personnel_mairie']);
        $personnelMairie->givePermissionTo([
            'view_dashboard',
            'view_garderie',
            'record_garderie_presence',
            'view_garderie_events',
            'view_cantine',
            'record_cantine_presence',
            'view_cantine_events',
        ]);

        $enseignant = Role::create(['name' => 'enseignant']);
        $enseignant->givePermissionTo([
            'view_dashboard',
            'view_garderie',
            'create_garderie_event',
            'view_garderie_events',
            'view_cantine',
            'create_cantine_event',
            'view_cantine_events',
        ]);

        $alsh = Role::create(['name' => 'alsh']);
        $alsh->givePermissionTo([
            'view_dashboard',
            'view_garderie',
            'record_garderie_presence',
            'create_garderie_event',
            'view_garderie_events',
        ]);

        $cantine = Role::create(['name' => 'cantine']);
        $cantine->givePermissionTo([
            'view_dashboard',
            'view_cantine',
            'record_cantine_presence',
            'create_cantine_event',
            'view_cantine_events',
        ]);

        $parent = Role::create(['name' => 'parent']);
        $parent->givePermissionTo([
            'view_dashboard',
            'view_own_children',
            'view_own_events',
            'manage_notifications',
        ]);

        $superAdminUser = User::create([
            'name' => 'Super Admin',
            'email' => 'simonmaraval@smallwebconcept.fr',
            'password' => 'Occupy-Shoplift9-Exposable',
            'is_active' => true,
            'tenant_id' => null,
        ]);
        $superAdminUser->assignRole('super_admin');
    }
}
