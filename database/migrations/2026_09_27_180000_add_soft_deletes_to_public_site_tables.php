<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('public_site_blocks') && !Schema::hasColumn('public_site_blocks', 'deleted_at')) {
            Schema::table('public_site_blocks', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }

        if (Schema::hasTable('public_site_events') && !Schema::hasColumn('public_site_events', 'deleted_at')) {
            Schema::table('public_site_events', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('public_site_blocks') && Schema::hasColumn('public_site_blocks', 'deleted_at')) {
            Schema::table('public_site_blocks', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasTable('public_site_events') && Schema::hasColumn('public_site_events', 'deleted_at')) {
            Schema::table('public_site_events', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
