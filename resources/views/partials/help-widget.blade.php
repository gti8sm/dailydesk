@php
    $routeName = request()->route() ? request()->route()->getName() : '';
    $user = auth()->user();
    $isSuperAdmin = $user && $user->hasRole('super_admin');
    $isAdmin = $user && $user->hasRole('admin_mairie');
    $isParent = $user && $user->hasRole('parent');

    $helpContent = [
        'login' => [
            'title' => 'Connexion',
            'text' => 'Connectez-vous avec votre email ou identifiant et votre mot de passe ou code PIN (4-6 chiffres).',
            'tips' => ['Cochez "Rester connecté 30 jours" pour éviter de vous reconnecter', 'Mot de passe oublié ? Cliquez sur le lien en bas du formulaire'],
            'roles' => null, // accessible à tous
        ],
    ];

    if ($isSuperAdmin) {
        $helpContent['dashboard'] = [
            'title' => 'Dashboard Super Admin',
            'text' => 'Gérez tous les tenants, plans d\'abonnement, modules et statistiques globales.',
            'tips' => ['Utilisez "Modules" pour activer/désactiver des modules par tenant', 'Consultez le "Journal d\'activité" pour tracer les actions'],
            'roles' => ['super_admin'],
        ];
        $helpContent['central.tenants.index'] = [
            'title' => 'Tenants',
            'text' => 'Gérez les mairies (tenants) : création, modification, activation, impersonation.',
            'tips' => ['Cliquez sur "Créer" pour ajouter une nouvelle mairie', 'Utilisez l\'impersonation pour vous connecter en tant qu\'admin d\'un tenant'],
            'roles' => ['super_admin'],
        ];
        $helpContent['central.plans.index'] = [
            'title' => 'Plans d\'abonnement',
            'text' => 'Les plans sont définis par tranche de population (Village, Petite commune, Commune moyenne, Grande commune, Agglomération). Tous les modules sont inclus dans chaque plan.',
            'tips' => ['Le plan est suggéré automatiquement selon la population INSEE du tenant', 'Le prix et le niveau de support évoluent avec la taille de la commune'],
            'roles' => ['super_admin'],
        ];
        $helpContent['central.modules.overview'] = [
            'title' => 'Gestion des modules',
            'text' => 'Activez ou désactivez les modules (Garderie, Cantine, Stock) pour chaque tenant.',
            'tips' => ['Cliquez sur le bouton toggle pour activer/désactiver un module', 'Configurez les paramètres globaux (horaires garderie, mode cantine, seuils stock) via le bouton "Paramètres globaux"'],
            'roles' => ['super_admin'],
        ];
        $helpContent['central.logs.index'] = [
            'title' => 'Journal d\'activité',
            'text' => 'Consultez toutes les actions effectuées sur la plateforme (connexions, créations, modifications, suppressions).',
            'tips' => ['Filtrez par tenant, action ou date', 'Cliquez sur une entrée pour voir le détail des changements'],
            'roles' => ['super_admin'],
        ];
        $helpContent['central.statistics'] = [
            'title' => 'Statistiques globales',
            'text' => 'Vue d\'ensemble des statistiques de la plateforme : tenants actifs, revenus, croissance.',
            'tips' => ['Consultez la répartition par plan d\'abonnement', 'Suivez la croissance mensuelle'],
            'roles' => ['super_admin'],
        ];
    } else {
        $helpContent['dashboard'] = [
            'title' => 'Tableau de bord',
            'text' => 'Bienvenue sur votre tableau de bord. Vous y retrouvez les statistiques et accès rapides.',
            'tips' => ['Utilisez le menu en haut pour naviguer entre les modules', 'Cliquez sur votre nom en haut à droite pour accéder aux paramètres'],
            'roles' => null,
        ];

        if ($user && $user->can('view_garderie')) {
            $helpContent['garderie.index'] = [
                'title' => 'Garderie',
                'text' => 'Gérez les présences des enfants à la garderie (matin et soir).',
                'tips' => ['Cliquez sur un enfant pour enregistrer son arrivée ou son départ', 'Utilisez le filtre date pour voir l\'historique'],
                'roles' => null,
            ];
        }

        if ($user && $user->can('view_cantine')) {
            $helpContent['cantine.index'] = [
                'title' => 'Cantine',
                'text' => 'Gérez les présences des enfants à la cantine et le type de repas. Les enfants affichés sont filtrés selon votre école.',
                'tips' => ['Marquez les présents/absents d\'un clic', 'Consultez les événements (allergies, refus) dans l\'onglet dédié', 'La saisie des présences est assurée par les agents ALSH'],
                'roles' => null,
            ];
            $helpContent['cantine.menus.index'] = [
                'title' => 'Menus cantine',
                'text' => 'Créez et publiez les menus (entrée, plat, garniture, dessert). Les menus publiés sont visibles par les parents sur leur agenda.',
                'tips' => ['En mode "par école", chaque école a son propre menu', 'En mode "global", un seul menu sert toutes les écoles', 'Le mode est configurable dans Paramètres globaux', 'Réservé au profil Cuisinier / Gestion menus'],
                'roles' => null,
            ];
        }

        if ($user && $user->can('view_stock')) {
            $helpContent['stock.items.index'] = [
                'title' => 'Stock',
                'text' => 'Gérez les lieux de stockage, les articles et les mouvements (entrées/sorties).',
                'tips' => ['Définissez un seuil minimum par article pour générer des alertes', 'Les droits stock sont attribués individuellement par utilisateur'],
                'roles' => null,
            ];
        }

        if ($user && $user->can('manage_families')) {
            $helpContent['families.index'] = [
                'title' => 'Familles',
                'text' => 'Gérez les familles inscrites : coordonnées, enfants, contacts.',
                'tips' => ['Cliquez sur une famille pour voir le détail', 'Utilisez le bouton "Importer" pour ajouter plusieurs familles en masse'],
                'roles' => null,
            ];
        }

        if ($isAdmin) {
            $helpContent['schools.index'] = [
                'title' => 'Écoles',
                'text' => 'Créez et gérez les écoles de votre commune. Chaque école regroupe des classes et des enfants.',
                'tips' => ['Une "École principale" est créée automatiquement à l\'installation', 'Pour les communes multi-écoles, créez les écoles supplémentaires ici'],
                'roles' => ['admin_mairie'],
            ];
            $helpContent['classes.index'] = [
                'title' => 'Classes',
                'text' => 'Gérez les classes scolaires et leurs enseignants. Chaque classe est rattachée à une école.',
                'tips' => ['Créez une classe par année scolaire', 'Associez les enfants à leur classe lors de leur inscription'],
                'roles' => ['admin_mairie'],
            ];
            $helpContent['users.index'] = [
                'title' => 'Utilisateurs',
                'text' => 'Gérez les comptes utilisateurs, leurs rôles, leur école de rattachement et leurs écoles de remplacement.',
                'tips' => ['Attribuez une école de rattachement pour limiter un agent à une école', 'Cochez les "Écoles de remplacement" pour autoriser un agent à accéder à d\'autres écoles (remplacements)', 'Attribuez un code PIN pour un accès rapide'],
                'roles' => ['admin_mairie'],
            ];
        }

        if ($user && $user->can('manage_settings')) {
            $helpContent['settings.index'] = [
                'title' => 'Paramètres',
                'text' => 'Configurez les paramètres de votre instance : horaires garderie, mode de gestion des menus cantine, SMTP, notifications, seuils de stock.',
                'tips' => ['Les horaires de garderie définissent les plages par défaut', 'Le mode cantine "par école" permet un menu différent par école', 'Configurez le SMTP pour activer les notifications email'],
                'roles' => null,
            ];
        }

        // Support (tenant)
        $helpContent['support.index'] = [
            'title' => 'Support',
            'text' => 'Consultez vos tickets de support et les réponses de l\'équipe.',
            'tips' => ['Les tickets avec un point orange ont une nouvelle réponse non lue', 'Créez un nouveau ticket via le bouton "Nouveau ticket"', 'Si un ticket résolu reçoit votre commentaire, il est automatiquement rouvert'],
            'roles' => null,
        ];
        $helpContent['support.create'] = [
            'title' => 'Nouveau ticket',
            'text' => 'Décrivez votre demande à l\'équipe support. Plus le ticket est précis, plus rapide sera la réponse.',
            'tips' => ['Joignez une capture d\'écran si vous signalez un bug', 'Choisissez la catégorie la plus pertinente', 'La priorité "Urgent" est réservée aux blocages complets'],
            'roles' => null,
        ];
    }

    // Support (super admin)
    if ($user && $user->hasRole('super_admin')) {
        $helpContent['central.support.index'] = [
            'title' => 'Tickets de support',
            'text' => 'Gérez les demandes de support des utilisateurs. Les tickets non lus sont mis en évidence en rouge.',
            'tips' => ['Le badge rouge dans le menu indique les tickets non lus (nouveau ou nouvelle réponse)', 'Archivez les tickets résolus ou obsolètes', 'Les tickets des tenants suspendus/supprimés sont auto-archivés'],
            'roles' => null,
        ];
    }

    if ($isParent) {
        $helpContent['parent.dashboard'] = [
            'title' => 'Mon Espace Parent',
            'text' => 'Suivez les présences et événements de vos enfants à la garderie et la cantine.',
            'tips' => ['Consultez l\'historique des présences', 'Signalez un changement (allergie, régime) via l\'onglet Signalements'],
            'roles' => ['parent'],
        ];
        $helpContent['parent.events'] = [
            'title' => 'Signalements',
            'text' => 'Signalez un changement important concernant votre enfant (allergie, régime, information médicale).',
            'tips' => ['Soyez précis dans votre description', 'Un membre de l\'équipe prendra en compte votre signalement'],
            'roles' => ['parent'],
        ];
    }

    $currentHelp = $helpContent[$routeName] ?? [
        'title' => 'Besoin d\'aide ?',
        'text' => 'Cliquez sur le bouton ci-dessous pour accéder à l\'aide complète.',
        'tips' => [],
    ];
