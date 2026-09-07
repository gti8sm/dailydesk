# Guide de Démarrage Rapide - DailyDesk

## 🚀 Démarrage en 5 Minutes

### 1. Vérifier les prérequis

```bash
php -v    # Doit être >= 8.2
mysql --version  # Doit être >= 8.0
```

### 2. Configuration de la base de données

Créer une base de données MySQL :

```sql
CREATE DATABASE dailydesk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'dailydesk_user'@'localhost' IDENTIFIED BY 'votre_mot_de_passe';
GRANT ALL PRIVILEGES ON dailydesk.* TO 'dailydesk_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3. Configuration de l'application

```bash
# Copier le fichier d'environnement
cp .env.example .env

# Éditer .env et configurer :
nano .env
```

Modifier ces lignes :
```env
DB_DATABASE=dailydesk
DB_USERNAME=dailydesk_user
DB_PASSWORD=votre_mot_de_passe

APP_URL=http://localhost:8000
CENTRAL_DOMAINS=localhost
```

### 4. Installation

```bash
# Générer la clé d'application
php artisan key:generate

# Créer les tables
php artisan migrate

# Créer les rôles et permissions
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### 5. Créer un utilisateur admin

```bash
php artisan tinker
```

Puis dans tinker :
```php
$user = \App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@dailydesk.fr',
    'password' => bcrypt('password'),
    'is_active' => true,
]);

$user->assignRole('super_admin');
exit
```

### 6. Lancer le serveur

```bash
php artisan serve
```

Accéder à : http://localhost:8000

## 🏢 Créer votre première mairie (Tenant)

### Via Tinker

```bash
php artisan tinker
```

```php
$tenant = \Stancl\Tenancy\Database\Models\Tenant::create([
    'id' => 'mairie-test',
]);

$tenant->domains()->create([
    'domain' => 'mairie-test.localhost',
]);

// Créer une licence
\App\Models\License::create([
    'tenant_id' => 'mairie-test',
    'license_type' => 'trial',
    'start_date' => now(),
    'end_date' => now()->addDays(30),
    'status' => 'active',
    'modules_enabled' => ['garderie', 'cantine'],
]);

exit
```

### Accéder au tenant

Ajouter dans `/etc/hosts` (Linux/Mac) ou `C:\Windows\System32\drivers\etc\hosts` (Windows) :
```
127.0.0.1 mairie-test.localhost
```

Accéder à : http://mairie-test.localhost:8000

## 👨‍👩‍👧‍👦 Créer des données de test

### Créer une famille

```bash
php artisan tinker
```

```php
$family = \App\Models\Family::create([
    'family_name' => 'Famille Dupont',
    'address' => '123 Rue de la Paix',
    'postal_code' => '75001',
    'city' => 'Paris',
    'phone' => '0123456789',
    'email' => 'dupont@example.com',
]);

// Créer un parent
$parent = \App\Models\ParentModel::create([
    'family_id' => $family->id,
    'first_name' => 'Jean',
    'last_name' => 'Dupont',
    'email' => 'jean.dupont@example.com',
    'phone' => '0123456789',
    'relationship' => 'father',
    'is_primary_contact' => true,
]);

// Créer un enfant
$child = \App\Models\Child::create([
    'family_id' => $family->id,
    'first_name' => 'Sophie',
    'last_name' => 'Dupont',
    'birth_date' => '2018-05-15',
    'gender' => 'F',
    'class' => 'CP',
]);

exit
```

## 📊 Tester les modules

### Enregistrer une présence garderie

```php
use App\Modules\Garderie\Models\GarderiePresence;

$presence = GarderiePresence::create([
    'child_id' => 1,  // ID de l'enfant
    'date' => today(),
    'arrival_time' => '08:30',
    'recorded_by_arrival' => 1,  // ID de l'utilisateur
]);

// Enregistrer le départ
$presence->update([
    'departure_time' => '17:00',
    'recorded_by_departure' => 1,
]);

$presence->calculateDuration();
```

### Enregistrer une présence cantine

```php
use App\Modules\Cantine\Models\CantinePresence;

$presence = CantinePresence::create([
    'child_id' => 1,
    'date' => today(),
    'meal_type' => 'lunch',
    'is_present' => true,
    'recorded_by' => 1,
]);
```

## 🔐 Rôles Disponibles

| Rôle | Description |
|------|-------------|
| `super_admin` | Accès complet au système |
| `admin_mairie` | Administration d'une mairie |
| `personnel_mairie` | Consultation des données |
| `enseignant` | Signalement d'événements |
| `alsh` | Gestion garderie |
| `cantine` | Gestion cantine |
| `parent` | Consultation enfants |

### Assigner un rôle

```php
$user = \App\Models\User::find(1);
$user->assignRole('admin_mairie');
```

## 🛠️ Commandes Utiles

```bash
# Vider tous les caches
php artisan optimize:clear

# Créer un contrôleur
php artisan make:controller NomController

# Créer un modèle avec migration
php artisan make:model NomModele -m

# Créer une migration
php artisan make:migration create_table_name

# Lancer les migrations
php artisan migrate

# Rollback dernière migration
php artisan migrate:rollback

# Rafraîchir la base (ATTENTION : supprime toutes les données)
php artisan migrate:fresh --seed
```

## 📝 Prochaines Étapes

1. **Créer les vues** : Développer les interfaces Blade
2. **Implémenter l'authentification** : Pages login/register
3. **Créer le dashboard** : Interface adaptée par rôle
4. **Développer les exports** : Classes Excel pour rapports
5. **Ajouter les notifications** : Système d'alertes email

## 🆘 Dépannage

### Erreur "Class not found"
```bash
composer dump-autoload
```

### Erreur de permissions
```bash
chmod -R 775 storage bootstrap/cache
```

### Erreur de migration
```bash
php artisan migrate:fresh
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### Le serveur ne démarre pas
```bash
# Vérifier si le port 8000 est occupé
lsof -i :8000

# Utiliser un autre port
php artisan serve --port=8080
```

## 📚 Ressources

- [Documentation Laravel](https://laravel.com/docs/11.x)
- [Stancl Tenancy](https://tenancyforlaravel.com/docs/v3)
- [Spatie Permission](https://spatie.be/docs/laravel-permission/v6)
- [Tailwind CSS](https://tailwindcss.com/docs)

## 💬 Support

Pour toute question, consultez :
- `README.md` : Documentation complète
- `IMPLEMENTATION_STATUS.md` : État d'avancement
- Plan de développement : `.windsurf/plans/`
