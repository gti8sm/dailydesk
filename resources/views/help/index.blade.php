@extends('layouts.app')

@section('title', 'Aide & Documentation')

@php
    $user = auth()->user();
    $isSuperAdmin = $user && $user->hasRole('super_admin');
    $isAdmin = $user && $user->hasRole('admin_mairie');
    $isParent = $user && $user->hasRole('parent');
@endphp

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="text-center mb-8">
        <div class="mx-auto h-16 w-16 bg-blue-600 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-question text-white text-2xl"></i>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">Aide & Documentation</h1>
        <p class="mt-2 text-sm text-gray-600">Tout ce que vous devez savoir pour utiliser DailyDesk</p>
    </div>

    <div class="space-y-6">
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <button onclick="toggleSection('section-connexion')" class="w-full px-6 py-4 flex items-center justify-between bg-blue-50 hover:bg-blue-100 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-sign-in-alt text-blue-600 text-xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-900">Connexion & Sécurité</h2>
                </div>
                <i class="fas fa-chevron-down text-gray-400" id="icon-section-connexion"></i>
            </button>
            <div id="section-connexion" class="hidden px-6 py-4 space-y-3">
                <p class="text-sm text-gray-700"><strong>Email ou identifiant :</strong> Utilisez votre adresse email ou votre identifiant pour vous connecter.</p>
                <p class="text-sm text-gray-700"><strong>Mot de passe ou PIN :</strong> Vous pouvez utiliser votre mot de passe complet ou votre code PIN (4-6 chiffres).</p>
                <p class="text-sm text-gray-700"><strong>Rester connecté 30 jours :</strong> Cochez cette option pour éviter de vous reconnecter à chaque visite.</p>
                <p class="text-sm text-gray-700"><strong>Mot de passe oublié :</strong> Cliquez sur le lien en bas du formulaire de connexion. Un email vous sera envoyé pour réinitialiser votre mot de passe.</p>
                @if($isAdmin)
                <p class="text-sm text-gray-700"><strong>Whitelist IP :</strong> Les administrateurs peuvent restreindre les connexions à certaines adresses IP.</p>
                @endif
            </div>
        </div>

        @if($isSuperAdmin)
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border-l-4 border-indigo-500">
            <button onclick="toggleSection('section-superadmin')" class="w-full px-6 py-4 flex items-center justify-between bg-indigo-50 hover:bg-indigo-100 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-shield-alt text-indigo-600 text-xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-900">Super Admin — Gestion globale</h2>
                </div>
                <i class="fas fa-chevron-down text-gray-400" id="icon-section-superadmin"></i>
            </button>
            <div id="section-superadmin" class="hidden px-6 py-4 space-y-3">
                <p class="text-sm text-gray-700"><strong>Tenants :</strong> Créez et gérez les mairies (tenants). Chaque tenant a son propre domaine, ses utilisateurs et ses données. La recherche INSEE remplit automatiquement le code INSEE et la population, et suggère le plan adapté.</p>
                <p class="text-sm text-gray-700"><strong>Plans d'abonnement :</strong> Les plans sont définis par tranche de population (Village, Petite commune, Commune moyenne, Grande commune, Agglomération). Tous les modules sont inclus dans chaque plan — la différenciation se fait sur le support et la taille de commune.</p>
                <p class="text-sm text-gray-700"><strong>Modules :</strong> Activez ou désactivez les modules (Garderie, Cantine, Stock) par tenant. Configurez les paramètres globaux (horaires garderie, mode cantine, seuils stock).</p>
                <p class="text-sm text-gray-700"><strong>Journal d'activité :</strong> Consultez toutes les actions effectuées sur la plateforme. Filtrez par tenant, module, action, utilisateur ou date.</p>
                <p class="text-sm text-gray-700"><strong>Impersonation :</strong> Connectez-vous temporairement en tant qu'admin d'un tenant pour le dépanner. Utilisez "Retour Super Admin" pour revenir.</p>
                <p class="text-sm text-gray-700"><strong>Statistiques :</strong> Vue d'ensemble des tenants actifs, revenus, croissance mensuelle et répartition par plan.</p>
            </div>
        </div>
        @endif

        @can('view_garderie')
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <button onclick="toggleSection('section-garderie')" class="w-full px-6 py-4 flex items-center justify-between bg-blue-50 hover:bg-blue-100 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-child text-blue-600 text-xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-900">Module Garderie</h2>
                </div>
                <i class="fas fa-chevron-down text-gray-400" id="icon-section-garderie"></i>
            </button>
            <div id="section-garderie" class="hidden px-6 py-4 space-y-3">
                <p class="text-sm text-gray-700"><strong>Enregistrer une présence :</strong> Cliquez sur un enfant puis sur le bouton "Arrivée" ou "Départ". L'heure est enregistrée automatiquement.</p>
                <p class="text-sm text-gray-700"><strong>Filtrer par date :</strong> Utilisez le sélecteur de date en haut pour consulter l'historique des présences.</p>
                <p class="text-sm text-gray-700"><strong>Événements :</strong> Signalez un incident, un accident ou un comportement particulier via l'onglet "Événements".</p>
                @can('manage_settings')
                <p class="text-sm text-gray-700"><strong>Notifications :</strong> Les parents peuvent être notifiés automatiquement (configurable dans Paramètres).</p>
                @endcan
            </div>
        </div>
        @endcan

        @can('view_cantine')
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <button onclick="toggleSection('section-cantine')" class="w-full px-6 py-4 flex items-center justify-between bg-orange-50 hover:bg-orange-100 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-utensils text-orange-600 text-xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-900">Module Cantine</h2>
                </div>
                <i class="fas fa-chevron-down text-gray-400" id="icon-section-cantine"></i>
            </button>
            <div id="section-cantine" class="hidden px-6 py-4 space-y-3">
                <p class="text-sm text-gray-700"><strong>Présences cantine :</strong> Marquez chaque enfant comme présent ou absent pour le repas. Les enfants affichés sont filtrés selon votre école. <em>Saisie assurée par les agents ALSH (profil garderie).</em></p>
                <p class="text-sm text-gray-700"><strong>Type de repas :</strong> Choisissez entre "Midi" et "Goûter" selon le moment de la journée.</p>
                <p class="text-sm text-gray-700"><strong>Allergies & régimes :</strong> Les informations sur les allergies sont affichées à côté de chaque enfant.</p>
                <p class="text-sm text-gray-700"><strong>Menus :</strong> Le cuisinier ou l'admin crée les menus (entrée, plat, garniture, dessert). Les menus publiés sont visibles par les parents sur leur agenda. <em>Gestion réservée au profil Cuisinier / Gestion menus.</em></p>
                <p class="text-sm text-gray-700"><strong>Mode de gestion des menus :</strong> En mode "global", un seul menu sert toutes les écoles. En mode "par école", chaque école a son propre menu (configurable dans Paramètres globaux).</p>
                <div class="mt-3 p-3 bg-orange-50 rounded-lg border border-orange-200">
                    <p class="text-xs text-orange-800"><strong>Répartition des rôles cantine :</strong></p>
                    <ul class="mt-2 space-y-1 text-xs text-orange-700">
                        <li><i class="fas fa-child mr-1"></i> <strong>Agent ALSH</strong> : saisie des présences + événements garderie/cantine</li>
                        <li><i class="fas fa-utensils mr-1"></i> <strong>Cuisinier</strong> : création et publication des menus uniquement</li>
                    </ul>
                </div>
            </div>
        </div>
        @endcan

        @hasrole('admin_mairie')
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <button onclick="toggleSection('section-ecoles')" class="w-full px-6 py-4 flex items-center justify-between bg-blue-50 hover:bg-blue-100 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-school text-blue-600 text-xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-900">Écoles & Multi-sites</h2>
                </div>
                <i class="fas fa-chevron-down text-gray-400" id="icon-section-ecoles"></i>
            </button>
            <div id="section-ecoles" class="hidden px-6 py-4 space-y-3">
                <p class="text-sm text-gray-700"><strong>Écoles :</strong> Créez et gérez les écoles de votre commune (menu Gestion → Écoles). Chaque école a un nom, un type (maternelle, élémentaire, primaire, collège) et une adresse.</p>
                <p class="text-sm text-gray-700"><strong>Classes par école :</strong> Chaque classe est rattachée à une école. Les enfants sont également rattachés à une école directement.</p>
                <p class="text-sm text-gray-700"><strong>Sélecteur d'école :</strong> Dans le header, un sélecteur permet de basculer entre les écoles. Les admins globaux voient "Toutes les écoles" + chaque école. Les agents limités ne voient que leur école.</p>
                <p class="text-sm text-gray-700"><strong>Attribution d'un agent à une école :</strong> Dans Utilisateurs → Modifier, définissez l'"École de rattachement". L'agent ne verra que les données de son école (enfants, présences, menus).</p>
                <p class="text-sm text-gray-700"><strong>Remplacements :</strong> Dans le formulaire utilisateur, cochez les "Écoles de remplacement" pour autoriser un agent à accéder à d'autres écoles temporairement. L'agent pourra basculer entre ses écoles via le sélecteur du header.</p>
                <p class="text-sm text-gray-700"><strong>École par défaut :</strong> Une "École principale" est créée automatiquement à l'installation. Pour les communes multi-écoles, créez les écoles supplémentaires via le CRUD.</p>
            </div>
        </div>
        @endhasrole

        @can('view_stock')
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <button onclick="toggleSection('section-stock')" class="w-full px-6 py-4 flex items-center justify-between bg-indigo-50 hover:bg-indigo-100 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-boxes-stacked text-indigo-600 text-xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-900">Module Stock</h2>
                </div>
                <i class="fas fa-chevron-down text-gray-400" id="icon-section-stock"></i>
            </button>
            <div id="section-stock" class="hidden px-6 py-4 space-y-3">
                <p class="text-sm text-gray-700"><strong>Lieux de stockage :</strong> Créez des lieux (cuisine, réserve, cellier) et suivez les quantités par article.</p>
                <p class="text-sm text-gray-700"><strong>Mouvements :</strong> Enregistrez les entrées et sorties de stock. Chaque mouvement est horodaté et tracé.</p>
                <p class="text-sm text-gray-700"><strong>Seuils d'alerte :</strong> Définissez un seuil minimum par article. En dessous du seuil, une alerte est générée (si activé dans Paramètres globaux).</p>
                <p class="text-sm text-gray-700"><strong>Permissions :</strong> Les droits stock sont attribués individuellement par utilisateur (Voir, Saisir, Gérer) dans le formulaire Utilisateur.</p>
            </div>
        </div>
        @endcan

        @can('manage_families')
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <button onclick="toggleSection('section-familles')" class="w-full px-6 py-4 flex items-center justify-between bg-green-50 hover:bg-green-100 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-users text-green-600 text-xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-900">Gestion des Familles</h2>
                </div>
                <i class="fas fa-chevron-down text-gray-400" id="icon-section-familles"></i>
            </button>
            <div id="section-familles" class="hidden px-6 py-4 space-y-3">
                <p class="text-sm text-gray-700"><strong>Créer une famille :</strong> Renseignez le nom de famille, les coordonnées et les contacts.</p>
                <p class="text-sm text-gray-700"><strong>Ajouter un enfant :</strong> Depuis la fiche famille, ajoutez les enfants avec leur date de naissance, classe et informations médicales.</p>
                <p class="text-sm text-gray-700"><strong>Importer en masse :</strong> Utilisez un fichier CSV pour importer plusieurs famles à la fois.</p>
                <p class="text-sm text-gray-700"><strong>Invitations :</strong> Envoyez une invitation par email aux parents pour qu'ils créent leur compte et accèdent à leur espace.</p>
            </div>
        </div>
        @endcan

        @hasrole('parent')
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <button onclick="toggleSection('section-parent')" class="w-full px-6 py-4 flex items-center justify-between bg-purple-50 hover:bg-purple-100 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-home text-purple-600 text-xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-900">Espace Parent</h2>
                </div>
                <i class="fas fa-chevron-down text-gray-400" id="icon-section-parent"></i>
            </button>
            <div id="section-parent" class="hidden px-6 py-4 space-y-3">
                <p class="text-sm text-gray-700"><strong>Tableau de bord :</strong> Visualisez les présences de vos enfants à la garderie et la cantine.</p>
                <p class="text-sm text-gray-700"><strong>Signalements :</strong> Signalez un changement (allergie, régime alimentaire, information médicale) via l'onglet dédié.</p>
                <p class="text-sm text-gray-700"><strong>Notifications :</strong> Recevez des notifications lors des arrivées/départs de vos enfants (si activé par la mairie).</p>
            </div>
        </div>
        @endhasrole

        @hasrole('admin_mairie')
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <button onclick="toggleSection('section-admin')" class="w-full px-6 py-4 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-cog text-gray-600 text-xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-900">Administration</h2>
                </div>
                <i class="fas fa-chevron-down text-gray-400" id="icon-section-admin"></i>
            </button>
            <div id="section-admin" class="hidden px-6 py-4 space-y-3">
                <p class="text-sm text-gray-700"><strong>Utilisateurs :</strong> Créez et gérez les comptes utilisateurs. Attribuez des rôles (admin, personnel, enseignant, agent ALSH, cuisinier, parent), une école de rattachement et des écoles de remplacement.</p>
                <p class="text-sm text-gray-700"><strong>Écoles :</strong> Créez les écoles de votre commune (menu Gestion → Écoles). Chaque école regroupe des classes et des enfants.</p>
                <p class="text-sm text-gray-700"><strong>Classes :</strong> Créez les classes scolaires par année. Associez les classes à une école et les enfants à leur classe.</p>
                <p class="text-sm text-gray-700"><strong>Paramètres :</strong> Configurez les horaires de garderie, le mode de gestion des menus cantine (global ou par école), le SMTP pour les emails, les seuils de stock et les notifications.</p>
                <p class="text-sm text-gray-700"><strong>Imports/Exports :</strong> Importez des familles en CSV, exportez les présences en Excel/PDF.</p>
                <p class="text-sm text-gray-700"><strong>Onboarding :</strong> Au premier lancement, un assistant en 4 étapes vous guide : informations de la mairie, création des classes, personnalisation (couleurs, logo), notifications. Vous pouvez le skipper et le reprendre plus tard.</p>
            </div>
        </div>
        @endhasrole

        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <button onclick="toggleSection('section-support')" class="w-full px-6 py-4 flex items-center justify-between bg-orange-50 hover:bg-orange-100 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-life-ring text-orange-600 text-xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-900">Support & Demandes d'aide</h2>
                </div>
                <i class="fas fa-chevron-down text-gray-400" id="icon-section-support"></i>
            </button>
            <div id="section-support" class="hidden px-6 py-4 space-y-3">
                @if($isSuperAdmin)
                <p class="text-sm text-gray-700"><strong>Tickets de support :</strong> Consultez et répondez aux demandes des utilisateurs depuis le menu Support. Les tickets sont organisés par statut (Ouvert, En cours, Résolu) et par archive.</p>
                <p class="text-sm text-gray-700"><strong>Badge de notification :</strong> Le chiffre rouge à côté de "Support" dans le menu indique le nombre de tickets <strong>non lus</strong> (nouveau ticket ou nouvelle réponse de l'utilisateur). Le badge disparaît dès que vous ouvrez le ticket.</p>
                <p class="text-sm text-gray-700"><strong>Archivage :</strong> Archivez manuellement un ticket (bouton "Archiver" sur la fiche ou dans la liste). Les tickets archivés sont conservés pour historique et visibles via le filtre "Archivés". Désarchivez un ticket pour le rouvrir.</p>
                <p class="text-sm text-gray-700"><strong>Auto-archivage :</strong> Quand un tenant est suspendu ou supprimé, ses tickets ouverts sont automatiquement archivés. Le nom du tenant reste visible dans la liste des tickets archivés.</p>
                <p class="text-sm text-gray-700"><strong>Indicateur "Non lu" :</strong> Les tickets non lus apparaissent en surbrillance rouge dans la liste, avec un point rouge à côté du sujet. Le compteur "Non lus" en haut de la page récapitule le total.</p>
                @else
                <p class="text-sm text-gray-700"><strong>Créer un ticket :</strong> Cliquez sur "Nouveau ticket" dans la section Support. Décrivez votre demande, choisissez une catégorie (général, technique, facturation, suggestion, bug) et une priorité. Vous pouvez joindre des captures d'écran ou documents.</p>
                <p class="text-sm text-gray-700"><strong>Suivre vos tickets :</strong> Consultez vos tickets et les réponses de l'équipe support depuis la page Support. Les tickets avec une nouvelle réponse du support apparaissent en surbrillance orange avec un point orange.</p>
                <p class="text-sm text-gray-700"><strong>Badge de notification :</strong> Un chiffre orange à côté de "Support" dans le menu indique qu'une nouvelle réponse a été apportée à l'un de vos tickets. Le badge disparaît dès que vous ouvrez le ticket.</p>
                <p class="text-sm text-gray-700"><strong>Répondre :</strong> Ajoutez un commentaire sur votre ticket pour répondre à l'équipe support ou apporter des précisions. Si le ticket était résolu, il sera automatiquement rouvert.</p>
                <p class="text-sm text-gray-700"><strong>Catégories :</strong> Général (question diverse), Technique (bug, erreur), Facturation (abonnement, paiement), Suggestion (demande de fonctionnalité), Bug (signalement d'anomalie).</p>
                @endif
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <button onclick="toggleSection('section-roles')" class="w-full px-6 py-4 flex items-center justify-between bg-purple-50 hover:bg-purple-100 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-id-badge text-purple-600 text-xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-900">Rôles & Permissions</h2>
                </div>
                <i class="fas fa-chevron-down text-gray-400" id="icon-section-roles"></i>
            </button>
            <div id="section-roles" class="hidden px-6 py-4 space-y-3">
                <p class="text-sm text-gray-700">Chaque utilisateur a un ou plusieurs rôles qui déterminent ses accès. Voici le détail de chaque profil :</p>
                <div class="space-y-2 mt-3">
                    <div class="flex items-start p-2 bg-red-50 rounded">
                        <i class="fas fa-user-shield text-red-600 mt-1 mr-2"></i>
                        <div><strong class="text-sm text-gray-900">Admin / Admin mairie</strong> <span class="text-xs text-gray-500">(admin, admin_mairie)</span><p class="text-xs text-gray-600 mt-1">Accès complet : utilisateurs, écoles, classes, familles, paramètres, imports/exports. Recommandé : maire, secrétaire de mairie.</p></div>
                    </div>
                    <div class="flex items-start p-2 bg-blue-50 rounded">
                        <i class="fas fa-user-clock text-blue-600 mt-1 mr-2"></i>
                        <div><strong class="text-sm text-gray-900">Personnel de mairie</strong> <span class="text-xs text-gray-500">(personnel_mairie)</span><p class="text-xs text-gray-600 mt-1">Saisie des présences garderie + cantine, consultation de l'historique. Recommandé : agent d'accueil.</p></div>
                    </div>
                    <div class="flex items-start p-2 bg-green-50 rounded">
                        <i class="fas fa-chalkboard-teacher text-green-600 mt-1 mr-2"></i>
                        <div><strong class="text-sm text-gray-900">Enseignant</strong> <span class="text-xs text-gray-500">(enseignant)</span><p class="text-xs text-gray-600 mt-1">Création d'événements sur ses élèves. Recommandé : instituteur, professeur des écoles.</p></div>
                    </div>
                    <div class="flex items-start p-2 bg-purple-50 rounded">
                        <i class="fas fa-child text-purple-600 mt-1 mr-2"></i>
                        <div><strong class="text-sm text-gray-900">Agent ALSH / Garderie</strong> <span class="text-xs text-gray-500">(alsh)</span><p class="text-xs text-gray-600 mt-1">Présences garderie + cantine, événements, exports. Recommandé : animateur ALSH, agent polyvalent.</p></div>
                    </div>
                    <div class="flex items-start p-2 bg-orange-50 rounded">
                        <i class="fas fa-utensils text-orange-600 mt-1 mr-2"></i>
                        <div><strong class="text-sm text-gray-900">Cuisinier / Gestion menus</strong> <span class="text-xs text-gray-500">(cantine)</span><p class="text-xs text-gray-600 mt-1">Création et publication des menus cantine uniquement. Recommandé : cuisinier, responsable restauration.</p></div>
                    </div>
                    <div class="flex items-start p-2 bg-indigo-50 rounded">
                        <i class="fas fa-home text-indigo-600 mt-1 mr-2"></i>
                        <div><strong class="text-sm text-gray-900">Parent</strong> <span class="text-xs text-gray-500">(parent)</span><p class="text-xs text-gray-600 mt-1">Portail parent : consultation des présences de ses enfants, menus, signalements. Recommandé : parents des enfants inscrits.</p></div>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2"><i class="fas fa-info-circle mr-1"></i> Un utilisateur peut avoir plusieurs rôles (ex: un agent qui gère la garderie et la cantine). Les permissions stock (Voir, Saisir, Gérer) sont attribuées individuellement en plus du rôle.</p>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <button onclick="toggleSection('section-abonnements')" class="w-full px-6 py-4 flex items-center justify-between bg-green-50 hover:bg-green-100 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-tags text-green-600 text-xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-900">Abonnements & Tarification</h2>
                </div>
                <i class="fas fa-chevron-down text-gray-400" id="icon-section-abonnements"></i>
            </button>
            <div id="section-abonnements" class="hidden px-6 py-4 space-y-3">
                <p class="text-sm text-gray-700">La tarification de DailyDesk est basée sur la <strong>population de la commune</strong> (données INSEE), pas sur le nombre de modules. Tous les modules sont inclus dans chaque plan.</p>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-gray-600">Plan</th>
                                <th class="px-3 py-2 text-left text-gray-600">Population</th>
                                <th class="px-3 py-2 text-left text-gray-600">Prix/mois</th>
                                <th class="px-3 py-2 text-left text-gray-600">Support</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr><td class="px-3 py-2 font-medium">Village</td><td class="px-3 py-2">< 1 000 hab.</td><td class="px-3 py-2">39 €</td><td class="px-3 py-2 text-xs">Email (48h)</td></tr>
                            <tr><td class="px-3 py-2 font-medium">Petite commune</td><td class="px-3 py-2">1 000 - 4 999</td><td class="px-3 py-2">79 €</td><td class="px-3 py-2 text-xs">Email + tel (24h)</td></tr>
                            <tr><td class="px-3 py-2 font-medium">Commune moyenne</td><td class="px-3 py-2">5 000 - 19 999</td><td class="px-3 py-2">149 €</td><td class="px-3 py-2 text-xs">Prioritaire (J)</td></tr>
                            <tr><td class="px-3 py-2 font-medium">Grande commune</td><td class="px-3 py-2">20 000 - 99 999</td><td class="px-3 py-2">299 €</td><td class="px-3 py-2 text-xs">Dédié 7j/7</td></tr>
                            <tr><td class="px-3 py-2 font-medium">Agglomération</td><td class="px-3 py-2">100 000+</td><td class="px-3 py-2">Sur devis</td><td class="px-3 py-2 text-xs">Sur mesure</td></tr>
                        </tbody>
                    </table>
                </div>
                <p class="text-sm text-gray-700"><strong>Tous les modules inclus :</strong> Garderie, Cantine, Stock et les futurs modules. Les enfants et utilisateurs sont illimités (la population est le facteur limitant naturel).</p>
                <p class="text-sm text-gray-700"><strong>Activation/Désactivation des modules :</strong> Le tenant peut activer ou désactiver individuellement chaque module selon ses besoins, indépendamment du plan.</p>
                @if($isSuperAdmin)
                <p class="text-sm text-gray-700"><strong>Recherche INSEE :</strong> À la création d'un tenant, tapez le nom de la commune — le code INSEE et la population sont récupérés automatiquement via l'API geo.api.gouv.fr, et le plan adapté est suggéré.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-8 text-center">
        <p class="text-sm text-gray-500">
            <i class="fas fa-info-circle mr-1"></i>
            Besoin d'aide supplémentaire ?
            @if($isSuperAdmin)
            Consultez les tickets de support ou la documentation interne.
            @else
            Ouvrez un ticket via le menu Support, ou contactez votre administrateur.
            @endif
        </p>
    </div>
</div>

<script>
function toggleSection(id) {
    const section = document.getElementById(id);
    const icon = document.getElementById('icon-' + id);
    section.classList.toggle('hidden');
    icon.classList.toggle('fa-chevron-down');
    icon.classList.toggle('fa-chevron-up');
}
</script>
@endsection
