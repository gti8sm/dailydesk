# 🚀 Guide de Déploiement sur cPanel

## 📋 Prérequis

- Accès cPanel avec SSH
- PHP 8.2 ou supérieur
- MySQL/MariaDB
- Composer installé
- Accès aux DNS pour configurer les sous-domaines

---

## 🎯 Étape 1 : Préparer le serveur cPanel

### 1.1 Vérifier la version PHP

Dans cPanel :
1. Aller dans **MultiPHP Manager**
2. Sélectionner votre domaine
3. Choisir **PHP 8.2** ou supérieur
4. Activer les extensions requises :
   - `mbstring`
   - `pdo_mysql`
   - `openssl`
   - `tokenizer`
   - `xml`
   - `ctype`
   - `json`
   - `bcmath`
   - `fileinfo`

### 1.2 Créer une base de données MySQL

Dans cPanel :
1. Aller dans **MySQL Databases**
2. Créer une nouvelle base de données : `dailydesk_central`
3. Créer un utilisateur MySQL
4. Assigner l'utilisateur à la base avec **TOUS LES PRIVILÈGES**
5. Noter les informations :
   - Nom de la base : `cpanel_dailydesk_central`
   - Utilisateur : `cpanel_dailydesk`
   - Mot de passe : `votre_mot_de_passe`
   - Hôte : `localhost`

---

## 📦 Étape 2 : Uploader les fichiers

### Option A : Via FTP/SFTP

1. Compresser votre projet local :
   ```bash
   tar -czf dailydesk.tar.gz --exclude='node_modules' --exclude='.git' --exclude='vendor' .
   ```

2. Uploader `dailydesk.tar.gz` dans `/home/votre_user/`

3. Se connecter en SSH et extraire :
   ```bash
   cd /home/votre_user/
   tar -xzf dailydesk.tar.gz -C public_html/
   cd public_html
   ```

### Option B : Via Git (recommandé)

```bash
cd /home/votre_user/public_html/
git clone https://votre-repo.git .
```

---

## 🔧 Étape 3 : Exécuter le script d'installation

### 3.1 Se connecter en SSH

```bash
ssh votre_user@votre_domaine.com
cd public_html
```

### 3.2 Lancer l'installation

```bash
bash install.sh
```

Le script vous demandera :
- **Hôte MySQL** : `localhost`
- **Port MySQL** : `3306`
- **Utilisateur MySQL** : `cpanel_dailydesk`
- **Mot de passe MySQL** : `votre_mot_de_passe`
- **Nom de la base** : `cpanel_dailydesk_central`
- **URL de l'application** : `https://dailydesk.votredomaine.com`
- **Domaine central** : `admin.dailydesk.votredomaine.com`
- **Suffixe tenants** : `.dailydesk.votredomaine.com`
- **Email super admin** : `admin@votredomaine.com`
- **Mot de passe super admin** : `votre_mot_de_passe_admin`

### 3.3 Vérifier l'installation

Le script va :
- ✅ Créer le fichier `.env`
- ✅ Générer la clé d'application
- ✅ Installer les dépendances Composer
- ✅ Créer la base de données centrale
- ✅ Exécuter les migrations
- ✅ Créer les plans d'abonnement
- ✅ Créer le super administrateur
- ✅ Optimiser l'application

---

## 🌐 Étape 4 : Configurer le DNS (Wildcard)

### 4.1 Dans votre gestionnaire DNS

Ajouter les enregistrements suivants :

```
Type    Nom                             Valeur
A       admin.dailydesk.votredomaine     IP_DU_SERVEUR
A       *.dailydesk.votredomaine         IP_DU_SERVEUR
```

Le wildcard `*` permet à tous les sous-domaines de pointer vers votre serveur.

### 4.2 Dans cPanel - Addon Domains

1. Aller dans **Addon Domains**
2. Ajouter le domaine principal : `dailydesk.votredomaine.com`
3. Document Root : `/home/votre_user/public_html/public`

---

## 🔒 Étape 5 : Configurer SSL (Let's Encrypt)

### Dans cPanel

1. Aller dans **SSL/TLS Status**
2. Sélectionner tous les domaines :
   - `dailydesk.votredomaine.com`
   - `admin.dailydesk.votredomaine.com`
   - `*.dailydesk.votredomaine.com` (wildcard)
3. Cliquer sur **Run AutoSSL**

### Ou via Certbot (SSH)

```bash
sudo certbot --apache -d dailydesk.votredomaine.com -d admin.dailydesk.votredomaine.com -d *.dailydesk.votredomaine.com
```

---

## ⚙️ Étape 6 : Configurer Apache/Nginx

### 6.1 Fichier .htaccess (déjà inclus)

Le fichier `public/.htaccess` est déjà configuré pour Laravel.

### 6.2 Vérifier la configuration Apache

Dans cPanel, aller dans **MultiPHP INI Editor** et vérifier :

```ini
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
memory_limit = 256M
```

### 6.3 Configuration du Document Root

**IMPORTANT** : Le document root doit pointer vers `/public`

Dans cPanel :
1. **Domains** → Sélectionner votre domaine
2. **Document Root** : `/home/votre_user/public_html/public`

---

## 🎨 Étape 7 : Créer votre premier tenant

### Via SSH

```bash
cd /home/votre_user/public_html
php artisan tenant:create
```

Ou créer manuellement :

```bash
php artisan tinker
```

