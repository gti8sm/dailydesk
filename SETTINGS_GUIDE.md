# ⚙️ Guide des Paramètres - DailyDesk

## 🎯 Vue d'Ensemble

Le système de paramètres permet aux administrateurs et super admins de configurer :
1. **Horaires de Garderie** - Plages horaires pour l'horodatage automatique
2. **Configuration SMTP** - Envoi automatique d'emails
3. **Notifications** - Choix des alertes à envoyer

## 🔐 Accès

**Qui peut accéder** : 
- Super Admin
- Admin Mairie

**Permission requise** : `manage_settings`

**URL** : `/settings`

## ⏰ Horaires de Garderie

### Fonctionnement

Le système distingue **2 périodes** de garderie :

#### 1. Garderie du Matin
- **Début** : Heure à partir de laquelle les enfants peuvent arriver (défaut: 07:00)
- **Fin** : Heure de fin de la garderie / début de l'école (défaut: 08:30)

#### 2. Garderie du Soir
- **Début** : Heure de début de la garderie / fin de l'école (défaut: 16:30)
- **Fin** : Heure limite de départ des enfants (défaut: 18:30)

### Horodatage Automatique

**Principe** :
- Le système détecte automatiquement si on est le **matin** ou le **soir**
- Un simple **clic sur l'enfant** enregistre l'heure actuelle

**Matin (entre `morning_start` et `morning_end`)** :
```
Clic sur enfant → Enregistrement ARRIVÉE avec heure actuelle
```

**Soir (entre `evening_start` et `evening_end`)** :
```
Clic sur enfant → Enregistrement DÉPART avec heure actuelle
```

**Calcul automatique** :
- La durée est calculée automatiquement **HORS TEMPS DE CLASSE**
- Seul le temps de garderie (matin + soir) est compté
- Affichage en heures et minutes
- Exemple : Arrivée 07:15, Départ 17:00 = 1h15 (matin) + 0h30 (soir) = **1h45** (et non 9h45)

### Exemple Concret

**Configuration** :
- Matin : 07:00 - 08:30
- Soir : 16:30 - 18:30

**Scénario** :
1. **07:15** - Personnel clique sur "Sophie" → Arrivée enregistrée à 07:15
2. **16:45** - Personnel clique sur "Sophie" → Départ enregistré à 16:45
3. **Durée calculée** : 
   - Garderie matin : 07:15 → 08:30 = **1h15**
   - Temps classe : 08:30 → 16:30 = NON COMPTÉ ❌
   - Garderie soir : 16:30 → 16:45 = **0h15**
   - **TOTAL : 1h30** (temps de garderie uniquement)

## 📧 Configuration SMTP

### Paramètres Requis

| Champ | Description | Exemple |
|-------|-------------|---------|
| **Serveur SMTP** | Adresse du serveur mail | `smtp.gmail.com` |
| **Port** | Port de connexion | `587` (TLS) ou `465` (SSL) |
| **Nom d'utilisateur** | Login SMTP | `votre-email@gmail.com` |
| **Mot de passe** | Mot de passe ou app password | `****************` |
| **Chiffrement** | Type de sécurité | `TLS` (recommandé) |
| **Email expéditeur** | Adresse d'envoi | `noreply@mairie.fr` |
| **Nom expéditeur** | Nom affiché | `Garderie Municipale` |

### Exemples de Configuration

#### Gmail
```
Serveur: smtp.gmail.com
Port: 587
Chiffrement: TLS
Username: votre-email@gmail.com
Password: [App Password - voir Google Account]
```

#### Office 365
```
Serveur: smtp.office365.com
Port: 587
Chiffrement: TLS
Username: votre-email@outlook.com
Password: votre-mot-de-passe
```

#### OVH
```
Serveur: ssl0.ovh.net
Port: 587
Chiffrement: TLS
Username: votre-email@votredomaine.fr
Password: votre-mot-de-passe
```

### Test de Configuration

Après avoir sauvegardé les paramètres SMTP :
1. Activer une notification (ex: "Arrivée enregistrée")
2. Enregistrer une arrivée
3. Vérifier que l'email est bien envoyé

## 🔔 Notifications Automatiques

### Types de Notifications

#### 1. Arrivée Enregistrée
- **Quand** : Dès qu'une arrivée est enregistrée
- **Destinataire** : Parents de l'enfant
- **Contenu** : 
  - Nom de l'enfant
  - Heure d'arrivée
  - Date

**Exemple d'email** :
```
Bonjour,

Votre enfant Sophie Dupont est bien arrivé à la garderie à 07:15.

Cordialement,
L'équipe de la garderie
```

#### 2. Départ Enregistré
- **Quand** : Dès qu'un départ est enregistré
- **Destinataire** : Parents de l'enfant
- **Contenu** :
  - Nom de l'enfant
  - Heure de départ
  - Durée totale

**Exemple d'email** :
```
Bonjour,

Votre enfant Sophie Dupont a quitté la garderie à 16:45.
Durée de présence : 9h30

Cordialement,
L'équipe de la garderie
```

#### 3. Absence Détectée
- **Quand** : Si l'enfant n'est pas arrivé 30min après l'heure prévue
- **Destinataire** : Parents de l'enfant
- **Contenu** :
  - Nom de l'enfant
  - Date
  - Demande de confirmation

**Exemple d'email** :
```
Bonjour,

Nous n'avons pas enregistré l'arrivée de Sophie Dupont ce matin.
Merci de nous confirmer son absence.

Cordialement,
L'équipe de la garderie
```

#### 4. Événement Signalé (RECOMMANDÉ)
- **Quand** : Incident, allergie, refus de repas, etc.
- **Destinataire** : Parents de l'enfant
- **Priorité** : **HAUTE**
- **Contenu** :
  - Type d'événement
  - Description détaillée
  - Heure
  - Personne ayant signalé

