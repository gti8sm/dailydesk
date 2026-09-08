<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DailyDesk')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
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
                        <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-blue-600">
                            @php
                                try {
                                    echo \App\Models\Setting::get('app_name', config('app.name', 'DailyDesk'));
                                } catch (\Exception $e) {
                                    echo config('app.name', 'DailyDesk');
                                }
                            @endphp
                        </a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('dashboard') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-home mr-2"></i> Dashboard
                        </a>

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
                                <a href="{{ route('garderie.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-child w-5"></i>
                                    <span class="ml-3">Garderie</span>
                                </a>
                                @endcan
                                @can('view_cantine')
                                <a href="{{ route('cantine.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600">
                                    <i class="fas fa-utensils w-5"></i>
                                    <span class="ml-3">Cantine</span>
                                </a>
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
                        @endhasrole
                    </div>
                </div>
                
                <div class="flex items-center">
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
                            @endhasrole
                            @can('manage_settings')
                            <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog mr-2"></i> Paramètres
                            </a>
                            @endcan
                            <form method="POST" action="{{ route('logout') }}">
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
                        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-home w-6"></i>
                            <span class="ml-3">Dashboard</span>
                        </a>

                        @if(auth()->user()->can('view_garderie') || auth()->user()->can('view_cantine'))
                        <div x-data="{ open: false }" class="space-y-1">
                            <button @click="open = !open" class="w-full flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                                <i class="fas fa-th-large w-6"></i>
                                <span class="ml-3 flex-1 text-left">Modules</span>
                                <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                            </button>
                            <div x-show="open" x-collapse class="pl-4 space-y-1">
                                @can('view_garderie')
                                <a href="{{ route('garderie.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg">
                                    <i class="fas fa-child w-5"></i>
                                    <span class="ml-3">Garderie</span>
                                </a>
                                @endcan
                                @can('view_cantine')
                                <a href="{{ route('cantine.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-orange-50 hover:text-orange-600 rounded-lg">
                                    <i class="fas fa-utensils w-5"></i>
                                    <span class="ml-3">Cantine</span>
                                </a>
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
                        <a href="{{ route('parent.notifications') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-bell w-6"></i>
                            <span class="ml-3">Notifications</span>
                        </a>
                        <a href="{{ route('parent.events') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-exclamation-triangle w-6"></i>
                            <span class="ml-3">Signalements</span>
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
                        <form method="POST" action="{{ route('logout') }}">
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
</body>
</html>