@endphp

<div id="help-widget" class="fixed bottom-6 right-6 z-50">
    <div id="help-bubble" class="hidden absolute bottom-20 right-0 w-80 bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden transition-all duration-300 opacity-0 transform translate-y-2">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-5 py-4 flex items-center justify-between">
            <div class="flex items-center">
                <div class="bg-white bg-opacity-20 rounded-full w-10 h-10 flex items-center justify-center mr-3">
                    <i class="fas fa-question text-lg"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-sm">{{ $currentHelp['title'] }}</h3>
                    <p class="text-xs text-blue-100">Assistant DailyDesk</p>
                </div>
            </div>
            <button onclick="closeHelpBubble()" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full w-8 h-8 flex items-center justify-center transition-colors">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <div class="p-5 space-y-3">
            <p class="text-sm text-gray-700">{{ $currentHelp['text'] }}</p>

            @if(!empty($currentHelp['tips']))
            <div class="space-y-2">
                @foreach($currentHelp['tips'] as $tip)
                <div class="flex items-start bg-blue-50 rounded-lg p-3">
                    <i class="fas fa-lightbulb text-yellow-500 text-sm mt-0.5 mr-2"></i>
                    <p class="text-xs text-gray-600">{!! $tip !!}</p>
                </div>
                @endforeach
            </div>
            @endif

            <div class="pt-2 border-t border-gray-100">
                @unless(auth()->user()->hasRole('super_admin'))
                <a href="#" onclick="window.open('{{ route('help') }}', '_blank', 'width=800,height=600'); return false;"
                   class="flex items-center justify-center w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-book mr-2"></i>Aide complète
                </a>
                @endunless
            </div>
        </div>

        <div class="absolute -bottom-2 right-6 w-4 h-4 bg-white border-r border-b border-gray-200 transform rotate-45"></div>
    </div>

    <button id="help-button" onclick="toggleHelpBubble()"
            class="bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-full w-14 h-14 shadow-lg hover:shadow-xl flex items-center justify-center transition-all duration-300 hover:scale-110 group">
        <i class="fas fa-question text-xl group-hover:scale-110 transition-transform"></i>
        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold" id="help-badge">!</span>
    </button>
