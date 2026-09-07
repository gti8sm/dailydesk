#!/bin/bash

###############################################################################
# Script d'installation DailyDesk Multi-Tenant
# Pour déploiement sur cPanel ou serveur Linux
###############################################################################

set -e  # Arrêter en cas d'erreur

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonction pour afficher les messages
print_message() {
    echo -e "${GREEN}[✓]${NC} $1"
}

print_error() {
    echo -e "${RED}[✗]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[!]${NC} $1"
}

print_info() {
    echo -e "${BLUE}[i]${NC} $1"
}

# Banner
echo -e "${BLUE}"
echo "╔═══════════════════════════════════════════════════════════╗"
echo "║                                                           ║"
echo "║         DAILYDESK - Installation Multi-Tenant             ║"
echo "║                                                           ║"
echo "╚═══════════════════════════════════════════════════════════╝"
echo -e "${NC}"

# Vérification des prérequis
print_info "Vérification des prérequis..."

# Vérifier PHP
if ! command -v php &> /dev/null; then
    print_error "PHP n'est pas installé"
    exit 1
fi

PHP_VERSION=$(php -r "echo PHP_VERSION;")
print_message "PHP version: $PHP_VERSION"

# Vérifier Composer
if ! command -v composer &> /dev/null; then
    print_error "Composer n'est pas installé"
    exit 1
fi
print_message "Composer trouvé"

# Vérifier MySQL/MariaDB
if command -v mysql &> /dev/null; then
    print_message "MySQL/MariaDB trouvé"
elif command -v mariadb &> /dev/null; then
    print_message "MariaDB trouvé"
else
    print_error "MySQL/MariaDB n'est pas installé"
    exit 1
fi

echo ""
print_info "═══════════════════════════════════════════════════════════"
print_info "Configuration de la base de données"
print_info "═══════════════════════════════════════════════════════════"
echo ""

# Demander les informations de connexion DB
read -p "Hôte de la base de données [127.0.0.1]: " DB_HOST
DB_HOST=${DB_HOST:-127.0.0.1}

read -p "Port de la base de données [3306]: " DB_PORT
DB_PORT=${DB_PORT:-3306}

read -p "Nom d'utilisateur MySQL: " DB_USERNAME
read -sp "Mot de passe MySQL: " DB_PASSWORD
echo ""

read -p "Nom de la base de données centrale [dailydesk_central]: " DB_DATABASE
DB_DATABASE=${DB_DATABASE:-dailydesk_central}

# Vérifier la connexion
print_info "Test de connexion à MySQL..."
if mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1;" &> /dev/null; then
    print_message "Connexion MySQL réussie"
else
    print_error "Impossible de se connecter à MySQL avec ces identifiants"
    exit 1
fi

echo ""
print_info "═══════════════════════════════════════════════════════════"
print_info "Configuration de l'application"
print_info "═══════════════════════════════════════════════════════════"
echo ""

read -p "URL de l'application (ex: https://dailydesk.example.com): " APP_URL
read -p "Domaine central pour super admin [admin.dailydesk.test]: " CENTRAL_DOMAIN
CENTRAL_DOMAIN=${CENTRAL_DOMAIN:-admin.dailydesk.test}

read -p "Suffixe des sous-domaines tenants [.dailydesk.test]: " TENANT_SUFFIX
TENANT_SUFFIX=${TENANT_SUFFIX:-.dailydesk.test}

# Email super admin
read -p "Email du super administrateur: " ADMIN_EMAIL
read -sp "Mot de passe du super administrateur: " ADMIN_PASSWORD
echo ""

# Créer/Mettre à jour le fichier .env
print_info "Création du fichier .env..."

if [ ! -f .env ]; then
    cp .env.example .env
    print_message "Fichier .env créé depuis .env.example"
else
    print_warning "Fichier .env existe déjà, sauvegarde dans .env.backup"
    cp .env .env.backup
fi

