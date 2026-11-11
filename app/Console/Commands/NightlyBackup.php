<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NightlyBackup extends Command
{
    protected $signature = 'backup:nightly';
    protected $description = 'Sauvegarde complète automatique nocturne (base, uploads, code) avec rotation';

    public function handle(): int
    {
        [$ok, $details, $errors] = app(BackupService::class)->create('Sauvegarde automatique nocturne');

        if ($ok) {
            $this->info('Sauvegarde nocturne créée : ' . implode(' · ', $details));
            Log::info('Sauvegarde nocturne créée.', ['details' => $details]);

            return self::SUCCESS;
        }

        $this->error('Échec de la sauvegarde nocturne : ' . implode(' · ', $errors));
        Log::error('Échec de la sauvegarde nocturne.', ['errors' => $errors]);

        return self::FAILURE;
    }
}
