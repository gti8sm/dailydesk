<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class RgpdPurgePresences extends Command
{
    protected $signature = 'rgpd:purge-presences {--years=3 : Durée de conservation des présences en années}';

    protected $description = 'RGPD — purge les présences cantine/garderie au-delà de la durée de conservation';

    public function handle(): int
    {
        $years = max(1, (int) $this->option('years'));
        $cutoff = now()->subYears($years)->toDateString();

        if (!$this->confirm("Purger définitivement toutes les présences antérieures au {$cutoff} ({$years} ans de conservation) ?")) {
            return self::SUCCESS;
        }

        $totals = ['cantine' => 0, 'garderie' => 0];

        foreach (Tenant::all() as $tenant) {
            tenancy()->initialize($tenant);

            $cantine = \App\Modules\Cantine\Models\CantinePresence::where('date', '<', $cutoff)->count();
            $garderie = \App\Modules\Garderie\Models\GarderiePresence::where('date', '<', $cutoff)->count();

            if ($cantine > 0) {
                \App\Modules\Cantine\Models\CantinePresence::where('date', '<', $cutoff)->delete();
            }
            if ($garderie > 0) {
                \App\Modules\Garderie\Models\GarderiePresence::where('date', '<', $cutoff)->delete();
            }

            $totals['cantine'] += $cantine;
            $totals['garderie'] += $garderie;
        }

        $this->info("Présences purgées — cantine : {$totals['cantine']}, garderie : {$totals['garderie']} (antérieures au {$cutoff}).");

        return self::SUCCESS;
    }
}
