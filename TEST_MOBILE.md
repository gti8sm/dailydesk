# 📱 Guide de Test - Interface Mobile

## 🎯 Comptes de Test

### 1. Personnel Polyvalent (NOUVEAU !)
```
Email: polyvalent@test.fr
Password: password
Code PIN: 0000
Rôles: ALSH + Cantine
```
**Accès** : Garderie ET Cantine

### 2. Personnel ALSH
```
Email: alsh@test.fr
Password: password
Code PIN: 5678
Rôle: ALSH uniquement
```
**Accès** : Garderie uniquement

### 3. Personnel Cantine
```
Email: cantine@test.fr
Password: password
Code PIN: 9012
Rôle: Cantine uniquement
```
**Accès** : Cantine uniquement

## 🧪 Scénarios de Test

### Test 1 : Recherche Alphabétique - Garderie

**Objectif** : Tester la recherche GPS-style

1. Se connecter avec `polyvalent@test.fr` (PIN: 0000)
2. Cliquer sur "Garderie"
3. Observer le clavier alphabétique :
   - Lettres **bleues** = disponibles
   - Lettres **grises** = non disponibles
4. Cliquer sur **"S"**
   - Affichage : "S"
   - Nouvelles lettres disponibles : O, A (selon enfants)
5. Cliquer sur **"O"**
   - Affichage : "SO"
   - Enfant "Sophie" devrait apparaître
6. Cliquer sur **"P"**
   - "Sophie Dupont" sélectionnée automatiquement
7. Vérifier l'affichage :
   - ✅ Nom complet
   - ✅ Classe (CP)
   - ✅ Famille (Dupont)

### Test 2 : Enregistrement Arrivée

**Objectif** : Tester l'enregistrement tactile

1. Après avoir sélectionné Sophie (Test 1)
2. Observer les **gros boutons** :
   - 🟢 ARRIVÉE (vert)
   - 🟠 DÉPART (orange)
3. Cliquer sur **ARRIVÉE**
   - Animation au toucher (scale-95)
   - Message de confirmation
   - Rechargement de la page
4. Vérifier dans la liste de droite :
   - ✅ Sophie apparaît avec heure d'arrivée
   - ✅ Badge "Présent"

### Test 3 : Recherche Alphabétique - Cantine

**Objectif** : Tester avec alertes allergies

1. Cliquer sur "Cantine"
2. Sélectionner "Déjeuner" dans le menu déroulant
3. Recherche alphabétique :
   - Cliquer **"E"** → **"M"**
   - Emma Martin devrait apparaître
4. Observer les **alertes** :
   - 🟡 Badge "RESTRICTIONS" (jaune)
   - Détails affichés en bas
5. Vérifier la carte détaillée :
   - ✅ Grande alerte jaune
   - ✅ Texte "Végétarien" visible
   - ✅ Icône info

### Test 4 : Enregistrement avec Alertes

**Objectif** : Vérifier que les alertes sont visibles

1. Avec Emma sélectionnée (Test 3)
2. **LIRE** l'alerte restrictions alimentaires
3. Cliquer sur le **gros bouton PRÉSENT** (vert)
4. Confirmer l'enregistrement
5. Vérifier dans la liste :
   - ✅ Emma avec badge allergie
   - ✅ Statut "Présent"

### Test 5 : Réinitialisation

**Objectif** : Tester le bouton reset

1. Après une recherche (ex: "SO")
2. Cliquer sur **"Réinitialiser"**
3. Vérifier :
   - ✅ Recherche effacée
   - ✅ Toutes les lettres disponibles
   - ✅ Liste complète affichée

### Test 6 : Multi-Rôles

**Objectif** : Vérifier l'accès aux deux modules

1. Connecté avec `polyvalent@test.fr`
2. Observer le menu :
   - ✅ "Garderie" visible
   - ✅ "Cantine" visible
3. Naviguer entre les deux :
   - Clic "Garderie" → Interface bleue
   - Clic "Cantine" → Interface verte
4. Vérifier les permissions :
   - ✅ Boutons d'action visibles dans les deux

### Test 7 : Responsive Mobile

**Objectif** : Tester sur petit écran

1. Ouvrir sur smartphone (ou DevTools mobile)
2. Mode portrait :
   - ✅ Clavier 7 colonnes
   - ✅ Boutons pleine largeur
   - ✅ Texte lisible
3. Mode paysage :
   - ✅ Clavier 9 colonnes
   - ✅ Meilleur espacement

### Test 8 : Tablette Tactile

**Objectif** : Vérifier l'ergonomie tactile

1. Ouvrir sur tablette (iPad, Android)
2. Tester les zones de touch :
   - ✅ Lettres faciles à cliquer
   - ✅ Boutons bien espacés
   - ✅ Pas de clics accidentels
3. Tester avec le doigt :
   - ✅ Feedback visuel au toucher
   - ✅ Animation smooth
4. Tester rapidement :
   - Rechercher 5 enfants d'affilée
   - Chronométrer le temps

## 📊 Checklist de Validation

### Interface Générale
- [ ] Boutons > 60px de hauteur
- [ ] Espacement suffisant (12-16px)
- [ ] Texte lisible (18px minimum)
- [ ] Couleurs contrastées
- [ ] Animations fluides

### Recherche Alphabétique
- [ ] Lettres disponibles en couleur
- [ ] Lettres indisponibles grisées
- [ ] Affichage de la recherche en cours
- [ ] Compteur d'enfants trouvés
- [ ] Sélection automatique si 1 résultat
- [ ] Bouton réinitialiser fonctionne

### Alertes
- [ ] Allergies en rouge vif
- [ ] Restrictions en jaune
- [ ] Icônes visibles
- [ ] Texte détaillé affiché
- [ ] Impossible à manquer

### Performance
- [ ] Recherche instantanée
- [ ] Pas de lag au clic
- [ ] Rechargement rapide
- [ ] Scroll fluide

### Multi-Rôles
- [ ] Menu affiche les deux modules
- [ ] Navigation fluide
- [ ] Permissions correctes
- [ ] Pas de conflit

## 🐛 Problèmes Connus

### Si les lettres ne se désactivent pas
→ Vérifier que les enfants ont des prénoms en base de données

### Si les boutons sont trop petits
→ Vérifier que Tailwind CSS est bien chargé

### Si la recherche ne fonctionne pas
→ Vérifier la route `/garderie/search` ou `/cantine/search`

## 📈 Métriques de Succès

**Temps moyen pour enregistrer une présence** :
- ✅ Cible : < 10 secondes
- 🎯 Optimal : < 5 secondes

**Taux d'erreur** :
- ✅ Cible : < 5%
- 🎯 Optimal : < 1%

**Satisfaction utilisateur** :
- ✅ Interface intuitive
- ✅ Pas de formation nécessaire
- ✅ Utilisable en conditions réelles

## 🎉 Résultat Attendu

Après ces tests, vous devriez avoir :
- ✅ Une interface ultra-rapide
- ✅ Une recherche intuitive
- ✅ Des alertes impossibles à manquer
- ✅ Un outil utilisable sur tablette
- ✅ Un personnel polyvalent efficace

**Prêt pour la production !** 🚀