**Exemple d'email** :
```
⚠️ ALERTE - Événement signalé

Bonjour,

Un événement concernant Sophie Dupont a été signalé :

Type : Allergie
Date : 15/07/2026 à 12:30
Description : Réaction allergique légère aux arachides

Merci de prendre contact avec nous.

Cordialement,
L'équipe de la cantine
```

### Recommandations

✅ **Activer obligatoirement** :
- Événement signalé (sécurité)

⚠️ **Activer selon besoin** :
- Arrivée enregistrée (si parents demandent)
- Départ enregistré (si parents demandent)
- Absence détectée (si problème d'absentéisme)

❌ **Ne pas activer si** :
- Trop d'emails (spam)
- Parents ne consultent pas leurs emails

## 💾 Stockage des Paramètres

### Base de Données

Les paramètres sont stockés dans la table `settings` :

```sql
CREATE TABLE settings (
    id BIGINT PRIMARY KEY,
    key VARCHAR(255) UNIQUE,
    value TEXT,
    type VARCHAR(255),
    group VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Cache

Les paramètres sont **mis en cache** pendant 1 heure pour optimiser les performances.

**Invalidation du cache** :
- Automatique lors de la sauvegarde
- Manuel : `php artisan cache:clear`

### Utilisation dans le Code

```php
// Récupérer un paramètre
$morningStart = Setting::get('garderie_morning_start', '07:00');

// Définir un paramètre
Setting::set('garderie_morning_start', '07:30', 'string', 'garderie');

// Récupérer un groupe
$garderieSettings = Setting::getGroup('garderie');
```

## 🔧 Modification des Paramètres

### Via l'Interface

1. Se connecter en tant qu'Admin ou Super Admin
2. Menu → **Paramètres**
3. Modifier les valeurs souhaitées
4. Cliquer sur **"Enregistrer les Paramètres"**
5. ✅ Confirmation affichée

### Via la Base de Données

```sql
-- Modifier l'heure de début de garderie
UPDATE settings 
SET value = '07:30' 
WHERE key = 'garderie_morning_start';

-- Activer notification arrivée
UPDATE settings 
SET value = '1' 
WHERE key = 'notify_arrival';
```

### Via Tinker

```bash
php artisan tinker
```

```php
// Modifier un paramètre
Setting::set('garderie_morning_start', '07:30');

// Vérifier
Setting::get('garderie_morning_start');
```

## 📊 Valeurs par Défaut

| Paramètre | Valeur | Modifiable |
|-----------|--------|------------|
| `garderie_morning_start` | 07:00 | ✅ |
| `garderie_morning_end` | 08:30 | ✅ |
| `garderie_evening_start` | 16:30 | ✅ |
| `garderie_evening_end` | 18:30 | ✅ |
| `smtp_port` | 587 | ✅ |
| `smtp_encryption` | tls | ✅ |
| `smtp_from_name` | DailyDesk | ✅ |
| `notify_event` | Activé | ✅ |
| `notify_arrival` | Désactivé | ✅ |
| `notify_departure` | Désactivé | ✅ |
| `notify_absence` | Désactivé | ✅ |

## 🚨 Sécurité

### Mot de Passe SMTP

⚠️ **Important** : Le mot de passe SMTP est stocké en **clair** dans la base de données.

**Recommandations** :
1. Utiliser un compte email dédié
2. Utiliser un "App Password" plutôt que le mot de passe principal
3. Limiter les permissions du compte email
4. Changer régulièrement le mot de passe

### Permissions

Seuls les utilisateurs avec la permission `manage_settings` peuvent :
- Voir les paramètres
- Modifier les paramètres
- Voir le mot de passe SMTP

## 🔄 Migration et Sauvegarde

### Exporter les Paramètres

```bash
# Via SQL
mysqldump -u root -p dailydesk settings > settings_backup.sql

# Via Tinker
php artisan tinker
```

```php
$settings = Setting::all()->toArray();
file_put_contents('settings_backup.json', json_encode($settings, JSON_PRETTY_PRINT));
```

### Importer les Paramètres

```bash
# Via SQL
mysql -u root -p dailydesk < settings_backup.sql

# Via Tinker
php artisan tinker
```

```php
$settings = json_decode(file_get_contents('settings_backup.json'), true);
foreach ($settings as $setting) {
    Setting::updateOrCreate(
        ['key' => $setting['key']],
        $setting
    );
}
```

## 📝 FAQ

### Comment changer les horaires de garderie ?
1. Menu → Paramètres
2. Section "Horaires de Garderie"
3. Modifier les heures
4. Enregistrer

### Les emails ne partent pas, que faire ?
1. Vérifier la configuration SMTP
2. Tester avec un email personnel
3. Vérifier les logs : `storage/logs/laravel.log`
4. Vérifier que les notifications sont activées

### Peut-on avoir des horaires différents par jour ?
Non, actuellement les horaires sont fixes pour tous les jours. Cette fonctionnalité pourrait être ajoutée dans une future version.

### Comment désactiver toutes les notifications ?
1. Menu → Paramètres
2. Section "Notifications Automatiques"
3. Désactiver tous les interrupteurs
4. Enregistrer

## 🎯 Prochaines Améliorations

- [ ] Horaires différents par jour de la semaine
- [ ] Horaires différents par période (vacances/école)
- [ ] Templates d'emails personnalisables
- [ ] Test d'envoi d'email depuis l'interface
- [ ] Historique des modifications de paramètres
- [ ] Import/Export des paramètres en JSON
- [ ] Notifications SMS (optionnel)

---

**Pour toute question, consulter la documentation complète ou contacter le support.**
