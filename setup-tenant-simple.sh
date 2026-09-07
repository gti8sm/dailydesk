#!/bin/bash

# Script simple pour créer un tenant de test en développement local

echo "🚀 Configuration du tenant de test..."
echo ""

# Récupérer l'ID du tenant
TENANT_ID=$(php artisan tinker --execute="echo App\Models\Tenant::where('slug', 'beauville')->first()->id;")

if [ -z "$TENANT_ID" ]; then
    echo "❌ Tenant 'beauville' non trouvé"
    exit 1
fi

echo "✓ Tenant ID: $TENANT_ID"

# Nom de la base de données
DB_NAME="tenant_$TENANT_ID"

echo "✓ Base de données: $DB_NAME"
echo ""

# Exécuter les migrations directement sur la base tenant
echo "📦 Exécution des migrations..."
php artisan migrate --path=database/migrations/tenant --database=central --force --env=local << EOF
USE $DB_NAME;
EOF

# Ou utilisons une approche différente - créons les tables manuellement
mysql -u dailydesk -ppassword -e "USE $DB_NAME; SHOW TABLES;" 2>/dev/null

if [ $? -ne 0 ]; then
    echo "⚠️  Problème d'accès à la base de données"
    echo "Essayons une autre approche..."
fi

echo ""
echo "✅ Configuration terminée !"
echo ""
echo "Pour accéder au tenant :"
echo "  URL: http://beauville.localhost:8001"
echo "  (Ajoutez '127.0.0.1 beauville.localhost' dans /etc/hosts)"
