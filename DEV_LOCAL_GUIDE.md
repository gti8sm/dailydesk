# 💻 Guide de Développement Local

## 🎯 Continuer le développement sur votre PC

Ce guide vous permet de continuer à développer en local avant de déployer sur cPanel.

---

## 🚀 Configuration initiale (une seule fois)

### 1. Démarrer MySQL/MariaDB

```bash
# Vérifier le statut
sudo systemctl status mysql

# Démarrer si nécessaire
sudo systemctl start mysql

# Activer au démarrage
sudo systemctl enable mysql
```

### 2. Créer la base de données centrale

```bash
cd /home/simon/Documents/Web/DailyDesk

# Option 1 : Via artisan
php artisan db:create-central

# Option 2 : Manuellement
mysql -u root -p
```

```sql
CREATE DATABASE dailydesk_central CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 3. Configurer le fichier .env

```bash
cp .env.example .env
```

Modifier `.env` :

```env
APP_NAME=DailyDesk
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=central
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dailydesk_central
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe

TENANT_DB_PREFIX=tenant_
CENTRAL_DOMAIN=admin.dailydesk.test
TENANT_DOMAIN_SUFFIX=.dailydesk.test
```

### 4. Générer la clé d'application

```bash
php artisan key:generate
```

### 5. Installer les dépendances

```bash
composer install
```

### 6. Exécuter les migrations

```bash
php artisan migrate
```

### 7. Seeder les plans d'abonnement

```bash
php artisan db:seed --class=SubscriptionPlansSeeder
```

### 8. Créer un super administrateur

```bash
php artisan tinker
```

```php
$user = App\Models\User::create([
    'name' => 'Super Admin',
    'email' => 'admin@test.fr',
    'password' => Hash::make('password'),
    'is_active' => true,
]);

// Créer le rôle si nécessaire
if (!Spatie\Permission\Models\Role::where('name', 'super_admin')->exists()) {
    Spatie\Permission\Models\Role::create(['name' => 'super_admin']);
}

$user->assignRole('super_admin');

echo "Super admin créé : " . $user->email;
exit;
```

### 9. Configurer les domaines locaux

Éditer `/etc/hosts` :

```bash
sudo nano /etc/hosts
```

Ajouter :

```
127.0.0.1 dailydesk.test
127.0.0.1 admin.dailydesk.test
127.0.0.1 beauville.dailydesk.test
127.0.0.1 valmont.dailydesk.test
```

---

## 🏃 Démarrer le serveur de développement

### Méthode 1 : Serveur Laravel (simple)

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Accès :
- Application : `http://localhost:8000`
- Admin : `http://admin.dailydesk.test:8000`

### Méthode 2 : Valet (macOS/Linux)

```bash
# Installer Valet
composer global require laravel/valet
valet install

# Dans le dossier du projet
cd /home/simon/Documents/Web/DailyDesk
valet link dailydesk
valet secure dailydesk
```

Accès :
- Application : `https://dailydesk.test`
- Admin : `https://admin.dailydesk.test`

---

## 🎨 Créer un tenant de test

### Via Tinker

```bash
php artisan tinker
```

