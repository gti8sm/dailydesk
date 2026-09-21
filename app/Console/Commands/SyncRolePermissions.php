<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class SyncRolePermissions extends Command
{
    protected $signature = 'permissions:sync';
    protected $description = 'Synchronise les permissions des rôles avec le seeder (sans recréer les données)';

    public function handle(): void
    {
        $mapping = [
            'admin' => [
                'view_dashboard', 'manage_users', 'manage_families', 'manage_children',
                'import_families', 'export_data', 'manage_settings',
                'view_garderie', 'record_garderie_presence', 'create_garderie_event', 'view_garderie_events',
                'view_cantine', 'record_cantine_presence', 'create_cantine_event', 'view_cantine_events',
                'manage_cantine_menus', 'view_cantine_menus',
                'view_own_children', 'view_own_events', 'manage_notifications',
                'manage_stock', 'view_stock', 'record_stock_movement',
                'notify_event_parents',
            ],
            'admin_mairie' => [
                'view_dashboard', 'manage_users', 'manage_families', 'manage_children',
                'import_families', 'export_data', 'manage_settings',
                'view_garderie', 'record_garderie_presence', 'create_garderie_event', 'view_garderie_events',
                'view_cantine', 'record_cantine_presence', 'create_cantine_event', 'view_cantine_events',
                'manage_cantine_menus', 'view_cantine_menus',
                'view_own_children', 'view_own_events', 'manage_notifications',
                'manage_stock', 'view_stock', 'record_stock_movement',
                'notify_event_parents',
            ],
            'alsh' => [
                'view_dashboard',
                'view_garderie',
                'record_garderie_presence',
                'create_garderie_event',
                'view_garderie_events',
                'view_cantine',
                'record_cantine_presence',
                'view_cantine_events',
            ],
            'cantine' => [
                'view_dashboard',
                'view_cantine',
                'manage_cantine_menus',
                'view_cantine_menus',
            ],
        ];

        foreach ($mapping as $roleName => $permissions) {
            $role = Role::where('name', $roleName)->first();
            if (!$role) {
                $this->warn("Rôle '{$roleName}' introuvable, ignoré.");
                continue;
            }

            $role->syncPermissions($permissions);
            $this->info("Rôle '{$roleName}' : " . count($permissions) . ' permission(s) appliquée(s).');
        }

        $this->info('Synchronisation terminée.');
    }
}
