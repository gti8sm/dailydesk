#!/bin/bash

# DailyDesk - Script de Test Rapide
# Ce script configure et teste l'application

echo "🚀 DailyDesk - Configuration et Test"
echo "===================================="
echo ""

# Couleurs
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Vérifier si .env existe
if [ ! -f .env ]; then
    echo -e "${BLUE}📝 Copie de .env.example vers .env...${NC}"
    cp .env.example .env
    echo -e "${GREEN}✓ Fichier .env créé${NC}"
else
    echo -e "${GREEN}✓ Fichier .env existe déjà${NC}"
fi

# Générer la clé d'application
echo -e "\n${BLUE}🔑 Génération de la clé d'application...${NC}"
php artisan key:generate
echo -e "${GREEN}✓ Clé générée${NC}"

# Créer les tables
echo -e "\n${BLUE}🗄️  Création des tables de base de données...${NC}"
php artisan migrate --force
echo -e "${GREEN}✓ Tables créées${NC}"

# Créer les rôles et permissions
echo -e "\n${BLUE}👥 Création des rôles et permissions...${NC}"
php artisan db:seed --class=RolesAndPermissionsSeeder --force
echo -e "${GREEN}✓ Rôles et permissions créés${NC}"

# Créer un utilisateur admin
echo -e "\n${BLUE}👤 Création d'un utilisateur admin...${NC}"
php artisan tinker --execute="
\$user = \App\Models\User::create([
    'name' => 'Admin Test',
    'email' => 'admin@test.fr',
    'password' => bcrypt('password'),
    'confidential_code' => '1234',
    'is_active' => true,
]);
\$user->assignRole('super_admin');
echo 'Utilisateur créé: admin@test.fr / password (PIN: 1234)';
"
echo -e "${GREEN}✓ Admin créé${NC}"

# Créer des données de test
echo -e "\n${BLUE}📊 Création de données de test...${NC}"
php artisan tinker --execute="
\$family1 = \App\Models\Family::create([
    'family_name' => 'Famille Dupont',
    'address' => '123 Rue de la Paix',
    'postal_code' => '75001',
    'city' => 'Paris',
    'phone' => '0123456789',
    'email' => 'dupont@example.com',
]);

\$child1 = \App\Models\Child::create([
    'family_id' => \$family1->id,
    'first_name' => 'Sophie',
    'last_name' => 'Dupont',
    'birth_date' => '2018-05-15',
    'gender' => 'F',
    'class' => 'CP',
    'allergies' => 'Arachides',
]);

\$family2 = \App\Models\Family::create([
    'family_name' => 'Famille Martin',
    'address' => '456 Avenue des Champs',
    'postal_code' => '75008',
    'city' => 'Paris',
    'phone' => '0987654321',
]);

\$child2 = \App\Models\Child::create([
    'family_id' => \$family2->id,
    'first_name' => 'Lucas',
    'last_name' => 'Martin',
    'birth_date' => '2019-03-20',
    'gender' => 'M',
    'class' => 'CE1',
]);

\$child3 = \App\Models\Child::create([
    'family_id' => \$family2->id,
    'first_name' => 'Emma',
    'last_name' => 'Martin',
    'birth_date' => '2020-07-10',
    'gender' => 'F',
    'class' => 'Maternelle',
    'dietary_restrictions' => 'Végétarien',
]);

echo '3 enfants créés dans 2 familles';
"
echo -e "${GREEN}✓ Données de test créées${NC}"

# Créer un utilisateur ALSH
echo -e "\n${BLUE}👤 Création d'un utilisateur ALSH...${NC}"
php artisan tinker --execute="
\$user = \App\Models\User::create([
    'name' => 'Personnel ALSH',
    'email' => 'alsh@test.fr',
    'password' => bcrypt('password'),
    'confidential_code' => '5678',
    'is_active' => true,
]);
\$user->assignRole('alsh');
echo 'Utilisateur ALSH créé: alsh@test.fr / password (PIN: 5678)';
"
echo -e "${GREEN}✓ Utilisateur ALSH créé${NC}"

# Créer un utilisateur Cantine
echo -e "\n${BLUE}👤 Création d'un utilisateur Cantine...${NC}"
php artisan tinker --execute="
\$user = \App\Models\User::create([
    'name' => 'Personnel Cantine',
    'email' => 'cantine@test.fr',
    'password' => bcrypt('password'),
    'confidential_code' => '9012',
    'is_active' => true,
]);
\$user->assignRole('cantine');
echo 'Utilisateur Cantine créé: cantine@test.fr / password (PIN: 9012)';
"
echo -e "${GREEN}✓ Utilisateur Cantine créé${NC}"

# Résumé
echo -e "\n${GREEN}========================================${NC}"
echo -e "${GREEN}✅ Configuration terminée avec succès !${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "${BLUE}📋 Comptes créés :${NC}"
echo ""
echo "  👤 Super Admin"
echo "     Email: admin@test.fr"
echo "     Password: password"
echo "     Code PIN: 1234"
echo ""
echo "  👤 Personnel ALSH"
echo "     Email: alsh@test.fr"
echo "     Password: password"
echo "     Code PIN: 5678"
echo ""
echo "  👤 Personnel Cantine"
echo "     Email: cantine@test.fr"
echo "     Password: password"
echo "     Code PIN: 9012"
echo ""
echo -e "${BLUE}📊 Données de test :${NC}"
echo "  - 2 familles (Dupont, Martin)"
echo "  - 3 enfants (Sophie, Lucas, Emma)"
echo ""
echo -e "${BLUE}🚀 Pour démarrer l'application :${NC}"
echo "  php artisan serve"
echo ""
echo -e "${BLUE}🌐 Puis ouvrir :${NC}"
echo "  http://localhost:8000"
echo ""
echo -e "${GREEN}Bon test ! 🎉${NC}"
