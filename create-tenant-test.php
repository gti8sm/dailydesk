<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tenant;
use App\Models\User;
use App\Models\Family;
use App\Models\Child;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

echo "🚀 Création du tenant de test...\n\n";

// Récupérer le tenant
$tenant = Tenant::where('slug', 'beauville')->first();

if (!$tenant) {
    echo "❌ Tenant 'beauville' non trouvé\n";
    exit(1);
}

echo "✓ Tenant trouvé : {$tenant->name}\n";

// Créer la base de données manuellement
$dbName = 'tenant_' . $tenant->id;
try {
    DB::connection('central')->statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Base de données créée : {$dbName}\n";
} catch (\Exception $e) {
    echo "⚠ Base de données existe déjà ou erreur : " . $e->getMessage() . "\n";
}

// Créer le domaine
if ($tenant->domains()->count() === 0) {
    $tenant->domains()->create([
        'domain' => 'beauville.localhost',
    ]);
    echo "✓ Domaine créé : beauville.localhost\n";
} else {
    echo "✓ Domaine existe déjà\n";
}

// Exécuter les migrations dans le contexte du tenant
echo "\n📦 Exécution des migrations tenant...\n";
tenancy()->initialize($tenant);
Artisan::call('migrate', [
    '--path' => 'database/migrations/tenant',
    '--database' => 'tenant',
    '--force' => true,
]);
echo Artisan::output();

// Créer un admin pour ce tenant
echo "\n👤 Création de l'administrateur...\n";
$admin = User::firstOrCreate(
    ['email' => 'admin@beauville.fr'],
    [
        'name' => 'Admin Beauville',
        'password' => Hash::make('password'),
        'is_active' => true,
    ]
);

if (!Spatie\Permission\Models\Role::where('name', 'admin')->exists()) {
    Spatie\Permission\Models\Role::create(['name' => 'admin']);
}

if (!$admin->hasRole('admin')) {
    $admin->assignRole('admin');
}

echo "✓ Admin créé : {$admin->email}\n";

// Seeder les settings
echo "\n⚙️  Seeding des settings...\n";
Artisan::call('db:seed', [
    '--class' => 'DefaultSettingsSeeder',
    '--force' => true,
]);
echo "✓ Settings créés\n";

// Créer des données de test
echo "\n📊 Création des données de test...\n";
    // Famille 1
    $family1 = Family::firstOrCreate(
        ['email' => 'dupont@example.com'],
        [
            'family_name' => 'Famille Dupont',
            'address' => '10 Rue de la Paix',
            'postal_code' => '75001',
            'city' => 'Beauville',
            'phone' => '01 23 45 67 89',
            'is_active' => true,
        ]
    );
    
    Child::firstOrCreate(
        ['family_id' => $family1->id, 'first_name' => 'Lucas', 'last_name' => 'Dupont'],
        [
            'birth_date' => '2018-05-15',
            'gender' => 'M',
            'class' => 'CP',
            'is_active' => true,
        ]
    );
    
    Child::firstOrCreate(
        ['family_id' => $family1->id, 'first_name' => 'Emma', 'last_name' => 'Dupont'],
        [
            'birth_date' => '2020-03-20',
            'gender' => 'F',
            'class' => 'Maternelle',
            'is_active' => true,
        ]
    );
    
    // Famille 2
    $family2 = Family::firstOrCreate(
        ['email' => 'martin@example.com'],
        [
            'family_name' => 'Famille Martin',
            'address' => '25 Avenue des Fleurs',
            'postal_code' => '75002',
            'city' => 'Beauville',
            'phone' => '01 98 76 54 32',
            'is_active' => true,
        ]
    );
    
    Child::firstOrCreate(
        ['family_id' => $family2->id, 'first_name' => 'Léa', 'last_name' => 'Martin'],
        [
            'birth_date' => '2019-08-10',
            'gender' => 'F',
            'class' => 'CE1',
            'is_active' => true,
        ]
    );
    
$familyCount = Family::count();
$childCount = Child::count();

echo "✓ {$familyCount} familles et {$childCount} enfants créés\n";

echo "\n";
echo "╔═══════════════════════════════════════════════════════════╗\n";
echo "║                                                           ║\n";
echo "║         🎉 Tenant créé avec succès !                     ║\n";
echo "║                                                           ║\n";
echo "╚═══════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "📋 Informations du tenant :\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Nom :           {$tenant->name}\n";
echo "Slug :          {$tenant->slug}\n";
echo "Email :         {$tenant->email}\n";
echo "Plan :          {$tenant->subscription_plan}\n";
echo "Statut :        {$tenant->status}\n";
echo "Base de données : {$dbName}\n";
echo "Domaine :       beauville.localhost:8001\n";
echo "\n";
echo "🔑 Connexion admin :\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "URL :           http://beauville.localhost:8001\n";
echo "Email :         admin@beauville.fr\n";
echo "Mot de passe :  password\n";
echo "\n";
echo "⚠️  Note : Ajoutez '127.0.0.1 beauville.localhost' dans /etc/hosts\n";
echo "\n";
