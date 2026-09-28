<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Corrige la colonne tenant_id de public_site_pages : sur certaines bases,
     * la table préexistait aux migrations avec un tenant_id bigint alors que les
     * IDs de tenants sont des UUID (varchar). La création de pages échouait
     * avec "Out of range value for column 'tenant_id'".
     */
    public function up(): void
    {
        if (!Schema::hasTable('public_site_pages')) {
            return;
        }

        $column = DB::select("SHOW COLUMNS FROM public_site_pages WHERE Field = 'tenant_id'");

        if (empty($column) || str_starts_with($column[0]->Type, 'varchar')) {
            return; // déjà correct
        }

        // Défensif : retire toute FK existante sur tenant_id avant le changement de type
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'public_site_pages'
              AND COLUMN_NAME = 'tenant_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        foreach ($foreignKeys as $fk) {
            DB::statement("ALTER TABLE public_site_pages DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }

        DB::statement('ALTER TABLE public_site_pages MODIFY tenant_id VARCHAR(255) NOT NULL');

        // Retrouve la FK définie par les migrations
        Schema::table('public_site_pages', function ($table) {
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
        });

        // Retrouve l'index unique (tenant_id, slug) s'il manque
        $hasUnique = collect(DB::select('SHOW INDEX FROM public_site_pages'))
            ->contains(fn($index) => $index->Key_name === 'public_site_pages_tenant_id_slug_unique');

        if (!$hasUnique) {
            Schema::table('public_site_pages', function ($table) {
                $table->unique(['tenant_id', 'slug']);
            });
        }
    }

    public function down(): void
    {
        // Pas de retour arrière : le type bigint était buggy
    }
};
