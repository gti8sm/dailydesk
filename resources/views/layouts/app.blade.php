<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>@yield('title', 'DailyDesk')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        @php
            $primaryColor = '#3B82F6';
            $secondaryColor = '#6366F1';
            if (function_exists('tenant') && tenant()) {
                $primaryColor = tenant()->primary_color ?? '#3B82F6';
                $secondaryColor = tenant()->secondary_color ?? '#6366F1';
            }
        @endphp
        :root {
            --tenant-primary: {{ $primaryColor }};
            --tenant-secondary: {{ $secondaryColor }};
        }
        .tenant-primary { background-color: var(--tenant-primary); }
        .tenant-primary-text { color: var(--tenant-primary); }
        .tenant-primary-border { border-color: var(--tenant-primary); }
        .tenant-secondary { background-color: var(--tenant-secondary); }
        .tenant-secondary-text { color: var(--tenant-secondary); }
        .btn-tenant {
            background-color: var(--tenant-primary);
            color: white;
            transition: background-color 0.2s;
        }
        .btn-tenant:hover {
            background-color: var(--tenant-secondary);
        }
        .focus-tenant:focus {
            --tw-ring-color: var(--tenant-primary);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    @auth
    <!-- Bannière d'impersonation -->
    @if(session('impersonating_from'))
    <div class="bg-purple-600 text-white py-3 px-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-user-secret text-2xl mr-3"></i>
                <div>
                    <p class="font-semibold">Mode Impersonation Actif</p>
                    <p class="text-sm text-purple-100">Vous êtes connecté en tant que <strong>{{ Auth::user()->name }}</strong></p>
                </div>
            </div>
            <form action="{{ route('central.stop-impersonating') }}" method="POST">
                @csrf
                <button type="submit" class="bg-white text-purple-600 px-4 py-2 rounded-lg font-medium hover:bg-purple-50 transition-colors">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Retour Super Admin
                </button>
            </form>
        </div>
    </div>
    @endif
    
    <nav class="bg-white shadow-sm border-b border-gray-200" x-data="{ mobileMenuOpen: false, modulesOpen: false, gestionOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        @php
                            $isTenantContext = function_exists('tenant') && tenant();
                            $tenantModules = $isTenantContext ? (tenant()->modules_enabled ?? array_keys(config('modules', []))) : array_keys(config('modules', []));
                            if (auth()->user()->hasRole('super_admin')) {
                                $dashboardRoute = route('central.dashboard');
                            } elseif ($isTenantContext && tenant()->slug) {
                                $dashboardRoute = route('dashboard', ['tenant' => tenant()->slug]);
                            } else {
                                $dashboardRoute = '/';
                            }
                        @endphp
                        <a href="{{ $dashboardRoute }}" class="flex items-center gap-2">
                            @php
                                $tenantLogo = null;
                                $appName = config('app.name', 'DailyDesk');
                                try {
                                    $appName = \App\Models\Setting::get('app_name', config('app.name', 'DailyDesk'));
                                    if (function_exists('tenant') && tenant() && tenant()->logo_path) {
                                        $tenantLogo = tenant()->logo_path;
                                    }
                                } catch (\Exception $e) {}
                            @endphp
                            @if($tenantLogo)
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($tenantLogo) }}"
                                 alt="{{ $appName }}"
                                 class="h-8 w-auto max-h-8 object-contain">
                            @else
                            <div class="bg-blue-600 rounded-lg w-8 h-8 flex items-center justify-center">
                                <i class="fas fa-users text-white text-sm"></i>
                            </div>
                            @endif
                            <span class="text-2xl font-bold text-blue-600">{{ $appName }}</span>
                        </a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        @hasrole('admin')
                        <a href="{{ $dashboardRoute }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-home mr-2"></i> Dashboard
                        </a>
                        @endhasrole

                        @if($isTenantContext)
                        @if(auth()->user()->can('view_garderie') || auth()->user()->can('view_cantine'))
                        <div class="relative h-full flex items-center" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false"
                                    class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium cursor-pointer h-full">
                                <i class="fas fa-th-large mr-2"></i> Modules
                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute left-0 top-full w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                @can('view_garderie')
                                @if(in_array('garderie', $tenantModules))
                                <a href="{{ route('garderie.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-child w-5"></i>
                                    <span class="ml-3">Garderie</span>
                                </a>
                                @endif
                                @endcan
                                @can('view_cantine')
                                @if(in_array('cantine', $tenantModules))
                                <div class="relative" x-data="{ subOpen: false }" @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                                    <a href="{{ route('cantine.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600">
                                        <i class="fas fa-utensils w-5"></i>
                                        <span class="ml-3 flex-1">Cantine</span>
                                        <i class="fas fa-chevron-right text-xs text-gray-400 ml-2"></i>
                                    </a>
                                    <div x-show="subOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                         class="absolute left-full top-0 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 -ml-1">
                                        <a href="{{ route('cantine.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600">
                                            <i class="fas fa-clipboard-check w-4"></i>
                                            <span class="ml-3">Présences</span>
                                        </a>
                                        @can('manage_cantine_menus')
                                        <a href="{{ route('cantine.menus.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600">
                                            <i class="fas fa-clipboard-list w-4"></i>
                                            <span class="ml-3">Menus</span>
                                        </a>
                                        <a href="{{ route('cantine.dishes.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600">
                                            <i class="fas fa-concierge-bell w-4"></i>
                                            <span class="ml-3">Plats</span>
                                        </a>
                                        @endcan
                                        <a href="{{ route('cantine.events.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600">
                                            <i class="fas fa-exclamation-triangle w-4"></i>
                                            <span class="ml-3">Signalements</span>
                                        </a>
                                    </div>
                                </div>
                                @endif
                                @endcan
                                @can('view_stock')
                                @if(in_array('stock', $tenantModules))
                                <div class="relative" x-data="{ subOpen: false }" @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                                    <a href="{{ route('stock.items.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                        <i class="fas fa-boxes-stacked w-5"></i>
                                        <span class="ml-3 flex-1">Stock</span>
                                        <i class="fas fa-chevron-right text-xs text-gray-400 ml-2"></i>
                                    </a>
                                    <div x-show="subOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                         class="absolute left-full top-0 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 -ml-1">
                                        <a href="{{ route('stock.items.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                            <i class="fas fa-box w-4"></i>
                                            <span class="ml-3">Articles</span>
                                        </a>
                                        @can('manage_stock')
                                        <a href="{{ route('stock.locations.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                            <i class="fas fa-warehouse w-4"></i>
                                            <span class="ml-3">Lieux</span>
                                        </a>
                                        @endcan
                                        <a href="{{ route('stock.movements.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                            <i class="fas fa-arrow-right-arrow-left w-4"></i>
                                            <span class="ml-3">Mouvements</span>
                                        </a>
                                        <a href="{{ route('stock.alerts.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                            <i class="fas fa-triangle-exclamation w-4"></i>
                                            <span class="ml-3">Alertes</span>
                                        </a>
                                    </div>
                                </div>
                                @endif
                                @endcan
                            </div>
                        </div>
                        @endif

                        @can('manage_families')
                        <div class="relative h-full flex items-center" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false"
                                    class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium cursor-pointer h-full">
                                <i class="fas fa-folder-open mr-2"></i> Gestion
                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute left-0 top-full w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <a href="{{ route('families.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-users w-5"></i>
                                    <span class="ml-3">Familles</span>
                                </a>
                                @hasrole('admin')
                                <a href="{{ route('schools.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-building w-5"></i>
                                    <span class="ml-3">Écoles</span>
                                </a>
                                <a href="{{ route('classes.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-school w-5"></i>
                                    <span class="ml-3">Classes</span>
                                </a>
                                <a href="{{ route('invitations.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-envelope-open-text w-5"></i>
                                    <span class="ml-3">Invitations</span>
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="{{ route('users.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-user-cog w-5"></i>
                                    <span class="ml-3">Utilisateurs</span>
                                </a>
                                <a href="{{ route('imports.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-file-upload w-5"></i>
                                    <span class="ml-3">Imports</span>
                                </a>
                                <a href="{{ route('exports.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-file-download w-5"></i>
                                    <span class="ml-3">Exports</span>
                                </a>
                                @endhasrole
                                @can('manage_public_site')
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="{{ route('site.pages.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-globe w-5"></i>
                                    <span class="ml-3">Site public</span>
                                </a>
                                @endcan
                                @can('manage_settings')
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="{{ route('settings.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-cog w-5"></i>
                                    <span class="ml-3">Paramètres</span>
                                </a>
                                @endcan
                            </div>
                        </div>
                        @endcan

                        @hasrole('parent')
                        <a href="{{ route('parent.dashboard') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-home mr-2"></i> Mon espace
                        </a>
                        @can('view_cantine_menus')
                        <a href="{{ route('parent.menus') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-utensils mr-2"></i> Menus
                        </a>
                        @endcan
                        @endhasrole
                        @endif

                        @hasrole('super_admin')
                        <div class="relative h-full flex items-center" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false"
                                    class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium cursor-pointer h-full">
                                <i class="fas fa-cog mr-2"></i> Administration
                                @if($openTicketCount ?? 0)
                                <span class="ml-1 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full">{{ $openTicketCount }}</span>
                                @endif
                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute left-0 top-full w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <a href="{{ route('central.tenants.create') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-plus-circle w-5"></i>
                                    <span class="ml-3">Créer un tenant</span>
                                </a>
                                <a href="{{ route('central.tenants.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-building w-5"></i>
                                    <span class="ml-3">Tenants</span>
                                </a>
                                <a href="{{ route('central.plans.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-600">
                                    <i class="fas fa-tags w-5"></i>
                                    <span class="ml-3">Plans</span>
                                </a>
                                <a href="{{ route('central.statistics') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600">
                                    <i class="fas fa-chart-line w-5"></i>
                                    <span class="ml-3">Statistiques</span>
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="{{ route('central.modules.overview') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-puzzle-piece w-5"></i>
                                    <span class="ml-3">Modules</span>
                                </a>
                                <a href="{{ route('central.exports') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-file-download w-5"></i>
                                    <span class="ml-3">Exports</span>
                                </a>
                                <a href="{{ route('central.logs.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-history w-5"></i>
                                    <span class="ml-3">Journal</span>
                                </a>
                                <a href="{{ route('central.support.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600">
                                    <i class="fas fa-life-ring w-5"></i>
                                    <span class="ml-3">Support</span>
                                    @if($openTicketCount ?? 0)
                                    <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $openTicketCount }}</span>
                                    @endif
                                </a>
                            </div>
                        </div>
                        @endhasrole
                    </div>
                </div>
                
                <div class="flex items-center">
                    <!-- Sélecteur d'école (admin global ou agent avec remplacements) -->
                    @php
                        $isTenantContext = function_exists('tenant') && tenant();
                        $showSchoolSelector = $isTenantContext
                            && !auth()->user()->hasRole('super_admin')
                            && !auth()->user()->hasRole('parent');
                        $accessibleSchools = $showSchoolSelector ? \App\Models\User::getAccessibleSchools() : collect();
                        $userSchoolId = auth()->user()->school_id;
                        $hasAdditional = !empty(auth()->user()->additional_school_ids);
                        $selectedSchoolId = session('selected_school_id');
                        $currentSchool = $selectedSchoolId ? $accessibleSchools->firstWhere('id', $selectedSchoolId) : null;
                        $showDropdown = ($accessibleSchools->count() > 1) || ($userSchoolId && $hasAdditional);
                    @endphp
                    @if($showSchoolSelector && $showDropdown)
                    <div class="hidden sm:block mr-3" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-600 bg-white hover:bg-gray-50">
                            <i class="fas fa-school mr-2 text-blue-500"></i>
                            <span>{{ $currentSchool ? $currentSchool->name : ($userSchoolId ? $accessibleSchools->firstWhere('id', $userSchoolId)?->name ?? 'Mon école' : 'Toutes les écoles') }}</span>
                            <i class="fas fa-chevron-down ml-2 text-xs"></i>
                        </button>
                        <div x-show="open" x-transition class="origin-top-right absolute mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                            <form action="{{ route('schools.select') }}" method="POST">
                                @csrf
                                @if(!$userSchoolId)
                                <button type="submit" name="school_id" value="" class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 {{ !$selectedSchoolId ? 'bg-blue-50 font-semibold' : '' }}">
                                    <i class="fas fa-globe mr-2 text-gray-400"></i> Toutes les écoles
                                </button>
                                @endif
                                @foreach($accessibleSchools as $school)
                                <button type="submit" name="school_id" value="{{ $school->id }}" class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 {{ $selectedSchoolId === $school->id ? 'bg-blue-50 font-semibold' : '' }}">
                                    <i class="fas fa-school mr-2 text-blue-400"></i> {{ $school->name }}
                                    @if($school->id === $userSchoolId)
                                    <span class="ml-1 text-xs text-gray-400">(principal)</span>
                                    @endif
                                </button>
                                @endforeach
                            </form>
                        </div>
                    </div>
                    @elseif($userSchoolId && $isTenantContext)
                    <div class="hidden sm:block mr-3">
                        <span class="inline-flex items-center px-3 py-2 text-sm text-gray-500">
                            <i class="fas fa-school mr-2 text-blue-500"></i>
                            {{ optional(auth()->user()->school)->name }}
                        </span>
                    </div>
                    @endif

                    <!-- Bouton menu mobile -->
                    <div class="sm:hidden mr-2">
                        <button @click="mobileMenuOpen = true" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="ml-3 relative" x-data="{ open: false }">
                        <div>
                            <button @click="open = !open" class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <span class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                    <i class="fas fa-user-circle mr-2"></i>
                                    {{ Auth::user()->name }}
                                    <i class="fas fa-chevron-down ml-2"></i>
                                </span>
                            </button>
                        </div>
                        <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-50">
                            @unless(auth()->user()->hasRole('super_admin'))
                            <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Profil
                            </a>
                            @hasrole('parent')
                            <a href="{{ route('parent.notifications') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-bell mr-2"></i> Notifications
                            </a>
                            <a href="{{ route('parent.events') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-exclamation-triangle mr-2"></i> Signalements
                            </a>
                            @endhasrole
                            @hasrole('admin')
                            <a href="{{ route('users.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-users mr-2"></i> Utilisateurs
                            </a>
                            <a href="{{ route('imports.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-file-upload mr-2"></i> Imports
                            </a>
                            <a href="{{ route('exports.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-file-download mr-2"></i> Exports
                            </a>
                            <a href="{{ route('support.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-life-ring mr-2"></i> Support
                            </a>
                            @endhasrole
                            @can('manage_settings')
                            <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog mr-2"></i> Paramètres
                            </a>
                            @endcan
                            @endunless
                            @hasrole('super_admin')
                            <a href="{{ route('central.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Profil
                            </a>
                            <a href="{{ route('central.modules.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog mr-2"></i> Paramètres plateforme
                            </a>
                            @endhasrole
                            <form method="POST" action="{{ auth()->user()->hasRole('super_admin') ? route('central.logout') : route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Menu mobile -->
        <div x-cloak>
            <!-- Overlay -->
            <div x-show="mobileMenuOpen" 
                 @click="mobileMenuOpen = false"
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black bg-opacity-50 z-40 sm:hidden">
            </div>
            
            <!-- Menu drawer -->
            <div x-show="mobileMenuOpen"
                 x-transition:enter="transition ease-in-out duration-300 transform"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in-out duration-300 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="fixed inset-y-0 left-0 w-64 bg-white shadow-xl z-50 sm:hidden">
                
                <div class="h-full flex flex-col">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-4 border-b">
                        <span class="text-xl font-bold text-blue-600">
                            @php
                                try {
                                    echo \App\Models\Setting::get('app_name', config('app.name', 'DailyDesk'));
                                } catch (\Exception $e) {
                                    echo config('app.name', 'DailyDesk');
                                }
                            @endphp
                        </span>
                        <button @click="mobileMenuOpen = false" class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <!-- Menu items -->
                    <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
                        @hasrole('admin')
                        <a href="{{ $dashboardRoute }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-home w-6"></i>
                            <span class="ml-3">Dashboard</span>
                        </a>
                        @endhasrole

                        @if($isTenantContext)
                        @if(auth()->user()->can('view_garderie') || auth()->user()->can('view_cantine'))
                        <div x-data="{ open: false }" class="space-y-1">
                            <button @click="open = !open" class="w-full flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                                <i class="fas fa-th-large w-6"></i>
                                <span class="ml-3 flex-1 text-left">Modules</span>
                                <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                            </button>
                            <div x-show="open" x-collapse class="pl-4 space-y-1">
                                @can('view_garderie')
                                @if(in_array('garderie', $tenantModules))
                                <a href="{{ route('garderie.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg">
                                    <i class="fas fa-child w-5"></i>
                                    <span class="ml-3">Garderie</span>
                                </a>
                                @endif
                                @endcan
                                @can('view_cantine')
                                @if(in_array('cantine', $tenantModules))
                                <div x-data="{ cantineOpen: false }" class="space-y-1">
                                    <button @click="cantineOpen = !cantineOpen" class="w-full flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-orange-50 hover:text-orange-600 rounded-lg">
                                        <i class="fas fa-utensils w-5"></i>
                                        <span class="ml-3 flex-1 text-left">Cantine</span>
                                        <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': cantineOpen }"></i>
                                    </button>
                                    <div x-show="cantineOpen" x-collapse class="pl-4 space-y-1">
                                        <a href="{{ route('cantine.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-orange-50 hover:text-orange-600 rounded-lg">
                                            <i class="fas fa-clipboard-check w-4"></i>
                                            <span class="ml-3">Présences</span>
                                        </a>
                                        @can('manage_cantine_menus')
                                        <a href="{{ route('cantine.menus.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-orange-50 hover:text-orange-600 rounded-lg">
                                            <i class="fas fa-clipboard-list w-4"></i>
                                            <span class="ml-3">Menus</span>
                                        </a>
                                        <a href="{{ route('cantine.dishes.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-orange-50 hover:text-orange-600 rounded-lg">
                                            <i class="fas fa-concierge-bell w-4"></i>
                                            <span class="ml-3">Plats</span>
                                        </a>
                                        @endcan
                                        <a href="{{ route('cantine.events.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-orange-50 hover:text-orange-600 rounded-lg">
                                            <i class="fas fa-exclamation-triangle w-4"></i>
                                            <span class="ml-3">Signalements</span>
                                        </a>
                                    </div>
                                </div>
                                @endif
                                @endcan
                                @can('view_stock')
                                @if(in_array('stock', $tenantModules))
                                <div x-data="{ stockOpen: false }" class="space-y-1">
                                    <button @click="stockOpen = !stockOpen" class="w-full flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg">
                                        <i class="fas fa-boxes-stacked w-5"></i>
                                        <span class="ml-3 flex-1 text-left">Stock</span>
                                        <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': stockOpen }"></i>
                                    </button>
                                    <div x-show="stockOpen" x-collapse class="pl-4 space-y-1">
                                        <a href="{{ route('stock.items.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg">
                                            <i class="fas fa-box w-4"></i>
                                            <span class="ml-3">Articles</span>
                                        </a>
                                        @can('manage_stock')
                                        <a href="{{ route('stock.locations.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg">
                                            <i class="fas fa-warehouse w-4"></i>
                                            <span class="ml-3">Lieux</span>
                                        </a>
                                        @endcan
                                        <a href="{{ route('stock.movements.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg">
                                            <i class="fas fa-arrow-right-arrow-left w-4"></i>
                                            <span class="ml-3">Mouvements</span>
                                        </a>
                                        <a href="{{ route('stock.alerts.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg">
                                            <i class="fas fa-triangle-exclamation w-4"></i>
                                            <span class="ml-3">Alertes</span>
                                        </a>
                                    </div>
                                </div>
                                @endif
                                @endcan
                            </div>
                        </div>
                        @endif

                        @can('manage_families')
                        <div x-data="{ open: false }" class="space-y-1">
                            <button @click="open = !open" class="w-full flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                                <i class="fas fa-folder-open w-6"></i>
                                <span class="ml-3 flex-1 text-left">Gestion</span>
                                <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                            </button>
                            <div x-show="open" x-collapse class="pl-4 space-y-1">
                                <a href="{{ route('families.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-lg">
                                    <i class="fas fa-users w-5"></i>
                                    <span class="ml-3">Familles</span>
                                </a>
                                @hasrole('admin')
                                <a href="{{ route('classes.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-lg">
                                    <i class="fas fa-school w-5"></i>
                                    <span class="ml-3">Classes</span>
                                </a>
                                <a href="{{ route('invitations.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-lg">
                                    <i class="fas fa-envelope-open-text w-5"></i>
                                    <span class="ml-3">Invitations</span>
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="{{ route('users.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-lg">
                                    <i class="fas fa-user-cog w-5"></i>
                                    <span class="ml-3">Utilisateurs</span>
                                </a>
                                <a href="{{ route('imports.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-lg">
                                    <i class="fas fa-file-upload w-5"></i>
                                    <span class="ml-3">Imports</span>
                                </a>
                                <a href="{{ route('exports.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-lg">
                                    <i class="fas fa-file-download w-5"></i>
                                    <span class="ml-3">Exports</span>
                                </a>
                                @can('manage_public_site')
                                <a href="{{ route('site.pages.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-lg">
                                    <i class="fas fa-globe w-5"></i>
                                    <span class="ml-3">Site public</span>
                                </a>
                                @endcan
                                <a href="{{ route('support.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-lg">
                                    <i class="fas fa-life-ring w-5"></i>
                                    <span class="ml-3">Support</span>
                                </a>
                                @endhasrole
                                @can('manage_settings')
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="{{ route('settings.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-lg">
                                    <i class="fas fa-cog w-5"></i>
                                    <span class="ml-3">Paramètres</span>
                                </a>
                                @endcan
                            </div>
                        </div>
                        @endcan

                        @hasrole('parent')
                        <a href="{{ route('parent.dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-home w-6"></i>
                            <span class="ml-3">Mon espace</span>
                        </a>
                        @can('view_cantine_menus')
                        <a href="{{ route('parent.menus') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-utensils w-6"></i>
                            <span class="ml-3">Menus cantine</span>
                        </a>
                        @endcan
                        <a href="{{ route('parent.notifications') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-bell w-6"></i>
                            <span class="ml-3">Notifications</span>
                        </a>
                        <a href="{{ route('parent.events') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-exclamation-triangle w-6"></i>
                            <span class="ml-3">Signalements</span>
                        </a>
                        @endhasrole
                        @endif

                        @hasrole('super_admin')
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="{{ route('central.tenants.create') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-plus-circle w-6"></i>
                            <span class="ml-3">Créer un tenant</span>
                        </a>
                        <a href="{{ route('central.tenants.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-building w-6"></i>
                            <span class="ml-3">Tenants</span>
                        </a>
                        <a href="{{ route('central.plans.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-tags w-6"></i>
                            <span class="ml-3">Plans</span>
                        </a>
                        <a href="{{ route('central.statistics') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-chart-line w-6"></i>
                            <span class="ml-3">Statistiques</span>
                        </a>
                        <a href="{{ route('central.modules.overview') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-puzzle-piece w-6"></i>
                            <span class="ml-3">Modules</span>
                        </a>
                        <a href="{{ route('central.logs.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-history w-6"></i>
                            <span class="ml-3">Journal</span>
                        </a>
                        <a href="{{ route('central.support.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-life-ring w-6"></i>
                            <span class="ml-3">Support</span>
                            @if($openTicketCount ?? 0)
                            <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $openTicketCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('central.profile') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-user w-6"></i>
                            <span class="ml-3">Profil</span>
                        </a>
                        <a href="{{ route('central.modules.settings') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-cog w-6"></i>
                            <span class="ml-3">Paramètres plateforme</span>
                        </a>
                        @endhasrole
                    </nav>
                    
                    <!-- Footer -->
                    <div class="border-t p-4">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-user-circle text-2xl text-gray-400"></i>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ auth()->user()->hasRole('super_admin') ? route('central.logout') : route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg">
                                <i class="fas fa-sign-out-alt w-6"></i>
                                <span class="ml-3">Déconnexion</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <main class="@auth py-6 @endauth">
        @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
            <div class="bg-green-50 border-l-4 border-green-400 p-4">
                <div class="flex">
                    <i class="fas fa-check-circle text-green-400 mr-3"></i>
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
            <div class="bg-red-50 border-l-4 border-red-400 p-4">
                <div class="flex">
                    <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif

        @yield('content')
    </main>

    <script>
        (function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            
            window.axios = {
                post: (url, data) => {
                    return fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(data)
                    }).then(res => res.json().then(json => ({ data: json, status: res.status })));
                },
                get: (url) => {
                    return fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                        }
                    }).then(res => res.json().then(json => ({ data: json, status: res.status })));
                },
                delete: (url) => {
                    return fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        }
                    }).then(res => res.json().then(json => ({ data: json, status: res.status })));
                },
                patch: (url, data) => {
                    return fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(data)
                    }).then(res => res.json().then(json => ({ data: json, status: res.status })));
                }
            };
        })();
    </script>
    
    @stack('scripts')

    @auth
    @include('partials.help-widget')
    @endauth

    <footer class="bg-gray-800 text-gray-300 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-center sm:text-left">
                    &copy; {{ date('Y') }} DailyDesk — Tous droits réservés
                </div>
                <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-2 text-sm">
                    @if(Route::has('legal.cgv'))
                    <a href="{{ route('legal.cgv') }}" class="hover:text-white transition">CGV</a>
                    <span class="text-gray-600">|</span>
                    @endif
                    @if(Route::has('legal.mentions'))
                    <a href="{{ route('legal.mentions') }}" class="hover:text-white transition">Mentions légales</a>
                    <span class="text-gray-600">|</span>
                    @endif
                    @if(Route::has('legal.rgpd'))
                    <a href="{{ route('legal.rgpd') }}" class="hover:text-white transition">RGPD</a>
                    @endif
                </div>
                <div class="text-sm text-center sm:text-right">
                    Site développé à Libourne par <a href="https://smallwebconcept.fr" target="_blank" class="text-orange-400 hover:text-orange-300 font-medium">SmallWebConcept</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
