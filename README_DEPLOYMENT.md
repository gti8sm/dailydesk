# 🚀 Déploiement Rapide - DailyDesk Multi-Tenant

## 📦 Installation en 3 étapes

### 1️⃣ Uploader les fichiers sur cPanel

```bash
# Sur votre PC local
tar -czf dailydesk.tar.gz --exclude='node_modules' --exclude='.git' --exclude='vendor' .
```

Uploader `dailydesk.tar.gz` sur votre serveur cPanel via FTP dans `/home/votre_user/`

### 2️⃣ Extraire et installer

```bash
# Via SSH sur le serveur
cd /home/votre_user/
tar -xzf dailydesk.tar.gz -C public_html/
cd public_html/
bash install.sh
```

### 3️⃣ Configurer le DNS

Ajouter ces enregistrements DNS :

```
Type    Nom                             Valeur
A       admin.dailydesk.votredomaine     IP_SERVEUR
A       *.dailydesk.votredomaine         IP_SERVEUR
```

**C'est tout ! 🎉**

---

## 📋 Ce que le script fait automatiquement

✅ Crée le fichier `.env` avec vos paramètres  
✅ Génère la clé d'application Laravel  
✅ Installe les dépendances Composer  
✅ Crée la base de données centrale  
✅ Exécute les migrations  
✅ Crée les 3 plans d'abonnement (Starter, Pro, Premium)  
✅ Crée le super administrateur  
✅ Optimise l'application pour la production  

---

## 🔑 Informations demandées par le script

Le script vous demandera :

| Information | Exemple | Description |
|-------------|---------|-------------|
| **Hôte MySQL** | `localhost` | Généralement localhost sur cPanel |
| **Port MySQL** | `3306` | Port par défaut |
| **Utilisateur MySQL** | `cpanel_dailydesk` | Créé dans cPanel > MySQL Databases |
| **Mot de passe MySQL** | `***` | Mot de passe de l'utilisateur MySQL |
| **Nom de la base** | `cpanel_dailydesk_central` | Nom de la base créée dans cPanel |
| **URL application** | `https://dailydesk.example.com` | URL principale |
| **Domaine central** | `admin.dailydesk.example.com` | Pour le super admin |
| **Suffixe tenants** | `.dailydesk.example.com` | Pour les sous-domaines |
| **Email admin** | `admin@example.com` | Votre email |
| **Mot de passe admin** | `***` | Mot de passe sécurisé |

---

## 🌐 Configuration DNS Wildcard

### Chez votre registrar (OVH, Gandi, etc.)

Créer 2 enregistrements DNS :

1. **Domaine admin** (super admin)
   ```
   Type: A
   Nom: admin.dailydesk
   Valeur: 123.45.67.89 (IP de votre serveur)
   ```

2. **Wildcard** (tous les tenants)
   ```
   Type: A
   Nom: *.dailydesk
   Valeur: 123.45.67.89 (IP de votre serveur)
   ```

**Délai de propagation** : 1 à 24 heures

---

## 🔒 SSL (Let's Encrypt)

### Via cPanel

1. Aller dans **SSL/TLS Status**
2. Sélectionner tous les domaines
3. Cliquer sur **Run AutoSSL**

### Via SSH (Certbot)

```bash
sudo certbot --apache -d dailydesk.example.com -d admin.dailydesk.example.com -d *.dailydesk.example.com
```

---

## 🎨 Créer votre premier tenant (mairie)

### Méthode 1 : Via commande artisan (à venir)

```bash
php artisan tenant:create
```

### Méthode 2 : Via Tinker

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

$tenant->createDatabase();

$tenant->run(function () {
    Artisan::call('migrate', [
        '--path' => 'database/migrations/tenant',
        '--database' => 'tenant',
    ]);
});

$tenant->domains()->create([
    'domain' => 'beauville.dailydesk.example.com',
]);

echo "✓ Tenant créé !";
```

---

## ✅ Vérifications post-installation

### 1. Tester l'accès super admin

🔗 `https://admin.dailydesk.example.com`

- Email : celui que vous avez saisi
- Mot de passe : celui que vous avez saisi

### 2. Tester l'accès tenant

🔗 `https://beauville.dailydesk.example.com`

- Devrait afficher la page de connexion
- Le nom de l'application est personnalisable

### 3. Vérifier les bases de données

```bash
php artisan tinker
```

```php
// Lister les tenants
App\Models\Tenant::all();

// Lister les plans
App\Models\Central\SubscriptionPlan::all();
```

---

## 🔧 Configuration cPanel recommandée

### PHP Settings (MultiPHP INI Editor)

```ini
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
memory_limit = 256M
max_input_vars = 3000
```

### Document Root

**IMPORTANT** : Doit pointer vers `/public`

```
/home/votre_user/public_html/public
```

### Cron Jobs

Ajouter cette tâche (toutes les minutes) :

```bash
* * * * * cd /home/votre_user/public_html && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📧 Configuration SMTP (emails)

Modifier dans `.env` :

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.votredomaine.com
MAIL_PORT=587
MAIL_USERNAME=noreply@votredomaine.com
MAIL_PASSWORD=votre_mot_de_passe
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@votredomaine.com
MAIL_FROM_NAME="DailyDesk"
```

Puis :

```bash
php artisan config:clear
```

---

## 🆘 Problèmes courants

### ❌ Erreur 500

```bash
# Vérifier les logs
tail -f storage/logs/laravel.log

# Vérifier les permissions
chmod -R 755 storage bootstrap/cache
```

### ❌ CSS/JS ne se chargent pas

Vérifier que le **Document Root** pointe vers `/public`

### ❌ Sous-domaines ne fonctionnent pas

1. Vérifier le DNS wildcard (peut prendre 24h)
2. Vérifier SSL sur tous les domaines
3. Vérifier la table `domains` dans la DB

### ❌ Connexion MySQL refusée

Vérifier les credentials dans `.env` :

```bash
php artisan tinker
DB::connection()->getPdo();
```

---

## 📚 Documentation complète

- **Guide complet** : `DEPLOYMENT_CPANEL.md`
- **Configuration** : `MULTI_TENANT_SETUP.md`
- **Progression** : `MULTI_TENANT_PROGRESS.md`

---

## 🎯 Checklist de déploiement

- [ ] Fichiers uploadés sur cPanel
- [ ] Base de données MySQL créée
- [ ] Script `install.sh` exécuté avec succès
- [ ] DNS wildcard configuré
- [ ] SSL activé (Let's Encrypt)
- [ ] Document root = `/public`
- [ ] Cron job configuré
- [ ] SMTP configuré (optionnel)
- [ ] Premier tenant créé
- [ ] Connexion super admin testée
- [ ] Connexion tenant testée

---

## 🚀 Prêt à démarrer !

Une fois tout configuré, vous pouvez :

1. **Créer des tenants** (mairies) depuis le super admin
2. **Personnaliser** chaque mairie (logo, couleurs)
3. **Gérer les abonnements** et limites
4. **Monitorer** l'utilisation globale

**Bon déploiement ! 🎉**
