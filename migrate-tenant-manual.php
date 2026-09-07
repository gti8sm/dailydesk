<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

$dbName = 'tenant_12641466_db3d_47dc_9b81_adfdb83870ba';

echo "🔧 Migration manuelle de la base tenant: $dbName\n\n";

// Configurer la connexion
config(['database.connections.tenant_manual' => [
    'driver' => 'mysql',
    'host' => 'localhost',
    'port' => '3306',
    'database' => $dbName,
    'username' => 'dailydesk',
    'password' => 'password',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]]);

DB::purge('tenant_manual');

try {
    // Test de connexion
    DB::connection('tenant_manual')->getPdo();
    echo "✓ Connexion à la base de données réussie\n\n";
    
    // Créer les tables manuellement
    echo "📦 Création des tables...\n";
    
    // Table families
    if (!Schema::connection('tenant_manual')->hasTable('families')) {
        Schema::connection('tenant_manual')->create('families', function (Blueprint $table) {
            $table->id();
            $table->string('family_name');
            $table->string('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        echo "  ✓ Table families créée\n";
    }
    
    // Table children
    if (!Schema::connection('tenant_manual')->hasTable('children')) {
        Schema::connection('tenant_manual')->create('children', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('birth_date');
            $table->enum('gender', ['M', 'F']);
            $table->string('class')->nullable();
            $table->text('medical_notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        echo "  ✓ Table children créée\n";
    }
    
    // Table settings
    if (!Schema::connection('tenant_manual')->hasTable('settings')) {
        Schema::connection('tenant_manual')->create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string');
            $table->string('group')->default('general');
            $table->string('description')->nullable();
            $table->timestamps();
        });
        echo "  ✓ Table settings créée\n";
    }
    
    // Tables Garderie et Cantine (simplifiées pour le test)
    if (!Schema::connection('tenant_manual')->hasTable('garderie_presences')) {
        Schema::connection('tenant_manual')->create('garderie_presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('arrival_time')->nullable();
            $table->time('departure_time')->nullable();
            $table->enum('period', ['morning', 'evening']);
            $table->timestamps();
        });
        echo "  ✓ Table garderie_presences créée\n";
    }
    
    if (!Schema::connection('tenant_manual')->hasTable('cantine_presences')) {
        Schema::connection('tenant_manual')->create('cantine_presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->boolean('present')->default(false);
            $table->timestamps();
        });
        echo "  ✓ Table cantine_presences créée\n";
    }
    
    // Tables pour Spatie Permissions
    if (!Schema::connection('tenant_manual')->hasTable('permissions')) {
        Schema::connection('tenant_manual')->create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });
        echo "  ✓ Table permissions créée\n";
    }
    
    if (!Schema::connection('tenant_manual')->hasTable('roles')) {
        Schema::connection('tenant_manual')->create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });
        echo "  ✓ Table roles créée\n";
    }
    
    if (!Schema::connection('tenant_manual')->hasTable('model_has_roles')) {
        Schema::connection('tenant_manual')->create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->index(['model_id', 'model_type']);
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->primary(['role_id', 'model_id', 'model_type']);
        });
        echo "  ✓ Table model_has_roles créée\n";
    }
    
    // Table users
    if (!Schema::connection('tenant_manual')->hasTable('users')) {
        Schema::connection('tenant_manual')->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
        echo "  ✓ Table users créée\n";
    }
    
    echo "\n✅ Toutes les tables ont été créées avec succès !\n";
    
    // Afficher les tables
    $tables = DB::connection('tenant_manual')->select('SHOW TABLES');
    echo "\n📋 Tables dans la base de données:\n";
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "  - $tableName\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🎉 Migration manuelle terminée !\n";
