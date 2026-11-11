<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Sauvegarde complète automatique nocturne (base, uploads, code)
// Nécessite le cron serveur : * * * * * php artisan schedule:run
Schedule::command('backup:nightly')->daily()->at('02:30');
