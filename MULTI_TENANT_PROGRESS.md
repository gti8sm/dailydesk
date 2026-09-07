# 🚀 Progression Multi-Tenant - Phase 1

## ✅ Étapes complétées

### 1. Migrations et Modèles
- ✅ Migration `add_custom_columns_to_tenants_table` créée
  - Ajout de toutes les colonnes personnalisées (name, slug, email, status, subscription_plan, etc.)
- ✅ Migration `create_subscription_plans_table` créée
- ✅ Modèle `Tenant` personnalisé créé (`app/Models/Tenant.php`)
  - Méthodes : `isActive()`, `isExpired()`, `isInTrial()`, `hasModule()`, `canAddChild()`, etc.
- ✅ Modèle `SubscriptionPlan` créé (`app/Models/Central/SubscriptionPlan.php`)
- ✅ Seeder `SubscriptionPlansSeeder` créé avec 3 plans :
  - **Starter** : 49€/mois, 50 enfants, garderie
  - **Pro** : 99€/mois, 150 enfants, garderie + cantine
  - **Premium** : 199€/mois, illimité, tous modules

### 2. Configuration
- ✅ `config/database.php` modifié :
  - Connexion `central` ajoutée (DB: dailydesk_central)
  - Connexion `tenant` ajoutée (template pour tenants)
  - Default connection = `central`
- ✅ `config/tenancy.php` configuré :
  - `tenant_model` = `\App\Models\Tenant::class`
  - `template_tenant_connection` = `tenant`
  - `prefix` = `tenant_`
  - `central_domains` = `['127.0.0.1', 'localhost', 'admin.dailydesk.test']`

### 3. Séparation des migrations
- ✅ Dossier `database/migrations/tenant/` créé
- ✅ Migrations déplacées vers `tenant/` :
  - `create_families_table.php`
  - `create_parents_table.php`
  - `create_children_table.php`
  - `create_garderie_presences_table.php`
  - `create_garderie_events_table.php`
  - `create_cantine_presences_table.php`
  - `create_cantine_events_table.php`
  - `create_settings_table.php`

### 4. Migrations centrales exécutées
- ✅ `add_custom_columns_to_tenants_table` : DONE
- ✅ `create_subscription_plans_table` : DONE

### 5. Commandes
- ✅ Commande `db:create-central` créée pour créer la DB centrale

---

## ⚠️ Prérequis à compléter

### Base de données MySQL/MariaDB
**IMPORTANT** : Vous devez démarrer MySQL/MariaDB avant de continuer.

```bash
# Démarrer MySQL/MariaDB (selon votre système)
sudo systemctl start mysql
# ou
sudo systemctl start mariadb
```

Ensuite, créer la base de données centrale :
```bash
# Option 1 : Via artisan
php artisan db:create-central

# Option 2 : Manuellement
mysql -u root -p
CREATE DATABASE dailydesk_central CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## 📋 Prochaines étapes

### Étape 10 : Seeder les plans d'abonnement
```bash
php artisan db:seed --class=SubscriptionPlansSeeder
```

### Étape 11 : Créer le Job de provisioning
- [ ] `app/Jobs/CreateTenantJob.php`
  - Créer tenant
  - Créer base de données
  - Exécuter migrations tenant
  - Créer domaine
  - Créer admin initial
  - Envoyer email

### Étape 12 : Créer le TenantController (central)
- [ ] `app/Http/Controllers/Central/TenantController.php`
  - CRUD complet
  - Dispatch CreateTenantJob

### Étape 13 : Routes centrales
- [ ] `routes/central.php`
  - Dashboard super admin
  - Gestion tenants

### Étape 14 : Middleware tenancy
- [ ] Configurer `routes/web.php` avec middleware
- [ ] Tester isolation des données

### Étape 15 : Commandes artisan
- [ ] `tenant:create` - Créer un tenant via CLI
- [ ] `tenant:list` - Lister les tenants
- [ ] `tenant:migrate` - Migrer un tenant spécifique

### Étape 16 : Créer un tenant de test
```bash
php artisan tenant:create \
  --name="Mairie de Beauville" \
  --slug=beauville \
  --email=contact@beauville.fr \
  --plan=pro
```

### Étape 17 : Configuration DNS locale
Ajouter dans `/etc/hosts` :
```
127.0.0.1 admin.dailydesk.test
127.0.0.1 beauville.dailydesk.test
```

### Étape 18 : Tests
- [ ] Test création tenant
- [ ] Test isolation données
- [ ] Test accès via sous-domaine

---

## 📁 Structure actuelle

```
app/
├── Models/
│   ├── Tenant.php ✅
│   └── Central/
│       └── SubscriptionPlan.php ✅
├── Console/Commands/
│   └── CreateCentralDatabase.php ✅
└── Jobs/
    └── CreateTenantJob.php ⏳

config/
├── database.php ✅ (modifié)
└── tenancy.php ✅ (modifié)

database/
├── migrations/
│   ├── 2019_09_15_000010_create_tenants_table.php ✅
│   ├── 2019_09_15_000020_create_domains_table.php ✅
│   ├── 2026_07_13_122949_create_users_table.php ✅
│   ├── 2026_07_13_123136_create_licenses_table.php ✅
│   ├── 2026_07_13_130328_create_permission_tables.php ✅
│   ├── 2026_07_17_075105_create_password_resets_table.php ✅
│   ├── 2026_07_20_094636_add_custom_columns_to_tenants_table.php ✅
│   └── 2026_07_20_094727_create_subscription_plans_table.php ✅
└── migrations/tenant/
    ├── 2026_07_13_123136_create_families_table.php ✅
    ├── 2026_07_13_123136_create_parents_table.php ✅
    ├── 2026_07_13_123137_create_children_table.php ✅
    ├── 2026_07_13_123321_create_garderie_events_table.php ✅
    ├── 2026_07_13_123321_create_garderie_presences_table.php ✅
    ├── 2026_07_13_123322_create_cantine_events_table.php ✅
    ├── 2026_07_13_123322_create_cantine_presences_table.php ✅
    └── 2026_07_15_151558_create_settings_table.php ✅
```

---

## 🎯 Résumé

**Phase 1 : Configuration de base** est à **60% complétée** ✅

**Ce qui fonctionne :**
- ✅ Architecture multi-tenant configurée
- ✅ Modèles Tenant et SubscriptionPlan créés
- ✅ Migrations séparées (central vs tenant)
- ✅ Configuration des connexions DB

**Ce qu'il faut faire :**
1. ⚠️ **Démarrer MySQL/MariaDB**
2. ⚠️ **Créer la DB centrale** (`dailydesk_central`)
3. ⚠️ **Seeder les plans d'abonnement**
4. ⏳ Créer le système de provisioning (Job + Controller)
5. ⏳ Créer les routes et middleware
6. ⏳ Tester avec un tenant de démo

---

## 🔧 Commandes utiles

```bash
# Créer la DB centrale
php artisan db:create-central

# Exécuter migrations centrales
php artisan migrate

# Seeder les plans
php artisan db:seed --class=SubscriptionPlansSeeder

# Vider le cache
php artisan config:clear
php artisan cache:clear

# Lister les migrations
php artisan migrate:status
```

---

## 📞 Support

Si vous rencontrez des problèmes :
1. Vérifier que MySQL/MariaDB est démarré
2. Vérifier les credentials dans `.env`
3. Vérifier les permissions de création de DB
4. Consulter les logs : `storage/logs/laravel.log`
