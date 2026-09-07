# 🔧 DailyDesk - Guide de Dépannage

## Problème Actuel : Connexion Base de Données

### Erreur Affichée
```
SQLSTATE[HY000] [2002] Connection refused
```

### Cause
La base de données n'est pas configurée ou le serveur MySQL n'est pas démarré.

## Solutions Rapides

### Solution 1 : Utiliser SQLite (RECOMMANDÉ pour test)

SQLite est une base de données fichier, parfaite pour le développement.

#### Étape 1 : Installer l'extension PHP SQLite
```bash
sudo apt-get update
sudo apt-get install php8.3-sqlite3
```

#### Étape 2 : Configurer .env
```bash
# Éditer le fichier .env
nano .env
```

Modifier ces lignes :
```env
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=dailydesk
# DB_USERNAME=root
# DB_PASSWORD=
```

#### Étape 3 : Créer la base SQLite
```bash
touch database/database.sqlite
```

#### Étape 4 : Lancer les migrations
```bash
php artisan migrate:fresh --seed --seeder=RolesAndPermissionsSeeder --force
```

#### Étape 5 : Créer un utilisateur admin
```bash
php artisan tinker
```

Dans tinker :
```php
$user = \App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@test.fr',
    'password' => bcrypt('password'),
    'confidential_code' => '1234',
    'is_active' => true,
]);
$user->assignRole('super_admin');
exit
```

#### Étape 6 : Redémarrer le serveur
```bash
# Arrêter le serveur actuel (Ctrl+C dans le terminal)
php artisan serve
```

### Solution 2 : Utiliser MySQL

#### Étape 1 : Installer MySQL
```bash
sudo apt-get install mysql-server
```

#### Étape 2 : Démarrer MySQL
```bash
sudo systemctl start mysql
sudo systemctl enable mysql
```

#### Étape 3 : Créer la base de données
```bash
sudo mysql
```

Dans MySQL :
```sql
CREATE DATABASE dailydesk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'dailydesk_user'@'localhost' IDENTIFIED BY 'password123';
GRANT ALL PRIVILEGES ON dailydesk.* TO 'dailydesk_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### Étape 4 : Configurer .env
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dailydesk
DB_USERNAME=dailydesk_user
DB_PASSWORD=password123
```

#### Étape 5 : Lancer les migrations
```bash
php artisan migrate:fresh --seed --seeder=RolesAndPermissionsSeeder --force
```

## Vérifications Rapides

### 1. Vérifier la connexion DB
```bash
php artisan tinker
```
```php
DB::connection()->getPdo();
// Si ça fonctionne, vous verrez : PDO {#...}
exit
```

### 2. Vérifier les extensions PHP
```bash
php -m | grep -E 'pdo|sqlite|mysql'
```

Vous devriez voir :
- pdo_mysql (pour MySQL)
- pdo_sqlite (pour SQLite)

### 3. Vérifier le fichier .env
```bash
cat .env | grep DB_
```

### 4. Nettoyer le cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

## Erreurs Courantes

### "could not find driver"
**Cause** : Extension PDO manquante

**Solution** :
```bash
# Pour SQLite
sudo apt-get install php8.3-sqlite3

# Pour MySQL
sudo apt-get install php8.3-mysql

# Redémarrer PHP-FPM si nécessaire
sudo systemctl restart php8.3-fpm
```

### "Access denied for user"
**Cause** : Mauvais identifiants MySQL

**Solution** :
1. Vérifier les identifiants dans .env
2. Recréer l'utilisateur MySQL
3. Vérifier les permissions

### "Database does not exist"
**Cause** : Base de données non créée

**Solution** :
```bash
# Pour SQLite
touch database/database.sqlite

# Pour MySQL
mysql -u root -p
CREATE DATABASE dailydesk;
```

## Script de Diagnostic

Créez un fichier `diagnose.php` :

```php
<?php
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Extensions:\n";
foreach (['pdo', 'pdo_mysql', 'pdo_sqlite'] as $ext) {
    echo "  - $ext: " . (extension_loaded($ext) ? '✓' : '✗') . "\n";
}

// Test connexion
try {
    require __DIR__.'/vendor/autoload.php';
    $app = require_once __DIR__.'/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    DB::connection()->getPdo();
    echo "\n✓ Connexion DB OK\n";
} catch (Exception $e) {
    echo "\n✗ Erreur DB: " . $e->getMessage() . "\n";
}
```

Exécuter :
```bash
php diagnose.php
```

## Configuration Recommandée pour Développement

### .env Optimal (SQLite)
```env
APP_NAME=DailyDesk
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
# Commenter toutes les autres lignes DB_*

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

## Après Résolution

Une fois la base de données configurée :

1. **Créer les données de test**
```bash
./TEST_COMMANDS.sh
```

2. **Accéder à l'application**
```
http://localhost:8000
```

3. **Se connecter**
- Email: admin@test.fr
- Password: password
- Code PIN: 1234

## Support Supplémentaire

Si le problème persiste :

1. Vérifier les logs Laravel
```bash
tail -f storage/logs/laravel.log
```

2. Vérifier les permissions
```bash
chmod -R 775 storage bootstrap/cache
```

3. Réinstaller les dépendances
```bash
rm -rf vendor
composer install
```

---

**Besoin d'aide ?** Consultez README.md ou QUICK_START.md
