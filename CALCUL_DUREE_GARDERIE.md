# ⏱️ Calcul de la Durée de Garderie

## 🎯 Principe Fondamental

La durée de garderie **exclut le temps de classe** et ne compte que :
- ✅ Le temps de garderie du **matin** (avant l'école)
- ✅ Le temps de garderie du **soir** (après l'école)
- ❌ **PAS** le temps de classe

## 📐 Configuration des Horaires

### Paramètres (configurables dans `/settings`)

```
Garderie Matin : 07:00 → 08:30
Temps Classe   : 08:30 → 16:30  (NON COMPTÉ)
Garderie Soir  : 16:30 → 18:30
```

## 💡 Exemples de Calcul

### Exemple 1 : Garderie Matin Uniquement

**Situation** :
- Arrivée : **07:15**
- Départ : **08:15** (avant la fin de la garderie matin)

**Calcul** :
```
Garderie matin : 07:15 → 08:15 = 1h00
Garderie soir  : Aucune
TOTAL : 1h00
```

### Exemple 2 : Garderie Matin + Soir

**Situation** :
- Arrivée : **07:15**
- Départ : **17:00**

**Calcul** :
```
Garderie matin : 07:15 → 08:30 = 1h15
Temps classe   : 08:30 → 16:30 = NON COMPTÉ ❌
Garderie soir  : 16:30 → 17:00 = 0h30
TOTAL : 1h45
```

### Exemple 3 : Arrivée Pendant la Classe

**Situation** :
- Arrivée : **09:00** (pendant la classe)
- Départ : **17:30**

**Calcul** :
```
Garderie matin : Aucune (arrivée après 08:30)
Temps classe   : 09:00 → 16:30 = NON COMPTÉ ❌
Garderie soir  : 16:30 → 17:30 = 1h00
TOTAL : 1h00
```

### Exemple 4 : Journée Complète

**Situation** :
- Arrivée : **07:00** (dès l'ouverture)
- Départ : **18:30** (à la fermeture)

**Calcul** :
```
Garderie matin : 07:00 → 08:30 = 1h30
Temps classe   : 08:30 → 16:30 = NON COMPTÉ ❌
Garderie soir  : 16:30 → 18:30 = 2h00
TOTAL : 3h30
```

### Exemple 5 : Départ Anticipé

**Situation** :
- Arrivée : **07:30**
- Départ : **12:00** (pendant la classe)

**Calcul** :
```
Garderie matin : 07:30 → 08:30 = 1h00
Temps classe   : 08:30 → 12:00 = NON COMPTÉ ❌
Garderie soir  : Aucune
TOTAL : 1h00
```

### Exemple 6 : Soir Uniquement

**Situation** :
- Arrivée : **16:30** (fin de classe)
- Départ : **18:00**

**Calcul** :
```
Garderie matin : Aucune
Temps classe   : NON COMPTÉ ❌
Garderie soir  : 16:30 → 18:00 = 1h30
TOTAL : 1h30
```

## 🔧 Logique de Calcul

### Algorithme

```php
function calculateDuration() {
    $totalMinutes = 0;
    
    // 1. Temps de garderie du MATIN
    if (arrivée < fin_garderie_matin) {
        $fin = min(départ, fin_garderie_matin);
        $totalMinutes += arrivée → $fin;
    }
    
    // 2. Temps de garderie du SOIR
    if (départ > début_garderie_soir) {
        $début = max(arrivée, début_garderie_soir);
        $totalMinutes += $début → départ;
    }
    
    // 3. Le temps entre fin_matin et début_soir est IGNORÉ
    
    return $totalMinutes;
}
```

### Cas Limites

#### Cas 1 : Arrivée = Départ
```
Arrivée : 07:30
Départ  : 07:30
TOTAL   : 0 minutes
```

#### Cas 2 : Uniquement Temps de Classe
```
Arrivée : 09:00
Départ  : 15:00
TOTAL   : 0 minutes (tout est pendant la classe)
```

#### Cas 3 : Chevauchement Matin/Soir Impossible
```
Arrivée : 07:00
Départ  : 08:00
TOTAL   : 1h00 (uniquement matin)

Arrivée : 17:00
Départ  : 18:00
TOTAL   : 1h00 (uniquement soir)
```

## 📊 Tableau Récapitulatif

| Arrivée | Départ | Matin | Soir | Total |
|---------|--------|-------|------|-------|
| 07:00 | 08:00 | 1h00 | - | **1h00** |
| 07:00 | 08:30 | 1h30 | - | **1h30** |
| 07:00 | 17:00 | 1h30 | 0h30 | **2h00** |
| 07:00 | 18:30 | 1h30 | 2h00 | **3h30** |
| 08:00 | 17:00 | 0h30 | 0h30 | **1h00** |
| 09:00 | 17:00 | - | 0h30 | **0h30** |
| 16:30 | 18:00 | - | 1h30 | **1h30** |
| 09:00 | 15:00 | - | - | **0h00** |

## 💰 Impact sur la Facturation

### Tarification Exemple

```
Tarif horaire : 2,50€
```

**Scénario 1** : Arrivée 07:15, Départ 17:00
```
Durée garderie : 1h45 = 105 minutes
Coût : 1,75h × 2,50€ = 4,38€
```

**Scénario 2** : Arrivée 07:00, Départ 18:30
```
Durée garderie : 3h30 = 210 minutes
Coût : 3,5h × 2,50€ = 8,75€
```

**Scénario 3** : Arrivée 09:00, Départ 15:00
```
Durée garderie : 0h00 = 0 minutes
Coût : 0€ (pas de garderie)
```

## 🎯 Avantages de ce Système

✅ **Équitable** : Les parents ne paient que la garderie réelle  
✅ **Transparent** : Calcul clair et compréhensible  
✅ **Automatique** : Pas d'erreur de calcul manuel  
✅ **Configurable** : Horaires adaptables selon l'école  
✅ **Précis** : À la minute près  

## 🔄 Modification des Horaires

Si les horaires de l'école changent :

1. Menu → **Paramètres**
2. Section **"Horaires de Garderie"**
3. Modifier :
   - Fin garderie matin (ex: 08:30 → 09:00)
   - Début garderie soir (ex: 16:30 → 16:00)
4. **Enregistrer**

**Effet** : Tous les calculs futurs utiliseront les nouveaux horaires.

## 📝 Affichage pour les Parents

### Sur le Relevé Mensuel

```
Enfant : Sophie Dupont
Mois : Juillet 2026

Date       | Arrivée | Départ | Matin | Soir  | Total
-----------|---------|--------|-------|-------|-------
01/07/2026 | 07:15   | 17:00  | 1h15  | 0h30  | 1h45
02/07/2026 | 07:00   | 18:30  | 1h30  | 2h00  | 3h30
03/07/2026 | 08:00   | 17:30  | 0h30  | 1h00  | 1h30
...
-----------|---------|--------|-------|-------|-------
TOTAL MOIS |         |        | 25h00 | 30h00 | 55h00

Tarif horaire : 2,50€
MONTANT TOTAL : 137,50€
```

## 🚨 Points d'Attention

### ⚠️ Cas Particuliers

**Journée Continue (pas de garderie soir)** :
```
Si l'école finit à 16:30 mais l'enfant part à 16:00 :
→ Pas de garderie soir comptée
→ Uniquement la garderie matin
```

**Arrivée Tardive** :
```
Si l'enfant arrive à 10:00 (pendant la classe) :
→ Pas de garderie matin comptée
→ Uniquement la garderie soir (si applicable)
```

**Départ Anticipé** :
```
Si l'enfant part à 15:00 (pendant la classe) :
→ Garderie matin comptée
→ Pas de garderie soir
```

## 🔍 Vérification du Calcul

### Via l'Interface

1. Aller sur **Garderie** → **Présences**
2. Sélectionner la date
3. Voir la colonne **"Durée"**
4. Vérifier que seul le temps de garderie est compté

### Via la Base de Données

```sql
SELECT 
    c.first_name,
    c.last_name,
    gp.date,
    gp.arrival_time,
    gp.departure_time,
    gp.duration_minutes,
    CONCAT(
        FLOOR(gp.duration_minutes / 60), 'h',
        LPAD(gp.duration_minutes % 60, 2, '0')
    ) as duration_formatted
FROM garderie_presences gp
JOIN children c ON c.id = gp.child_id
WHERE gp.date = '2026-07-15'
ORDER BY c.last_name;
```

## 📖 Exemple Complet

### Situation Réelle

**École Primaire de Beauville**
- Garderie matin : 07:00 - 08:45
- École : 08:45 - 16:15
- Garderie soir : 16:15 - 18:45

**Enfant : Lucas Martin**
- Lundi : Arrivée 07:30, Départ 17:00
- Mardi : Arrivée 08:00, Départ 18:30
- Mercredi : Arrivée 09:00, Départ 12:00
- Jeudi : Arrivée 07:00, Départ 16:30
- Vendredi : Arrivée 16:15, Départ 18:00

**Calculs** :
```
Lundi   : (07:30→08:45) + (16:15→17:00) = 1h15 + 0h45 = 2h00
Mardi   : (08:00→08:45) + (16:15→18:30) = 0h45 + 2h15 = 3h00
Mercredi: Aucune garderie (arrivée et départ pendant classe) = 0h00
Jeudi   : (07:00→08:45) + (16:15→16:30) = 1h45 + 0h15 = 2h00
Vendredi: (16:15→18:00) = 1h45

TOTAL SEMAINE : 8h45
```

**Facturation** :
```
8h45 = 8,75 heures
8,75h × 2,50€ = 21,88€
```

---

**Ce système garantit une facturation juste et transparente pour tous !** ✅
