@php
/**
 * Partage des descriptions de rôles pour les formulaires utilisateur.
 * Utilisé dans users/create.blade.php et users/edit.blade.php.
 */
$roleDescriptions = [
    'admin' => [
        'icon' => 'fa-user-shield',
        'color' => 'text-red-600',
        'bg' => 'bg-red-50',
        'label' => 'Administrateur',
        'short' => 'Accès complet à toutes les fonctionnalités',
        'details' => [
            'Gère les utilisateurs, écoles, classes, familles et paramètres',
            'Voit toutes les écoles (sauf si une école de rattachement est définie)',
            'Peut importer/exporter des données',
            'Recommandé : maire, secrétaire de mairie, directeur général des services',
        ],
    ],
    'admin_mairie' => [
        'icon' => 'fa-user-tie',
        'color' => 'text-red-600',
        'bg' => 'bg-red-50',
        'label' => 'Admin mairie',
        'short' => 'Administration du tenant (équivalent admin)',
        'details' => [
            'Même accès que admin',
            'Profil utilisé pour l\'onboarding initial',
        ],
    ],
    'personnel_mairie' => [
        'icon' => 'fa-user-clock',
        'color' => 'text-blue-600',
        'bg' => 'bg-blue-50',
        'label' => 'Personnel de mairie',
        'short' => 'Saisie des présences (garderie + cantine)',
        'details' => [
            'Enregistre les arrivées/départs en garderie',
            'Marque les présents/absents en cantine',
            'Consulte l\'historique des présences',
            'Ne gère pas les utilisateurs ni les paramètres',
            'Recommandé : agent d\'accueil, stagiaire',
        ],
    ],
    'enseignant' => [
        'icon' => 'fa-chalkboard-teacher',
        'color' => 'text-green-600',
        'bg' => 'bg-green-50',
        'label' => 'Enseignant',
        'short' => 'Création d\'événements sur ses élèves',
        'details' => [
            'Crée des événements (sortie, absence, incident) pour ses élèves',
            'Voit les enfants de sa classe/école',
            'Ne gère pas les présences garderie/cantine',
            'Recommandé : instituteur, professeur des écoles',
        ],
    ],
    'alsh' => [
        'icon' => 'fa-child',
        'color' => 'text-purple-600',
        'bg' => 'bg-purple-50',
        'label' => 'Agent ALSH / Garderie',
        'short' => 'Gestion des présences garderie + cantine',
        'details' => [
            'Enregistre les arrivées/départs en garderie',
            'Marque les présents/absents en cantine',
            'Crée des événements garderie (incidents, comportements)',
            'Consulte l\'historique et exporte les présences',
            'Ne gère pas les menus cantine (rôle cuisinier dédié)',
            'Limité à son école si une école de rattachement est définie',
            'Recommandé : animateur ALSH, agent de garderie, agent polyvalent',
        ],
    ],
    'cantine' => [
        'icon' => 'fa-utensils',
        'color' => 'text-orange-600',
        'bg' => 'bg-orange-50',
        'label' => 'Cuisinier / Gestion menus',
        'short' => 'Création et publication des menus cantine',
        'details' => [
            'Crée et publie les menus (entrée, plat, garniture, dessert)',
            'Gère le catalogue de plats réutilisables',
            'Consulte les menus publiés',
            'Ne saisit pas les présences (rôle ALSH dédié)',
            'Limité à son école si une école de rattachement est définie',
            'Recommandé : cuisinier, responsable de restauration',
        ],
    ],
    'parent' => [
        'icon' => 'fa-home',
        'color' => 'text-indigo-600',
        'bg' => 'bg-indigo-50',
        'label' => 'Parent',
        'short' => 'Accès au portail parent (lecture seule)',
        'details' => [
            'Consulte les présences de ses enfants (garderie + cantine)',
            'Voit les menus cantine publiés',
            'Signale un changement (allergie, régime, info médicale)',
            'Ne voit que ses propres enfants',
            'Recommandé : parents des enfants inscrits',
        ],
    ],
];
@endphp

<div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
    <div class="flex items-start mb-3">
        <i class="fas fa-info-circle text-blue-500 mt-1 mr-2"></i>
        <div>
            <p class="text-sm font-medium text-gray-700">Choisissez le ou les profil(s) de cet utilisateur.</p>
            <p class="text-xs text-gray-500 mt-1">
                Un utilisateur peut avoir plusieurs profils (ex: un agent qui gère à la fois la garderie et la cantine).
                Cliquez sur un profil pour voir le détail de ses possibilités.
            </p>
        </div>
    </div>
</div>

<div class="space-y-3">
    @foreach($roles as $role)
    @php
        $desc = $roleDescriptions[$role->name] ?? [
            'icon' => 'fa-user',
            'color' => 'text-gray-600',
            'bg' => 'bg-gray-50',
            'label' => ucfirst($role->name),
            'short' => 'Profil personnalisé',
            'details' => [],
        ];
        $isChecked = isset($userRoles)
            ? in_array($role->name, old('roles', $userRoles))
            : in_array($role->name, old('roles', []));
    @endphp
    <label class="flex items-start p-3 border-2 {{ $isChecked ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:bg-gray-50' }} rounded-lg cursor-pointer transition-colors">
        <input type="checkbox"
               name="roles[]"
               value="{{ $role->name }}"
               {{ $isChecked ? 'checked' : '' }}
               class="mt-1 text-blue-600 focus:ring-blue-500 rounded">
        <div class="ml-3 flex-1">
            <div class="flex items-center">
                <i class="fas {{ $desc['icon'] }} {{ $desc['color'] }} mr-2"></i>
                <span class="font-medium text-gray-900">{{ $desc['label'] }}</span>
                <span class="ml-2 text-xs text-gray-400 font-mono">({{ $role->name }})</span>
            </div>
            <p class="text-sm text-gray-600 mt-1">{{ $desc['short'] }}</p>
            @if(!empty($desc['details']))
            <ul class="mt-2 space-y-1">
                @foreach($desc['details'] as $detail)
                <li class="flex items-start text-xs text-gray-500">
                    <i class="fas fa-check {{ $desc['color'] }} mt-0.5 mr-1.5"></i>
                    <span>{{ $detail }}</span>
                </li>
                @endforeach
            </ul>
            @endif
        </div>
    </label>
    @endforeach
</div>
