<?php

namespace App\Listeners;

use App\Console\Commands\SeedCantineDishes;
use App\Modules\Cantine\Models\CantineDish;
use App\Models\User;
use App\Services\PublicSiteProvisioner;
use Stancl\Tenancy\Events\TenantCreated;

/**
 * Peuple chaque nouveau tenant avec ses données par défaut :
 * - le catalogue de plats cantine (46 plats)
 * - les pages du site public si le module est activé (accueil, démarches,
 *   conseil municipal, mentions légales...)
 */
class SeedNewTenantDefaults
{
    public function handle(TenantCreated $event): void
    {
        $tenant = $event->tenant;

        // Conserve le contexte tenancy éventuel (ex: création depuis un contexte tenant)
        $wasInitialized = tenancy()->initialized;
        $previousTenant = tenancy()->tenant ?? null;

        try {
            tenancy()->initialize($tenant);

            $this->seedDishes($tenant);
            $this->provisionPublicSite($tenant);
        } finally {
            if ($wasInitialized && $previousTenant) {
                tenancy()->initialize($previousTenant);
            } else {
                tenancy()->end();
            }
        }
    }

    private function seedDishes($tenant): void
    {
        // L'admin du tenant n'existe pas encore à la création : repli sur
        // le super admin central pour le champ created_by (NOT NULL).
        // NB: sans le scope tenant, sinon whereNull(tenant_id) est contredit
        // par le scope global du contexte tenancy initialisé.
        $createdBy = User::where('tenant_id', $tenant->id)->first()?->id
            ?? User::withoutGlobalScope('tenant')
                ->whereNull('tenant_id')
                ->whereHas('roles', fn($q) => $q->where('name', 'super_admin'))
                ->first()?->id;

        if (!$createdBy) {
            return; // aucun utilisateur : les plats seront chargés par cantine:seed-dishes
        }

        foreach ((new SeedCantineDishes())->getDishes() as $dish) {
            CantineDish::firstOrCreate(
                ['name' => $dish['name'], 'category' => $dish['category']],
                [
                    'allergens' => $dish['allergens'] ?? null,
                    'vegetarian' => $dish['vegetarian'] ?? false,
                    'notes' => $dish['notes'] ?? null,
                    'created_by' => $createdBy,
                ]
            );
        }
    }

    private function provisionPublicSite($tenant): void
    {
        if (in_array('public_site', $tenant->modules_enabled ?? [])) {
            app(PublicSiteProvisioner::class)->provisionDefaultPages($tenant);
        }
    }
}
