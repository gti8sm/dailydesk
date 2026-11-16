<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide utilisateur DailyDesk</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; color: #1f2937; font-size: 11pt; line-height: 1.6; margin: 0; }
        .page { padding: 40px 50px; page-break-after: always; }
        .page:last-child { page-break-after: avoid; }
        h1 { color: #2563eb; font-size: 20pt; border-bottom: 3px solid #2563eb; padding-bottom: 8px; margin-top: 0; }
        h2 { color: #1e40af; font-size: 14pt; margin-top: 24px; }
        h3 { color: #374151; font-size: 12pt; margin-top: 16px; }
        p { margin: 8px 0; }
        ul, ol { margin: 8px 0 8px 24px; }
        li { margin: 4px 0; }
        table { border-collapse: collapse; width: 100%; margin: 12px 0; }
        th { background: #2563eb; color: #ffffff; padding: 8px; text-align: left; font-size: 10pt; }
        td { border: 1px solid #d1d5db; padding: 8px; font-size: 10pt; vertical-align: top; }
        tr:nth-child(even) td { background: #f3f4f6; }
        .cover { text-align: center; padding-top: 180px; }
        .cover .logo { font-size: 42pt; font-weight: bold; color: #2563eb; }
        .cover .subtitle { font-size: 16pt; color: #374151; margin-top: 12px; }
        .cover .version { font-size: 10pt; color: #9ca3af; margin-top: 60px; }
        .tip { background: #eff6ff; border-left: 4px solid #2563eb; padding: 10px 14px; margin: 12px 0; font-size: 10pt; }
        .warning { background: #fff7ed; border-left: 4px solid #ea580c; padding: 10px 14px; margin: 12px 0; font-size: 10pt; }
        .step { background: #f0fdf4; border: 1px solid #bbf7d0; padding: 10px 14px; margin: 10px 0; font-size: 10pt; }
        .step b { color: #15803d; }
        .toc td:first-child { width: 40px; color: #2563eb; font-weight: bold; }
        .screen-only { display: block; background: #f9fafb; border: 1px solid #e5e7eb; padding: 12px 16px; margin-bottom: 20px; font-size: 10pt; }
        .screen-only a { color: #2563eb; }
        .screen-only .btn { display: inline-block; background: #2563eb; color: #ffffff; padding: 8px 16px; text-decoration: none; font-weight: bold; margin-top: 8px; }
        .section-number { color: #2563eb; font-weight: bold; font-size: 12pt; }
        .footer-note { color: #9ca3af; font-size: 9pt; margin-top: 30px; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>

@if(!isset($pdf))
<div class="screen-only">
    <strong>Guide utilisateur DailyDesk</strong> — vous pouvez le consulter en ligne ou le télécharger pour l'imprimer
    et le remettre aux agents de la mairie.
    <br><a class="btn" href="{{ route('guide.download') }}">Télécharger le PDF</a>
</div>
@endif

<!-- Couverture -->
<div class="page cover">
    <div class="logo">DailyDesk</div>
    <div class="subtitle">Guide utilisateur de la plateforme municipale</div>
    <p style="font-size: 11pt; color: #4b5563; margin-top: 24px;">
        Familles &middot; Garderie &middot; Cantine &middot; Portail parents<br>
        Site public &middot; Stock &middot; Rapports
    </p>
    <div class="version">Version du guide : {{ now()->locale('fr')->translatedFormat('F Y') }} &middot; DailyDesk</div>
</div>

<!-- Sommaire -->
<div class="page">
    <h1>Sommaire</h1>
    <table class="toc">
        <tr><td>1</td><td>Présentation de DailyDesk</td></tr>
        <tr><td>2</td><td>Connexion, rôles et sécurité</td></tr>
        <tr><td>3</td><td>Familles et enfants</td></tr>
        <tr><td>4</td><td>Garderie : pointage quotidien</td></tr>
        <tr><td>5</td><td>Cantine : présences et absences</td></tr>
        <tr><td>6</td><td>Menus de la cantine</td></tr>
        <tr><td>7</td><td>Portail parent</td></tr>
        <tr><td>8</td><td>Site public de la commune</td></tr>
        <tr><td>9</td><td>Gestion du stock</td></tr>
        <tr><td>10</td><td>Rapports et exports</td></tr>
        <tr><td>11</td><td>Paramètres et modules</td></tr>
        <tr><td>12</td><td>Aide et support</td></tr>
    </table>
</div>

<!-- 1. Présentation -->
<div class="page">
    <h1>1. Présentation de DailyDesk</h1>
    <p>DailyDesk est la plateforme de gestion municipale qui regroupe dans un seul outil :</p>
    <ul>
        <li><strong>Familles et enfants</strong> — fiches complètes, imports CSV en masse</li>
        <li><strong>Garderie</strong> — pointage des arrivées et départs, rapports mensuels</li>
        <li><strong>Cantine</strong> — présences des repas, menus de la semaine, plats et allergènes</li>
        <li><strong>Portail parent</strong> — agenda et menus consultés par les familles</li>
        <li><strong>Site public de la commune</strong> — actualités, démarches, conseil municipal, contact</li>
        <li><strong>Stock</strong> — articles, lieux de stockage, mouvements et alertes de seuil</li>
        <li><strong>Rapports et exports</strong> — statistiques CSV/Excel pour la comptabilité municipale</li>
    </ul>
    <div class="tip">
        <strong>Bonne pratique</strong> : chaque agent utilise son propre compte. Ne partagez jamais
        un mot de passe entre plusieurs personnes — le journal d'activité identifie précisément
        qui a fait quoi.
    </div>
    <p>L'application fonctionne sur ordinateur, tablette et mobile, directement dans le navigateur,
    sans rien installer. Elle est hébergée en France.</p>
</div>

<!-- 2. Connexion -->
<div class="page">
    <h1>2. Connexion, rôles et sécurité</h1>

    <h2>Se connecter</h2>
    <div class="step">
        <b>1.</b> Ouvrez l'adresse de votre espace DailyDesk.<br>
        <b>2.</b> Saisissez votre <strong>email</strong> (ou votre identifiant) et votre <strong>mot de passe</strong>.<br>
        <b>3.</b> Validez — vous arrivez directement sur votre tableau de bord.
    </div>
    <p>En cas d'oubli, utilisez « Mot de passe oublié » ou contactez l'administrateur de votre mairie.</p>

    <h2>Les rôles</h2>
    <table>
        <tr><th>Rôle</th><th>Accès</th></tr>
        <tr><td><strong>Admin mairie</strong></td><td>Tout : familles, enfants, utilisateurs, paramètres, rapports, site public</td></tr>
        <tr><td><strong>Personnel municipal</strong></td><td>Lecture des fiches et saisie des présences garderie / cantine</td></tr>
        <tr><td><strong>Cuisinier</strong></td><td>Cantine : présences des repas et menus</td></tr>
        <tr><td><strong>Agent ALSH</strong></td><td>Garderie : pointage des enfants</td></tr>
        <tr><td><strong>Enseignant</strong></td><td>Création d'événements</td></tr>
        <tr><td><strong>Parent</strong></td><td>Portail parent : agenda et menus de ses enfants</td></tr>
    </table>

    <h2>Double authentification (recommandé)</h2>
    <p>Dans <strong>Mon Profil</strong>, activez la <strong>double authentification</strong> : à chaque
    connexion, un code à 6 chiffres vous est envoyé par email. Sans ce code, personne ne peut se
    connecter avec votre mot de passe, même s'il a été volé.</p>
    <div class="warning">
        <strong>Sécurité</strong> : choisissez un mot de passe d'au moins 8 caractères, unique pour DailyDesk.
        Ne le notez pas sur un post-it près de l'ordinateur de la mairie !
    </div>
</div>

<!-- 3. Familles et enfants -->
<div class="page">
    <h1>3. Familles et enfants</h1>

    <h2>Créer une famille</h2>
    <div class="step">
        <b>1.</b> Menu <strong>Familles</strong> → bouton <strong>+ Nouvelle famille</strong>.<br>
        <b>2.</b> Renseignez le nom, l'adresse (autocomplétion), le téléphone et l'email.<br>
        <b>3.</b> Ajoutez les parents/tuteurs : prénom, nom, relation, coordonnées. Cochez
        <strong>Contact principal</strong> (un seul par famille), <strong>Autorisé à récupérer l'enfant</strong>
        et <strong>Tuteur légal</strong> si applicable.<br>
        <b>4.</b> Enregistrez, puis ajoutez les enfants depuis la fiche famille.
    </div>

    <h2>La fiche enfant</h2>
    <p>Pour chaque enfant : prénom, nom, date de naissance, école et classe, abonnements
    <strong>cantine</strong> et <strong>garderie</strong>, allergies et remarques médicales.
    Ces informations alimentent automatiquement les listes de pointage des deux modules.</p>

    <h2>Import CSV en masse</h2>
    <div class="step">
        <b>1.</b> Menu <strong>Imports</strong> → téléchargez le <strong>modèle CSV</strong> (familles ou enfants).<br>
        <b>2.</b> Remplissez le fichier (une ligne par famille / enfant), en respectant l'ordre des colonnes.<br>
        <b>3.</b> Déposez le fichier, contrôlez le rapport : lignes créées, ignorées, et le détail des erreurs.<br>
    </div>
    <div class="tip">
        L'import d'enfants reconnaît <strong>l'école et la classe par leur nom</strong> (la casse et les
        accents n'ont pas d'importance) et crée automatiquement les abonnements cantine/garderie
        selon les colonnes. Un ré-import ne crée jamais de doublons.
    </div>
    <div class="warning">
        <strong>RGPD</strong> : chaque fiche famille dispose d'un bouton <strong>Export RGPD</strong>
        (droit d'accès, export JSON complet) et d'une purge définitive (droit à l'effacement) avec
        confirmation renforcée.
    </div>
</div>

<!-- 4. Garderie -->
<div class="page">
    <h1>4. Garderie : pointage quotidien</h1>

    <h2>Pointer les enfants</h2>
    <div class="step">
        <b>1.</b> Menu <strong>Garderie</strong> — la liste affiche les enfants abonnés, groupés par classe.<br>
        <b>2.</b> Pour enregistrer une arrivée : cliquez sur le nom ou utilisez le <strong>pavé alphabétique</strong>
        (recherche par lettre, en haut de page) puis pointez l'enfant — l'heure d'arrivée est horodatée.<br>
        <b>3.</b> Au départ de l'enfant, pointez le <strong>départ</strong> de la même façon : la durée
        totale est calculée automatiquement.<br>
        <b>4.</b> Le panneau <strong>Présences du jour</strong> récapitule les enfants présents, leurs
        horaires et le total d'heures.
    </div>

    <h2>Cas particuliers</h2>
    <ul>
        <li><strong>Enfant non inscrit qui se présente</strong> : il est identifiable dans la liste par un
        badge « Non inscrit » et peut être pointé exceptionnellement — la présence est enregistrée
        sans modifier son abonnement.</li>
        <li><strong>Signalement</strong> : le bouton « Signaler » permet de noter un fait important
        (chute, malaise, conflit) — horodaté et visible dans l'historique.</li>
        <li><strong>Événements</strong> : sorties scolaires, journées à thème — notifiés aux parents concernés.</li>
    </ul>

    <h2>Rapports</h2>
    <p>Le <strong>rapport mensuel</strong> (bouton « Rapport ») totalise par enfant : jours de présence,
    heures totales, moyenne journalière — exportable en CSV/Excel pour la facturation familiale.</p>
</div>

<!-- 5. Cantine -->
<div class="page">
    <h1>5. Cantine : présences et absences</h1>

    <h2>La liste de pointage</h2>
    <p>Menu <strong>Cantine</strong> — tous les enfants abonnés de la classe sont <strong>pré-cochés
    présents</strong> par défaut. Inscrivez simplement les absents du jour :</p>
    <div class="step">
        <b>1.</b> Recherchez l'enfant (recherche ou liste par classe).<br>
        <b>2.</b> Cliquez sur <strong>Absent</strong> — la présence du repas est retirée.<br>
        <b>3.</b> Une erreur ? Cliquez à nouveau pour rétablir la présence.
    </div>

    <h2>Enfants non inscrits et allergies</h2>
    <ul>
        <li>Les enfants <strong>non abonnés</strong> de la classe apparaissent avec un badge gris :
        le bouton <strong>+ Présent</strong> enregistre un repas exceptionnel sans les abonner.</li>
        <li>Les <strong>allergènes</strong> déclarés sur les plats (voir section 6) sont signalés
        sur la fiche de chaque enfant allergique — le cuisinier voit immédiatement les plats à risque.</li>
    </ul>

    <h2>Le goûter</h2>
    <p>Si l'option est activée dans les paramètres, les présences se saisissent séparément pour le
    déjeuner et le goûter.</p>

    <h2>Rapports</h2>
    <p>Le <strong>rapport mensuel</strong> détaille par enfant le nombre de déjeuners et goûters,
    avec les totaux — exportables en CSV/Excel.</p>
</div>

<!-- 6. Menus -->
<div class="page">
    <h1>6. Menus de la cantine</h1>

    <h2>Le catalogue de plats</h2>
    <p>Menu <strong>Cantine → Plats</strong> : construisez votre catalogue une fois pour toutes.
    Chaque plat porte sa catégorie (entrée, plat, légume, dessert...), ses <strong>allergènes</strong>
    (gluten, lactose, fruits à coque...) et son caractère végétarien.</p>

    <h2>Composer un menu</h2>
    <div class="step">
        <b>1.</b> Menu <strong>Cantine → Menus</strong> → <strong>+ Nouveau menu</strong>.<br>
        <b>2.</b> Choisissez la date, sélectionnez les plats du catalogue pour chaque catégorie.<br>
        <b>3.</b> Enregistrez : le menu est <strong>publié par défaut</strong>, donc visible par les
        parents dans leur agenda. Décochez « Publier » pour garder un brouillon.<br>
        <b>4.</b> Les menus peuvent être dupliqués d'une semaine à l'autre.
    </div>
    <div class="tip">
        <strong>Parents allergiques</strong> : le portail parent signale automatiquement sur le menu
        du jour les plats contenant un allergène déclaré pour l'enfant de la famille.
    </div>
</div>

<!-- 7. Portail parent -->
<div class="page">
    <h1>7. Portail parent</h1>
    <p>Les parents consultent leur espace par le même site, après invitation par email
    (fiche famille → <strong>Inviter</strong>). Ils y trouvent :</p>
    <ul>
        <li><strong>Agenda familial</strong> : menus de la cantine et événements (garderie, école)</li>
        <li><strong>Menus de la semaine</strong> publiés par la mairie, avec signalement des allergènes</li>
        <li><strong>Événements</strong> : sorties, repas spécial, réunions</li>
    </ul>
    <p>Chaque parent ne voit <strong>que les informations de ses propres enfants</strong> — aucune
    donnée d'une autre famille n'est accessible.</p>
    <div class="tip">
        Invitez les parents progressivement, famille par famille, plutôt qu'en masse : vous gardez
        la maîtrise du support si certains ont des difficultés à se connecter.
    </div>
</div>

<!-- 8. Site public -->
<div class="page">
    <h1>8. Site public de la commune</h1>
    <p>Le module <strong>Site public</strong> publie le site vitrine de votre commune, accessible à
    tous sans connexion : actualités, agenda, démarches, conseil municipal, écoles, documents
    administratifs, et un formulaire de contact anti-spam.</p>

    <h2>Administrer le site</h2>
    <div class="step">
        <b>1.</b> Menu <strong>Site public</strong> → <strong>Pages</strong> : créez vos pages et composez-les
        avec des blocs (texte, image, actualités, agenda, menus cantine, contact...).<br>
        <b>2.</b> <strong>Actualités</strong> et <strong>Événements</strong> : publiez des contenus datés
        qui apparaissent automatiquement sur la page d'accueil et l'agenda.<br>
        <b>3.</b> Les <strong>messages du formulaire de contact</strong> arrivent dans votre interface
        (Site public → Messages) — réponse par email en un clic.<br>
        <b>4.</b> Personnalisez logo et couleurs dans <strong>Paramètres</strong>.
    </div>
    <div class="warning">
        Pensez aux mentions légales et à l'accessibilité : les pages dédiées sont fournies par défaut,
        à compléter avec vos informations (organisme éditeur, directeur de publication...).
    </div>
</div>

<!-- 9. Stock -->
<div class="page">
    <h1>9. Gestion du stock</h1>
    <p>Le module <strong>Stock</strong> suit les denrées et fournitures de la cantine et de la garderie.</p>
    <div class="step">
        <b>1.</b> <strong>Lieux</strong> : créez vos emplacements (cuisine, réserve, congélateur...).<br>
        <b>2.</b> <strong>Articles</strong> : référencez chaque produit avec son unité et son
        <strong>seuil d'alerte</strong>.<br>
        <b>3.</b> <strong>Mouvements</strong> : chaque entrée (réception) et sortie (consommation)
        est enregistrée avec sa date et sa quantité — le stock courant est toujours à jour.<br>
        <b>4.</b> Quand un article passe sous son seuil, une <strong>alerte email</strong> est envoyée
        aux personnes désignées.
    </div>
    <div class="tip">
        Faites l'inventaire physique une première fois, saisissez-le comme « entrée initiale »,
    puis ne saisissez plus que les mouvements réels : le stock restera juste.
    </div>
</div>

<!-- 10. Rapports -->
<div class="page">
    <h1>10. Rapports et exports</h1>

    <h2>Rapports mensuels</h2>
    <p>Chaque module (garderie, cantine) dispose de son rapport mensuel détaillé par enfant,
    avec totaux et moyenne — exportables en CSV/Excel pour la facturation et la comptabilité.</p>

    <h2>Exports par plage de dates</h2>
    <p>Menu <strong>Exports</strong> : choisissez une période et exportez un récapitulatif agrégé
    par enfant :</p>
    <table>
        <tr><th>Module</th><th>Colonnes de l'export</th></tr>
        <tr><td>Cantine</td><td>Nombre de repas par type (déjeuner / goûter) et total</td></tr>
        <tr><td>Garderie</td><td>Jours de présence, jours complets, total heures, moyenne journalière</td></tr>
    </table>

    <h2>Journal d'activité</h2>
    <p>Toutes les actions sensibles (créations, modifications, suppressions, connexions) sont
    tracées avec l'utilisateur, la date et l'heure — utile en cas de litige ou de contrôle.</p>
</div>

<!-- 11. Paramètres -->
<div class="page">
    <h1>11. Paramètres et modules</h1>

    <h2>Utilisateurs</h2>
    <p>Créez les comptes des agents, attribuez le rôle adapté (voir section 2). Un compte peut être
    désactivé temporairement (congé, départ) sans être supprimé — l'historique est conservé.</p>

    <h2>Modules</h2>
    <p>Activez uniquement ce dont votre mairie a besoin : garderie, cantine, stock, site public...
    Un module désactivé disparaît de la navigation mais ses données sont conservées.</p>

    <h2>Écoles et classes</h2>
    <p>Structurez votre organisation : écoles, puis classes par école. Les listes de pointage et
    les imports s'appuient sur cette structure.</p>

    <h2>Notifications</h2>
    <p>Configurez les alertes email : arrivée/départ garderie, absences, événements, alertes stock.</p>
</div>

<!-- 12. Support -->
<div class="page">
    <h1>12. Aide et support</h1>

    <h2>La bulle d'aide</h2>
    <p>En bas à droite de chaque page : cliquez sur la bulle d'aide pour retrouver l'aide contextuelle
    du module consulté, et le bouton <strong>Créer un ticket</strong> pour contacter le support.</p>

    <h2>Les tickets support</h2>
    <div class="step">
        <b>1.</b> Décrivez votre problème ou votre question dans un ticket.<br>
        <b>2.</b> Suivez la réponse directement dans l'interface (et par email).<br>
        <b>3.</b> La conversation reste tracée — rien ne se perd entre deux interlocuteurs.
    </div>

    <h2>Bonnes pratiques au quotidien</h2>
    <ul>
        <li>Pointez les présents <strong>au moment réel</strong> plutôt qu'en fin de journée</li>
        <li>Un doute sur une manipulation ? Vérifiez d'abord la bulle d'aide de la page</li>
        <li>Signalez immédiatement tout comportement anormal de l'application (ticket support)</li>
        <li>Pensez à désactiver les comptes des agents qui partent de la mairie</li>
    </ul>

    <div class="footer-note">
        DailyDesk — plateforme de gestion municipale &middot; Hébergement France &middot;
        Données protégées (RGPD) &middot; Guide généré le {{ now()->locale('fr')->translatedFormat('d/m/Y') }}
    </div>
</div>

</body>
</html>
