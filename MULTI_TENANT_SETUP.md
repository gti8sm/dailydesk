# 🏗️ Guide de Configuration Multi-Tenant

## 📋 Phase 1 : Configuration de base (60% complété)

### ✅ Ce qui a été fait

1. **Architecture multi-tenant configurée**
   - Modèle `Tenant` personnalisé avec toutes les métadonnées
   - Modèle `SubscriptionPlan` avec 3 plans (Starter, Pro, Premium)
   - Migrations séparées : central vs tenant
   - Configuration des connexions de base de données

2. **Fichiers créés/modifiés**
   - `app/Models/Tenant.php`
   - `app/Models/Central/SubscriptionPlan.php`
   - `database/migrations/2026_07_20_094636_add_custom_columns_to_tenants_table.php`
   - `database/migrations/2026_07_20_094727_create_subscription_plans_table.php`
   - `database/seeders/SubscriptionPlansSeeder.php`
   - `config/database.php` (modifié)
   - `config/tenancy.php` (modifié)

3. **Migrations déplacées vers `database/migrations/tenant/`**
   - Families, Parents, Children
   - Garderie & Cantine (presences, events)
   - Settings

---

## 🚀 Étapes pour continuer

### Étape 1 : Démarrer MySQL/MariaDB

```bash
# Vérifier le statut
sudo systemctl status mysql
# ou
sudo systemctl status mariadb

# Démarrer si nécessaire
sudo systemctl start mysql
# ou
sudo systemctl start mariadb
```

### Étape 2 : Créer la base de données centrale

**Option A : Via artisan**
```bash
php artisan db:create-central
```

**Option B : Manuellement**
```bash
mysql -u root -p
```
```sql
CREATE DATABASE dailydesk_central CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Étape 3 : Vérifier la configuration `.env`

Assurez-vous que votre fichier `.env` contient :

```env
DB_CONNECTION=central
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dailydesk_central
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe

TENANT_DB_PREFIX=tenant_
```

### Étape 4 : Exécuter les migrations centrales

```bash
php artisan migrate
```

Cela devrait créer les tables :
- `tenants` (avec colonnes personnalisées)
- `domains`
- `subscription_plans`
- `users`
- `permissions` & `roles`
- `password_resets`

### Étape 5 : Seeder les plans d'abonnement

```bash
php artisan db:seed --class=SubscriptionPlansSeeder
```

Cela créera 3 plans :
- **Starter** : 49€/mois, 50 enfants, garderie
- **Pro** : 99€/mois, 150 enfants, garderie + cantine  
- **Premium** : 199€/mois, illimité, tous modules

### Étape 6 : Vérifier que tout fonctionne

```bash
# Lister les migrations
php artisan migrate:status

# Vérifier les plans
php artisan tinker
>>> App\Models\Central\SubscriptionPlan::all();
```

---

## 🎯 Prochaines étapes (Phase 1 suite)

Une fois les étapes ci-dessus complétées, nous continuerons avec :

### 1. Créer le Job de provisioning (`CreateTenantJob`)
Ce job automatisera :
- Création du tenant
- Création de la base de données tenant
- Exécution des migrations tenant
- Création du domaine/sous-domaine
- Création de l'utilisateur admin initial
- Envoi de l'email de bienvenue

### 2. Créer le TenantController (central)
Interface d'administration pour :
- Créer un nouveau tenant
- Lister les tenants
- Modifier un tenant
- Activer/Suspendre un tenant

### 3. Configurer les routes
- Routes centrales (`admin.dailydesk.test`)
- Routes tenant (avec middleware)

### 4. Créer un tenant de test
```bash
php artisan tenant:create \
  --name="Mairie de Beauville" \
  --slug=beauville \
  --email=contact@beauville.fr \
  --plan=pro
```

### 5. Tester l'isolation
- Accéder à `beauville.dailydesk.test`
- Vérifier que les données sont isolées
- Tester la création de familles/enfants

---

## 📁 Architecture des bases de données

### Base centrale : `dailydesk_central`
Contient :
- `tenants` - Liste des mairies
- `domains` - Domaines/sous-domaines
- `subscription_plans` - Plans d'abonnement
- `users` - Super admins uniquement
- `permissions` & `roles` - Permissions globales
- `invoices` - Facturation (à venir)
- `activity_logs` - Logs globaux (à venir)

### Bases tenant : `tenant_{slug}`
Chaque mairie a sa propre base :
- `tenant_beauville`
- `tenant_valmont`
- etc.

Chaque base tenant contient :
- `families` - Familles
- `children` - Enfants
- `parents` - Parents
- `garderie_presences` - Présences garderie
- `garderie_events` - Événements garderie
- `cantine_presences` - Présences cantine
- `cantine_events` - Événements cantine
- `settings` - Paramètres de la mairie
- `users` - Utilisateurs de la mairie (admin, staff, parents)

---

## 🔐 Sécurité et isolation

### Isolation des données
- ✅ Chaque tenant a sa propre base de données
- ✅ Impossible d'accéder aux données d'un autre tenant
- ✅ Connexion automatique à la bonne DB selon le domaine

### Domaines
- **Central** : `admin.dailydesk.test` (super admin)
- **Tenants** : `{slug}.dailydesk.test` (mairies)
- **Personnalisés** : `garderie.beauville.fr` (optionnel)

---

## 🛠️ Commandes utiles

```bash
# Créer la DB centrale
php artisan db:create-central

# Migrations
php artisan migrate                    # Central
php artisan tenants:migrate           # Tous les tenants
php artisan tenants:migrate --tenants=beauville  # Un tenant spécifique

# Seeders
php artisan db:seed --class=SubscriptionPlansSeeder

# Cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Tinker (console interactive)
php artisan tinker
```

---

## 📊 Vérifications

### Vérifier la DB centrale
```bash
php artisan tinker
```
```php
// Lister les plans
App\Models\Central\SubscriptionPlan::all();

// Compter les tenants
App\Models\Tenant::count();
```

### Vérifier les migrations
```bash
php artisan migrate:status
```

---

## ❓ Troubleshooting

### Erreur "Connection refused"
- ✅ Vérifier que MySQL/MariaDB est démarré
- ✅ Vérifier les credentials dans `.env`
- ✅ Vérifier le port (3306 par défaut)

### Erreur "Database does not exist"
- ✅ Créer la DB : `php artisan db:create-central`
- ✅ Ou manuellement via MySQL

### Erreur "Access denied"
- ✅ Vérifier `DB_USERNAME` et `DB_PASSWORD` dans `.env`
- ✅ Vérifier les permissions MySQL

### Migrations déjà exécutées
```bash
php artisan migrate:fresh  # ⚠️ Supprime toutes les données !
# ou
php artisan migrate:rollback
php artisan migrate
```

---

## 📞 Prochaines étapes

Une fois que vous avez :
1. ✅ Démarré MySQL/MariaDB
2. ✅ Créé la DB centrale
3. ✅ Exécuté les migrations
4. ✅ Seedé les plans

**Dites-moi "continue" et je passerai à la création du système de provisioning !** 🚀

---

## 📝 Notes

- Les migrations tenant ne seront exécutées que lors de la création d'un tenant
- Chaque tenant peut avoir des paramètres différents (logo, couleurs, modules)
- Le système de facturation sera ajouté en Phase 3
- L'impersonation (se connecter en tant que tenant) sera ajouté en Phase 4
