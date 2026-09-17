<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('schools', 'tenant_id')) {
        // Ajouter tenant_id sur schools (la table existait sans cette colonne)
        Schema::table('schools', function (Blueprint $table) {
            $table->uuid('tenant_id')->nullable()->after('id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->index('tenant_id');
        });

        // Backfill : pour chaque école, récupérer le tenant_id depuis les enfants
        // ou les classes qui y sont rattachés.
        $schools = DB::table('schools')->whereNull('tenant_id')->get();

        foreach ($schools as $school) {
            $tenantId = DB::table('children')
                ->where('school_id', $school->id)
                ->whereNotNull('tenant_id')
                ->value('tenant_id');

            if (!$tenantId) {
                $tenantId = DB::table('school_classes')
                    ->where('school_id', $school->id)
                    ->whereNotNull('tenant_id')
                    ->value('tenant_id');
            }

            if (!$tenantId) {
                // Aucun enfant ni classe rattaché : assigner au premier tenant
                $tenantId = DB::table('tenants')->value('id');
            }

            if ($tenantId) {
                DB::table('schools')->where('id', $school->id)->update(['tenant_id' => $tenantId]);
            }
        }
        }
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropIndex(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
    }
};
