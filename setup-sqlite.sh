#!/bin/bash

echo "🔧 Configuration Rapide avec SQLite"
echo "===================================="
echo ""

# Créer le fichier SQLite
echo "📁 Création du fichier database.sqlite..."
touch database/database.sqlite
echo "✓ Fichier créé"

# Modifier .env pour utiliser SQLite
echo ""
echo "⚙️  Configuration de .env pour SQLite..."
if [ -f .env ]; then
    # Backup
    cp .env .env.backup
    
    # Modifier DB_CONNECTION
    sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/' .env
    
    # Commenter les autres lignes DB
    sed -i 's/^DB_HOST=/#DB_HOST=/' .env
    sed -i 's/^DB_PORT=/#DB_PORT=/' .env
    sed -i 's/^DB_DATABASE=dailydesk/#DB_DATABASE=dailydesk/' .env
    sed -i 's/^DB_USERNAME=/#DB_USERNAME=/' .env
    sed -i 's/^DB_PASSWORD=/#DB_PASSWORD=/' .env
    
    echo "✓ .env configuré (backup dans .env.backup)"
else
    echo "✗ Fichier .env non trouvé"
    exit 1
fi

# Nettoyer le cache
echo ""
echo "🧹 Nettoyage du cache..."
php artisan config:clear > /dev/null 2>&1
php artisan cache:clear > /dev/null 2>&1
echo "✓ Cache nettoyé"

# Lancer les migrations
echo ""
echo "🗄️  Création des tables..."
php artisan migrate:fresh --force

# Créer les rôles
echo ""
echo "👥 Création des rôles et permissions..."
php artisan db:seed --class=RolesAndPermissionsSeeder --force

# Créer un admin
echo ""
echo "👤 Création de l'utilisateur admin..."
php artisan tinker --execute="
\$user = \App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@test.fr',
    'password' => bcrypt('password'),
    'confidential_code' => '1234',
    'is_active' => true,
]);
\$user->assignRole('super_admin');
echo 'Admin créé';
"

# Créer des données de test
echo ""
echo "📊 Création de données de test..."
php artisan tinker --execute="
\$f = \App\Models\Family::create(['family_name' => 'Famille Test', 'city' => 'Paris']);
\$c = \App\Models\Child::create(['family_id' => \$f->id, 'first_name' => 'Sophie', 'last_name' => 'Test', 'birth_date' => '2018-05-15', 'class' => 'CP']);
echo 'Données créées';
"

echo ""
echo "========================================="
echo "✅ Configuration terminée !"
echo "========================================="
echo ""
echo "🌐 Pour lancer l'application :"
echo "   php artisan serve"
echo ""
echo "🔑 Connexion :"
echo "   Email: admin@test.fr"
echo "   Password: password"
echo "   Code PIN: 1234"
echo ""