# Mettre à jour les variables d'environnement
cat > .env << EOF
APP_NAME=DailyDesk
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=$APP_URL

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=central
DB_HOST=$DB_HOST
DB_PORT=$DB_PORT
DB_DATABASE=$DB_DATABASE
DB_USERNAME=$DB_USERNAME
DB_PASSWORD=$DB_PASSWORD

TENANT_DB_PREFIX=tenant_
CENTRAL_DOMAIN=$CENTRAL_DOMAIN
TENANT_DOMAIN_SUFFIX=$TENANT_SUFFIX

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="\${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="\${APP_NAME}"
VITE_PUSHER_APP_KEY="\${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="\${PUSHER_HOST}"
VITE_PUSHER_PORT="\${PUSHER_PORT}"
VITE_PUSHER_SCHEME="\${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="\${PUSHER_APP_CLUSTER}"
EOF

print_message "Fichier .env configuré"

# Générer la clé d'application
print_info "Génération de la clé d'application..."
php artisan key:generate --force
print_message "Clé d'application générée"

# Installer les dépendances
print_info "Installation des dépendances Composer..."
composer install --no-dev --optimize-autoloader
print_message "Dépendances installées"

# Créer la base de données centrale
print_info "Création de la base de données centrale..."
mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS \`$DB_DATABASE\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || {
    print_error "Impossible de créer la base de données"
    exit 1
}
print_message "Base de données centrale créée: $DB_DATABASE"

# Exécuter les migrations centrales
print_info "Exécution des migrations centrales..."
php artisan migrate --force
print_message "Migrations centrales exécutées"

# Seeder les plans d'abonnement
print_info "Création des plans d'abonnement..."
php artisan db:seed --class=SubscriptionPlansSeeder --force
print_message "Plans d'abonnement créés (Starter, Pro, Premium)"

# Créer le super administrateur
print_info "Création du super administrateur..."
php artisan tinker --execute="
\$user = App\Models\User::firstOrCreate(
    ['email' => '$ADMIN_EMAIL'],
    [
        'name' => 'Super Administrateur',
        'password' => Hash::make('$ADMIN_PASSWORD'),
        'is_active' => true,
    ]
);
if (!\$user->hasRole('super_admin')) {
    \$user->assignRole('super_admin');
}
echo 'Super admin créé: ' . \$user->email;
"
print_message "Super administrateur créé: $ADMIN_EMAIL"

# Optimisations
print_info "Optimisation de l'application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
print_message "Application optimisée"

# Permissions des fichiers
print_info "Configuration des permissions..."
chmod -R 755 storage bootstrap/cache
print_message "Permissions configurées"

# Créer le fichier de version
echo "$(date '+%Y-%m-%d %H:%M:%S')" > storage/app/installation.txt
echo "Version: 1.0.0" >> storage/app/installation.txt
echo "DB: $DB_DATABASE" >> storage/app/installation.txt

echo ""
echo -e "${GREEN}"
echo "╔═══════════════════════════════════════════════════════════╗"
echo "║                                                           ║"
echo "║         ✓ Installation terminée avec succès !            ║"
echo "║                                                           ║"
echo "╚═══════════════════════════════════════════════════════════╝"
echo -e "${NC}"
echo ""

print_info "Informations de connexion:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "${BLUE}URL:${NC}            $APP_URL"
echo -e "${BLUE}Admin URL:${NC}      $APP_URL (domaine: $CENTRAL_DOMAIN)"
echo -e "${BLUE}Email:${NC}          $ADMIN_EMAIL"
echo -e "${BLUE}Mot de passe:${NC}   (celui que vous avez saisi)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

print_warning "Prochaines étapes:"
echo "  1. Configurer votre serveur web (Apache/Nginx)"
echo "  2. Configurer le DNS wildcard pour les sous-domaines"
echo "  3. Configurer SSL (Let's Encrypt recommandé)"
echo "  4. Créer votre premier tenant avec: php artisan tenant:create"
echo ""

print_info "Documentation:"
echo "  - Guide complet: MULTI_TENANT_SETUP.md"
echo "  - Progression: MULTI_TENANT_PROGRESS.md"
echo ""

print_message "Installation terminée ! 🚀"