</div>

<script>
(function() {
    const STORAGE_KEY = 'help_widget_dismissed';
    const badge = document.getElementById('help-badge');

    const dismissed = localStorage.getItem(STORAGE_KEY);
    if (dismissed) {
        badge.style.display = 'none';
    }

    window.toggleHelpBubble = function() {
        const bubble = document.getElementById('help-bubble');
        if (bubble.classList.contains('hidden')) {
            bubble.classList.remove('hidden');
            requestAnimationFrame(() => {
                bubble.classList.remove('opacity-0', 'translate-y-2');
            });
            badge.style.display = 'none';
            localStorage.setItem(STORAGE_KEY, '1');
        } else {
            closeHelpBubble();
        }
    };

    window.closeHelpBubble = function() {
        const bubble = document.getElementById('help-bubble');
        bubble.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => {
            bubble.classList.add('hidden');
        }, 300);
    };

    document.addEventListener('click', function(e) {
        const widget = document.getElementById('help-widget');
        if (widget && !widget.contains(e.target)) {
            closeHelpBubble();
        }
    });

    setTimeout(function() {
        if (!localStorage.getItem(STORAGE_KEY)) {
            const bubble = document.getElementById('help-bubble');
            bubble.classList.remove('hidden');
            requestAnimationFrame(() => {
                bubble.classList.remove('opacity-0', 'translate-y-2');
            });
            setTimeout(function() {
                closeHelpBubble();
            }, 5000);
        }
    }, 2000);
})();
</script>
