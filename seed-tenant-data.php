<?php

use App\Models\User;
use App\Models\Family;
use App\Models\Child;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

// Créer l'admin
$admin = User::firstOrCreate(
    ['email' => 'admin@beauville.fr'],
    [
        'name' => 'Admin Beauville',
        'password' => Hash::make('password'),
        'is_active' => true,
    ]
);

// Créer les rôles
if (!Role::where('name', 'admin')->exists()) {
    Role::create(['name' => 'admin']);
}
if (!Role::where('name', 'alsh')->exists()) {
    Role::create(['name' => 'alsh']);
}
if (!Role::where('name', 'cantine')->exists()) {
    Role::create(['name' => 'cantine']);
}

if (!$admin->hasRole('admin')) {
    $admin->assignRole('admin');
}

echo "✓ Admin créé : {$admin->email}\n";

// Créer des familles de test
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

echo "✓ Familles : " . Family::count() . "\n";
echo "✓ Enfants : " . Child::count() . "\n";
