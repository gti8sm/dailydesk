<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ajouter insee_code et population sur les tenants
        Schema::connection('central')->table('tenants', function (Blueprint $table) {
            $table->string('insee_code', 5)->nullable()->after('postal_code');
            $table->integer('population')->nullable()->after('insee_code');
        });

        // Rendre max_children nullable sur tenants (null = illimité, nouveau modèle par population)
        Schema::connection('central')->table('tenants', function (Blueprint $table) {
            $table->integer('max_children')->nullable()->default(null)->change();
        });

        // Rendre modules nullable (tous les modules sont inclus par défaut maintenant)
        Schema::connection('central')->table('subscription_plans', function (Blueprint $table) {
            $table->json('modules')->nullable()->change();
        });

        // Ajouter population_min et population_max sur les plans
        Schema::connection('central')->table('subscription_plans', function (Blueprint $table) {
            $table->integer('population_min')->nullable()->after('max_children');
            $table->integer('population_max')->nullable()->after('population_min');
        });
    }

    public function down(): void
    {
        Schema::connection('central')->table('tenants', function (Blueprint $table) {
            $table->dropColumn(['insee_code', 'population']);
            $table->integer('max_children')->default(50)->nullable(false)->change();
        });

        Schema::connection('central')->table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn(['population_min', 'population_max']);
            $table->json('modules')->nullable(false)->change();
        });
    }
};