```php
// Créer le tenant
$tenant = App\Models\Tenant::create([
    'name' => 'Mairie de Beauville',
    'slug' => 'beauville',
    'email' => 'contact@beauville.fr',
    'phone' => '01 23 45 67 89',
    'address' => '1 Place de la Mairie',
    'city' => 'Beauville',
    'postal_code' => '75001',
    'primary_color' => '#3B82F6',
    'secondary_color' => '#6366F1',
    'status' => 'active',
    'subscription_plan' => 'pro',
    'subscription_starts_at' => now(),
    'subscription_expires_at' => now()->addYear(),
    'trial_ends_at' => now()->addDays(30),
    'max_children' => 150,
    'modules_enabled' => ['garderie', 'cantine'],
]);

echo "Tenant créé : {$tenant->name}\n";

// Créer la base de données tenant
$tenant->createDatabase();
echo "Base de données créée : tenant_{$tenant->id}\n";

// Exécuter les migrations tenant
$tenant->run(function () {
    Artisan::call('migrate', [
        '--path' => 'database/migrations/tenant',
        '--database' => 'tenant',
        '--force' => true,
    ]);
});
echo "Migrations tenant exécutées\n";

// Créer le domaine
$tenant->domains()->create([
    'domain' => 'beauville.dailydesk.test',
]);
echo "Domaine créé : beauville.dailydesk.test\n";

// Créer un utilisateur admin pour ce tenant
$tenant->run(function () use ($tenant) {
    $admin = App\Models\User::create([
        'name' => 'Admin Beauville',
        'email' => 'admin@beauville.fr',
        'password' => Hash::make('password'),
        'is_active' => true,
    ]);
    
    // Créer le rôle admin si nécessaire
    if (!Spatie\Permission\Models\Role::where('name', 'admin')->exists()) {
        Spatie\Permission\Models\Role::create(['name' => 'admin']);
    }
    
    $admin->assignRole('admin');
    
    echo "Admin tenant créé : {$admin->email}\n";
});

// Seeder les settings par défaut
$tenant->run(function () {
    Artisan::call('db:seed', [
        '--class' => 'DefaultSettingsSeeder',
        '--force' => true,
    ]);
});
echo "Settings seedés\n";

echo "\n✓ Tenant complet créé !\n";
echo "URL : http://beauville.dailydesk.test:8000\n";
echo "Login : admin@beauville.fr / password\n";

exit;
```

---

## 🧪 Tester l'isolation des données

```bash
php artisan tinker
```

```php
// Récupérer le tenant
$tenant = App\Models\Tenant::where('slug', 'beauville')->first();

// Exécuter du code dans le contexte du tenant
$tenant->run(function () {
    // Créer une famille de test
    $family = App\Models\Family::create([
        'family_name' => 'Famille Dupont',
        'address' => '10 Rue de la Paix',
        'postal_code' => '75001',
        'city' => 'Beauville',
        'phone' => '01 23 45 67 89',
        'email' => 'dupont@example.com',
        'is_active' => true,
    ]);
    
    echo "Famille créée : {$family->family_name}\n";
    
    // Créer un enfant
    $child = App\Models\Child::create([
        'family_id' => $family->id,
        'first_name' => 'Lucas',
        'last_name' => 'Dupont',
        'birth_date' => '2018-05-15',
        'gender' => 'M',
        'class' => 'CP',
        'is_active' => true,
    ]);
    
    echo "Enfant créé : {$child->full_name}\n";
    
    // Vérifier le nombre total
    echo "Total familles : " . App\Models\Family::count() . "\n";
    echo "Total enfants : " . App\Models\Child::count() . "\n";
});

exit;
```

---

## 🔄 Workflow de développement quotidien

### 1. Démarrer la journée

```bash
cd /home/simon/Documents/Web/DailyDesk

# Mettre à jour les dépendances si nécessaire
composer install

# Vider les caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Démarrer le serveur
php artisan serve
```

### 2. Créer une nouvelle fonctionnalité

```bash
# Créer une migration
php artisan make:migration create_xxx_table

# Créer un modèle
php artisan make:model Xxx

# Créer un contrôleur
php artisan make:controller XxxController

# Créer une commande
php artisan make:command XxxCommand
```

### 3. Tester les modifications

```bash
# Exécuter les migrations
php artisan migrate

# Tester dans tinker
php artisan tinker

# Vérifier les routes
php artisan route:list
```

### 4. Fin de journée

```bash
# Commit Git
git add .
git commit -m "Description des modifications"
git push
```

---

## 📦 Préparer pour le déploiement

### 1. Vérifier que tout fonctionne

```bash
# Tests
php artisan test

# Vérifier les migrations
php artisan migrate:status

# Vérifier la configuration
php artisan config:show database
```

