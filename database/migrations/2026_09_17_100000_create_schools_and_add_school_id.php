<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Créer la table schools
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('type')->default('primaire'); // maternelle, élémentaire, primaire, collège
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Ajouter school_id sur school_classes
        Schema::table('school_classes', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('id')->constrained('schools')->nullOnDelete();
        });

        // 3. Ajouter school_id sur children
        Schema::table('children', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('family_id')->constrained('schools')->nullOnDelete();
        });

        // 4. Ajouter school_id sur users (nullable = admin global / toutes écoles)
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('tenant_id')->constrained('schools')->nullOnDelete();
        });

        // 5. Ajouter school_id sur cantine_menus (nullable = menu global)
        Schema::table('cantine_menus', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('tenant_id')->constrained('schools')->nullOnDelete();
        });

        // 6. Créer une école par défaut et assigner les données existantes
        $schoolId = DB::table('schools')->insertGetId([
            'name' => 'École principale',
            'type' => 'primaire',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('school_classes')->whereNull('school_id')->update(['school_id' => $schoolId]);
        DB::table('children')->whereNull('school_id')->update(['school_id' => $schoolId]);
        DB::table('cantine_menus')->whereNull('school_id')->update(['school_id' => $schoolId]);
    }

    public function down(): void
    {
        Schema::table('cantine_menus', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });

        Schema::table('children', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });

        Schema::table('school_classes', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });

        Schema::dropIfExists('schools');
    }
};
