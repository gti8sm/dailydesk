<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\PublicSiteProvisioner;
use Illuminate\Console\Command;

class ProvisionPublicSite extends Command
{
    protected $signature = 'public-site:provision {tenant : Slug du tenant} {--force : Provisionner même si le module public_site n\'est pas activé}';

    protected $description = 'Crée les pages publiques par défaut (accueil, démarches, conseil municipal, mentions légales...) pour un tenant';

    public function handle(): int
    {
        $tenant = Tenant::where('slug', $this->argument('tenant'))->first();

        if (!$tenant) {
            $this->error("Tenant introuvable : '{$this->argument('tenant')}'");
            return 1;
        }

        if (!in_array('public_site', $tenant->modules_enabled ?? []) && !$this->option('force')) {
            $this->warn("Le module public_site n'est pas activé pour le tenant '{$tenant->slug}'.");
            $this->info('Utilisez --force pour provisionner quand même.');
            return 0;
        }

        tenancy()->initialize($tenant);

        $created = app(PublicSiteProvisioner::class)->provisionDefaultPages($tenant);

        if (empty($created)) {
            $this->info("Toutes les pages par défaut existent déjà pour '{$tenant->slug}'.");
        } else {
            $this->info('Pages créées pour ' . $tenant->slug . ' :');
            foreach ($created as $slug) {
                $this->line("  - {$slug}");
            }
        }

        return 0;
    }
}