### 2. Créer l'archive pour cPanel

```bash
cd /home/simon/Documents/Web/

# Créer l'archive (exclure les fichiers inutiles)
tar -czf dailydesk.tar.gz \
  --exclude='DailyDesk/node_modules' \
  --exclude='DailyDesk/.git' \
  --exclude='DailyDesk/vendor' \
  --exclude='DailyDesk/storage/logs/*.log' \
  --exclude='DailyDesk/.env' \
  DailyDesk/

echo "Archive créée : dailydesk.tar.gz"
ls -lh dailydesk.tar.gz
```

### 3. Uploader sur cPanel

- Via FTP/SFTP
- Ou via l'interface cPanel File Manager

### 4. Exécuter l'installation

```bash
# Via SSH sur le serveur
cd /home/votre_user/
tar -xzf dailydesk.tar.gz -C public_html/
cd public_html/
bash install.sh
```

---

## 🛠️ Commandes utiles

### Base de données

```bash
# Créer la DB centrale
php artisan db:create-central

# Migrations
php artisan migrate                    # Central
php artisan migrate:fresh              # Reset tout (⚠️ perte de données)
php artisan migrate:status             # Statut

# Seeders
php artisan db:seed
php artisan db:seed --class=SubscriptionPlansSeeder
```

### Cache

```bash
php artisan config:clear    # Vider config
php artisan cache:clear     # Vider cache
php artisan view:clear      # Vider vues
php artisan route:clear     # Vider routes

# Optimiser pour production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Tenants

```bash
# Lister les tenants
php artisan tinker
>>> App\Models\Tenant::all()

# Migrer tous les tenants
php artisan tenants:migrate

# Migrer un tenant spécifique
php artisan tenants:migrate --tenants=beauville
```

### Debugging

```bash
# Console interactive
php artisan tinker

# Voir les routes
php artisan route:list

# Voir les événements
php artisan event:list

# Logs en temps réel
tail -f storage/logs/laravel.log
```

---

## 🐛 Debugging

### Activer le mode debug

Dans `.env` :

```env
APP_DEBUG=true
LOG_LEVEL=debug
```

### Utiliser dd() et dump()

```php
// Dans un contrôleur
dd($variable);  // Dump and die
dump($variable);  // Dump sans arrêter
```

### Utiliser Ray (optionnel)

```bash
composer require spatie/laravel-ray --dev
```

```php
ray($variable);
ray()->showQueries();
```

### Logs personnalisés

```php
use Illuminate\Support\Facades\Log;

Log::info('Message info', ['data' => $data]);
Log::error('Erreur', ['exception' => $e]);
```

---

## 📊 Vérifier l'état du projet

```bash
php artisan about
```

Affiche :
- Version PHP
- Version Laravel
- Environnement
- Base de données
- Cache
- etc.

---

## 🔐 Sécurité en développement

### Ne JAMAIS commiter

- `.env`
- `vendor/`
- `node_modules/`
- Fichiers de backup (`.sql`, `.tar.gz`)
- Logs (`*.log`)

### Utiliser des données de test

```bash
php artisan tinker
```

```php
// Créer des données de test
App\Models\Family::factory(10)->create();
App\Models\Child::factory(30)->create();
```

---

## 📚 Ressources

- **Laravel Docs** : https://laravel.com/docs
- **Tenancy Docs** : https://tenancyforlaravel.com/docs
- **Spatie Permissions** : https://spatie.be/docs/laravel-permission

---

## ✅ Checklist développement

- [ ] MySQL démarré
- [ ] Base de données centrale créée
- [ ] `.env` configuré
- [ ] Dépendances installées
- [ ] Migrations exécutées
- [ ] Plans d'abonnement créés
- [ ] Super admin créé
- [ ] Domaines locaux configurés (`/etc/hosts`)
- [ ] Serveur de développement démarré
- [ ] Tenant de test créé
- [ ] Données de test créées

---

**Bon développement ! 🚀**
