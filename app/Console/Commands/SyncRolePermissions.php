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
