# DailyDesk — Guide de développement

## Stack technique
- Laravel 11.54, PHP 8.3
- Multi-tenant path-based (`/{tenant}/...`), base centrale unique, scoping via trait `BelongsToTenant`
- Spatie Permission (rôles + permissions)
- Tailwind CSS (via CDN), Alpine.js, Font Awesome
- Déploiement : o2switch sous `/dailydesk/`, Git-based

## Commandes utiles

### Développement local
```bash
php artisan serve --host=127.0.0.1 --port=8000   # Serveur local
php artisan migrate                                 # Migrations
php artisan optimize:clear                          # Vider tous les caches
php artisan view:cache                              # Compiler les vues Blade
php artisan route:list                              # Lister les routes
php -l <file>                                       # Vérifier la syntaxe PHP
```

### Module cantine
```bash
php artisan cantine:seed-dishes                     # Pré-charger le catalogue de plats pour tous les tenants
```

### Tests et vérification
Avant de committer, vérifier :
1. `php -l` sur les fichiers PHP modifiés
2. `php artisan view:cache` (compile les Blade)
3. `php artisan route:list` (routes enregistrées)
4. `php artisan migrate` si nouvelles migrations

## Conventions de code

### Architecture
- Modules dans `app/Modules/{Module}/` (Controllers, Models)
- Vues dans `resources/views/{module}/`
- Migrations tenant dans `database/migrations/tenant/`
- Trait `BelongsToTenant` sur tous les modèles tenant-scopés

### Rôles et permissions (Spatie)
- `super_admin` — accès total (central)
- `admin` / `admin_mairie` — admin tenant complet
- `personnel_mairie` — lecture + saisie présences
- `enseignant` — création d'événements
- `alsh` — garderie
- `cantine` — cuisinier (cantine + menus)
- `parent` — portail parent (lecture)

Permissions définies dans `database/seeders/RolesAndPermissionsSeeder.php`.
Pour ajouter une permission en production sans réexécuter le seeder complet :
```php
Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'ma_permission']);
Spatie\Permission\Models\Role::whereName('admin')->first()->givePermissionTo('ma_permission');
```

### Layouts
- `layouts.app` — layout principal (contexte tenant, navigation complète)
- `layouts.guest` — layout simple pour pages hors-contexte tenant (invitations, etc.)

### Tenancy
- Les routes tenant sont sous `Route::prefix('{tenant}')->middleware(['tenancy.slug', 'auth'])`
- Les routes hors tenant (login, invitation, landing) sont en dehors de ce groupe
- Pour initialiser le contexte tenant manuellement : `tenancy()->initialize($tenant)`

## Déploiement en production (o2switch)

### Procédure standard
```bash
# 1. Sauvegarde
cp -r /path/to/dailydesk /path/to/dailydesk_backup_$(date +%Y%m%d)

# 2. Mise à jour du code
cd /path/to/dailydesk
git pull origin main

# 3. Dépendances
composer install --no-dev --optimize-autoloader

# 4. Migrations (additives uniquement, jamais migrate:fresh)
php artisan migrate

# 5. Seeders si nouvelles permissions
php artisan db:seed --class=RolesAndPermissionsSeeder
# ou créer les permissions manuellement (voir ci-dessus)

# 6. Caches
php artisan route:cache
php artisan view:cache
php artisan config:cache
php artisan event:cache
```

### Règles de sécurité
- **Jamais** `migrate:fresh` ou `migrate:rollback` en production
- **Jamais** de migrations destructives (drop column/table) sans backup
- Toujours tester en local avant de pousser
- Ne jamais committer de secrets (.env, mots de passe, tokens)

## Processus de release (tags GitHub)

### Convention de versionnement (SemVer)
- **vX.0.0** — changement majeur/cassant
- **v1.X.0** — nouvelle fonctionnalité
- **v1.1.X** — correction de bug

### Créer une release
1. S'assurer que tout est commité et poussé sur `main`
2. Créer un tag annoté avec changelog :
```bash
git tag -a vX.Y.Z -m "Description de la version

Nouveautés:
- ...

Corrections:
- ...

Déploiement:
- php artisan migrate
- ..."
```
3. Pousser le tag :
```bash
git push origin vX.Y.Z
```
4. La release apparaît sur https://github.com/gti8sm/dailydesk/releases

### Déployer une version spécifique en production
```bash
git fetch --tags
git checkout vX.Y.Z
composer install --no-dev
php artisan migrate
php artisan optimize:clear && php artisan route:cache && php artisan view:cache
```

Pour revenir à la dernière version stable :
```bash
git checkout main
git pull origin main
```

## Authentification Git
- Remote configuré en SSH : `git@github.com:gti8sm/dailydesk.git`
- Clé SSH : `~/.ssh/id_ed25519` (ed25519)
- Si le push échoue, vérifier la clé SSH sur GitHub (Settings → SSH keys)
