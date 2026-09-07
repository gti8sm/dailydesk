<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

$dbName = 'tenant_12641466_db3d_47dc_9b81_adfdb83870ba';

echo "🌱 Seeding manuel de la base tenant: $dbName\n\n";

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
    // Créer l'admin
    $adminId = DB::connection('tenant_manual')->table('users')->insertGetId([
        'name' => 'Admin Beauville',
        'email' => 'admin@beauville.fr',
        'password' => Hash::make('password'),
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "✓ Admin créé : admin@beauville.fr (ID: $adminId)\n";
    
    // Créer les rôles
    $adminRoleId = DB::connection('tenant_manual')->table('roles')->insertGetId([
        'name' => 'admin',
        'guard_name' => 'web',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "✓ Rôle admin créé (ID: $adminRoleId)\n";
    
    // Assigner le rôle
    DB::connection('tenant_manual')->table('model_has_roles')->insert([
        'role_id' => $adminRoleId,
        'model_type' => 'App\Models\User',
        'model_id' => $adminId,
    ]);
    echo "✓ Rôle assigné à l'admin\n\n";
    
    // Créer des familles
    $family1Id = DB::connection('tenant_manual')->table('families')->insertGetId([
        'family_name' => 'Famille Dupont',
        'address' => '10 Rue de la Paix',
        'postal_code' => '75001',
        'city' => 'Beauville',
        'phone' => '01 23 45 67 89',
        'email' => 'dupont@example.com',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "✓ Famille Dupont créée (ID: $family1Id)\n";
    
    // Enfants famille 1
    DB::connection('tenant_manual')->table('children')->insert([
        [
            'family_id' => $family1Id,
            'first_name' => 'Lucas',
            'last_name' => 'Dupont',
            'birth_date' => '2018-05-15',
            'gender' => 'M',
            'class' => 'CP',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'family_id' => $family1Id,
            'first_name' => 'Emma',
            'last_name' => 'Dupont',
            'birth_date' => '2020-03-20',
            'gender' => 'F',
            'class' => 'Maternelle',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);
    echo "✓ 2 enfants Dupont créés\n";
    
    $family2Id = DB::connection('tenant_manual')->table('families')->insertGetId([
        'family_name' => 'Famille Martin',
        'address' => '25 Avenue des Fleurs',
        'postal_code' => '75002',
        'city' => 'Beauville',
        'phone' => '01 98 76 54 32',
        'email' => 'martin@example.com',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "✓ Famille Martin créée (ID: $family2Id)\n";
    
    // Enfant famille 2
    DB::connection('tenant_manual')->table('children')->insert([
        'family_id' => $family2Id,
        'first_name' => 'Léa',
        'last_name' => 'Martin',
        'birth_date' => '2019-08-10',
        'gender' => 'F',
        'class' => 'CE1',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "✓ 1 enfant Martin créé\n\n";
    
    // Statistiques
    $familyCount = DB::connection('tenant_manual')->table('families')->count();
    $childCount = DB::connection('tenant_manual')->table('children')->count();
    $userCount = DB::connection('tenant_manual')->table('users')->count();
    
    echo "📊 Statistiques:\n";
    echo "  - Utilisateurs: $userCount\n";
    echo "  - Familles: $familyCount\n";
    echo "  - Enfants: $childCount\n";
    
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🎉 Seeding manuel terminé !\n";
echo "\n🔑 Connexion:\n";
echo "  URL: http://beauville.localhost:8001\n";
echo "  Email: admin@beauville.fr\n";
echo "  Password: password\n";
