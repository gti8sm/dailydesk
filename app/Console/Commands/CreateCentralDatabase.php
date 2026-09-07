<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateCentralDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create-central';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the central database if it does not exist';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $database = config('database.connections.central.database');
        $charset = config('database.connections.central.charset', 'utf8mb4');
        $collation = config('database.connections.central.collation', 'utf8mb4_unicode_ci');

        $this->info("Creating database: {$database}");

        try {
            // Connect without database
            $connection = config('database.connections.central');
            $connection['database'] = null;
            
            config(['database.connections.temp' => $connection]);
            
            DB::connection('temp')->statement(
                "CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET {$charset} COLLATE {$collation}"
            );

            $this->info("Database '{$database}' created successfully!");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to create database: " . $e->getMessage());
            return 1;
        }
    }
}