```php
$tenant = App\Models\Tenant::create([
    'name' => 'Mairie de Beauville',
    'slug' => 'beauville',
    'email' => 'contact@beauville.fr',
    'subscription_plan' => 'pro',
    'status' => 'active',
    'subscription_starts_at' => now(),
    'subscription_expires_at' => now()->addYear(),
    'max_children' => 150,
    'modules_enabled' => ['garderie', 'cantine'],
]);

// Créer la base de données tenant
$tenant->createDatabase();

// Exécuter les migrations tenant
$tenant->run(function () {
    Artisan::call('migrate', [
        '--path' => 'database/migrations/tenant',
        '--database' => 'tenant',
    ]);
});

// Créer le domaine
$tenant->domains()->create([
    'domain' => 'beauville.dailydesk.votredomaine.com',
]);

echo "Tenant créé avec succès !";
```

---

## 🔍 Étape 8 : Vérifications

### 8.1 Tester l'accès super admin

1. Aller sur `https://admin.dailydesk.votredomaine.com`
2. Se connecter avec l'email et mot de passe du super admin
3. Vérifier que le dashboard s'affiche

### 8.2 Tester l'accès tenant

1. Aller sur `https://beauville.dailydesk.votredomaine.com`
2. Vérifier que la page de connexion s'affiche
3. Le nom de l'application devrait être personnalisable

### 8.3 Vérifier les bases de données

```bash
php artisan tinker
```

```php
// Lister les tenants
App\Models\Tenant::all();

// Lister les plans
App\Models\Central\SubscriptionPlan::all();

// Vérifier la connexion tenant
$tenant = App\Models\Tenant::first();
$tenant->run(function () {
    echo "Familles: " . App\Models\Family::count();
});
```

---

## 🔄 Étape 9 : Configuration des Cron Jobs

### Dans cPanel - Cron Jobs

Ajouter les tâches suivantes :

```bash
# Scheduler Laravel (toutes les minutes)
* * * * * cd /home/votre_user/public_html && php artisan schedule:run >> /dev/null 2>&1

# Queue worker (optionnel, si vous utilisez les queues)
* * * * * cd /home/votre_user/public_html && php artisan queue:work --stop-when-empty >> /dev/null 2>&1
```

---

## 📧 Étape 10 : Configurer l'envoi d'emails (SMTP)

### Dans le fichier .env

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.votredomaine.com
MAIL_PORT=587
MAIL_USERNAME=noreply@votredomaine.com
MAIL_PASSWORD=votre_mot_de_passe_email
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@votredomaine.com
MAIL_FROM_NAME="DailyDesk"
```

### Tester l'envoi d'email

```bash
php artisan tinker
```

```php
Mail::raw('Test email', function ($message) {
    $message->to('votre@email.com')
            ->subject('Test DailyDesk');
});
```

---

## 🛡️ Étape 11 : Sécurité

### 11.1 Protéger les fichiers sensibles

Vérifier que ces fichiers ne sont PAS accessibles via le web :
- `.env`
- `composer.json`
- `composer.lock`
- `artisan`

### 11.2 Désactiver le mode debug

Dans `.env` :
```env
APP_DEBUG=false
APP_ENV=production
```

### 11.3 Configurer les permissions

```bash
chmod -R 755 storage bootstrap/cache
chown -R votre_user:votre_user storage bootstrap/cache
```

---

## 🔧 Maintenance

### Mettre à jour l'application

```bash
cd /home/votre_user/public_html

# Sauvegarder la base de données
php artisan backup:run

# Mettre à jour le code
git pull origin main

# Mettre à jour les dépendances
composer install --no-dev --optimize-autoloader

# Exécuter les migrations
php artisan migrate --force

# Vider le cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Re-optimiser
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Sauvegardes automatiques

Créer un cron job pour les sauvegardes quotidiennes :

```bash
0 2 * * * cd /home/votre_user/public_html && php artisan backup:run >> /dev/null 2>&1
```

---

## ❓ Troubleshooting

### Erreur 500

1. Vérifier les logs : `storage/logs/laravel.log`
2. Vérifier les permissions : `chmod -R 755 storage bootstrap/cache`
3. Vérifier le `.env`

### Erreur "Connection refused"

1. Vérifier les credentials MySQL dans `.env`
2. Vérifier que MySQL est démarré
3. Tester la connexion : `php artisan tinker` puis `DB::connection()->getPdo();`

### Sous-domaines ne fonctionnent pas

1. Vérifier le DNS wildcard
2. Vérifier la configuration Apache/Nginx
3. Vérifier les domaines dans la table `domains`

### CSS/JS ne se chargent pas

1. Vérifier que le document root pointe vers `/public`
2. Vérifier les permissions des fichiers
3. Vider le cache : `php artisan view:clear`

---

## 📞 Support

Pour toute question :
1. Consulter `MULTI_TENANT_SETUP.md`
2. Consulter `MULTI_TENANT_PROGRESS.md`
3. Vérifier les logs : `storage/logs/laravel.log`

---

## ✅ Checklist de déploiement

- [ ] PHP 8.2+ configuré
- [ ] Extensions PHP activées
- [ ] Base de données MySQL créée
- [ ] Fichiers uploadés
- [ ] Script `install.sh` exécuté
- [ ] DNS wildcard configuré
- [ ] SSL activé
- [ ] Document root = `/public`
- [ ] Premier tenant créé
- [ ] Accès super admin testé
- [ ] Accès tenant testé
- [ ] Emails configurés
- [ ] Cron jobs configurés
- [ ] Mode debug désactivé
- [ ] Sauvegardes configurées

---

**Félicitations ! Votre application multi-tenant est déployée ! 🎉**
