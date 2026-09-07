---
description: Deploy the application on o2switch shared hosting via SSH and Git
---

# Déploiement sur o2switch

## Prérequis
- Accès SSH à o2switch (mur.o2switch.net)
- Accès cPanel pour créer le sous-domaine et la BDD
- Le repo GitHub https://github.com/gti8sm/dailydesk

## Étapes

### 1. Connexion SSH
```bash
ssh gti8sm@mur.o2switch.net
```

### 2. Cloner le repo dans public_html
```bash
cd ~/public_html
git clone https://github.com/gti8sm/dailydesk.git dailydesk
cd dailydesk
```

### 3. Copier le .env de production
Le fichier `.env.production` n'est pas dans git. Il faut le créer manuellement :
```bash
cp .env.example .env
nano .env
```
Coller le contenu du `.env.production` fourni séparément.

### 4. Installer les dépendances
```bash
composer install --no-dev --optimize-autoloader
```

### 5. Générer la clé et migrer
```bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. Configurer le point d'entrée (cPanel)
Dans cPanel :
- Domaines > Domaine principal > Document Root
- Changer le document root de `public_html` vers `public_html/dailydesk/public`

OU créer un `.htaccess` dans `~/public_html` :
```apache
RewriteEngine On
RewriteRule ^(.*)$ dailydesk/public/$1 [L]
```

### 7. Permissions
```bash
chmod -R 755 storage bootstrap/cache
```

### 8. Vérifier
Visiter https://dailydesk.fr et se connecter avec :
- Email : simonmaraval@smallwebconcept.fr
- Mot de passe : Occupy-Shoplift9-Exposable

## Mises à jour futures
```bash
cd ~/public_html/dailydesk
git pull origin main
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
