# DailyDesk - Système de Gestion Garderie & Cantine

Application web multi-tenant Laravel pour gérer la présence en garderie et les repas de cantine pour les mairies.

## 🚀 Fonctionnalités

### Core
- **Multi-tenant** : Gestion de plusieurs mairies avec sous-domaines
- **Authentification renforcée** : Email/mot de passe + code PIN + whitelist IP
- **Gestion des rôles** : 7 rôles (Super Admin, Admin Mairie, Personnel, Enseignant, ALSH, Cantine, Parent)
- **Gestion des familles** : Familles, parents et enfants avec import CSV/Excel
- **Système de licences** : Gestion des abonnements mensuels/annuels

### Modules

#### Module Garderie
- Enregistrement des arrivées/départs
- Calcul automatique de la durée
- Signalement d'événements/incidents
- Export des compteurs mensuels

#### Module Cantine
- Enregistrement des présences repas (déjeuner/goûter)
- Signalement d'événements (allergies, refus, etc.)
- Export des compteurs mensuels

## 📋 Prérequis

- PHP 8.2 ou supérieur
- MySQL 8.0 / MariaDB 10.6+
- Composer
- Node.js & NPM (pour les assets)
- Extensions PHP : gd, zip, mbstring, xml, curl, mysql

## 🛠️ Installation

### 1. Cloner le projet

```bash
git clone <repository-url> dailydesk
cd dailydesk
```

### 2. Installer les dépendances

```bash
composer install
npm install
```

### 3. Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Éditer `.env` et configurer :
- Base de données (DB_*)
- URL de l'application (APP_URL)
- Domaines centraux (CENTRAL_DOMAINS)
- Mail (MAIL_*)

### 4. Base de données

```bash
php artisan migrate
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### 5. Compiler les assets

```bash
npm run build
```

### 6. Permissions

```bash
chmod -R 775 storage bootstrap/cache
```

## 🏗️ Structure du Projet

```
app/
├── Core/                    # Fonctionnalités core
├── Models/                  # Modèles principaux
│   ├── User.php
│   ├── Family.php
│   ├── ParentModel.php
│   ├── Child.php
│   └── License.php
└── Modules/                 # Modules activables
    ├── Garderie/
    │   └── Models/
    │       ├── GarderiePresence.php
    │       └── GarderieEvent.php
    └── Cantine/
        └── Models/
            ├── CantinePresence.php
            └── CantineEvent.php

database/
└── migrations/              # Migrations de base de données

config/
├── tenancy.php             # Configuration multi-tenant
└── permission.php          # Configuration des permissions
```

## 👥 Rôles et Permissions

### Super Admin
- Gestion globale du système
- Gestion des tenants et licences

### Admin Mairie
- Gestion des utilisateurs
- Gestion des familles (CRUD + import)
- Configuration des modules
- Exports pour facturation

### Personnel Mairie
- Consultation des données
- Exports basiques

### Enseignant
- Consultation des enfants
- Signalement d'événements

### ALSH (Accueil de Loisirs)
- Enregistrement présences garderie
- Signalement événements garderie

### Cantine
- Enregistrement présences cantine
- Signalement événements cantine

### Parent
- Consultation des présences de ses enfants
- Historique des événements
- Gestion des notifications

## 🔐 Authentification

Le système propose 3 méthodes d'authentification :

1. **Email/Mot de passe** : Authentification standard
2. **Code confidentiel** : Code PIN 4-6 chiffres pour accès rapide (mobile/tablette)
3. **Whitelist IP** : Restriction d'accès par adresse IP (optionnel)

## 🌐 Multi-tenant

### Configuration DNS

Configurer un wildcard DNS :
```
*.votre-domaine.com -> IP du serveur
```

### Créer un tenant

```bash
php artisan tenants:create mairie-example
```

### Accès

- Central : `https://dailydesk.fr`
- Tenant : `https://mairie-example.dailydesk.fr`

## 📊 Exports

Les exports Excel sont disponibles pour :
- Liste des familles
- Compteurs garderie (par mois)
- Compteurs cantine (par mois)

Format : Nom, Prénom, Nombre de jours/repas

## 🚀 Déploiement cPanel

### 1. Upload des fichiers

Uploader tous les fichiers dans le répertoire home (pas public_html)

### 2. Configuration du document root

Pointer le document root vers `/public`

### 3. Variables d'environnement

Configurer les variables dans `.env` via cPanel

### 4. Composer

```bash
composer install --optimize-autoloader --no-dev
```

### 5. Optimisations

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🔧 Maintenance

### Vider le cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Mettre à jour les dépendances

```bash
composer update
php artisan migrate
```

## 📝 Licence

Propriétaire - Tous droits réservés

## 🤝 Support

Pour toute question ou support, contactez l'administrateur système.

## 🗺️ Roadmap

### Phase 1 (Actuelle)
- ✅ Core & Infrastructure
- ✅ Module Garderie
- ✅ Module Cantine
- ✅ Gestion des familles

### Phase 2 (À venir)
- 🔄 Interface d'administration complète
- 🔄 Import/Export CSV avancé
- 🔄 Système de notifications
- 🔄 Rapports et statistiques

### Phase 3 (Futur)
- 📅 Module Planning
- 📦 Module Stock
- 💰 Module Comptabilité
- 📱 Application mobile native
