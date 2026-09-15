<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @php
        try {
            $appName = \App\Models\Setting::get('app_name', config('app.name', 'DailyDesk'));
        } catch (\Exception $e) {
            $appName = config('app.name', 'DailyDesk');
        }
    @endphp
    <title>{{ $appName }} — Gestion municipale pour les mairies</title>
    <meta name="description" content="DailyDesk — Solution complète de gestion municipale pour les mairies et collectivités. Gestion des familles, cantine scolaire, garderie, stocks et plus encore.">
    <meta name="keywords" content="gestion municipale, mairie, cantine scolaire, garderie, administration, collectivités, logiciel mairie, gestion périscolaire, gestion des stocks">
    <meta name="author" content="SmallWebConcept">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="theme-color" content="#2563eb">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $appName }} — Gestion municipale pour les mairies">
    <meta property="og:description" content="Solution complète de gestion pour les mairies et collectivités.">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('og-image.png') }}">
    <meta property="og:locale" content="fr_FR">
    <meta property="og:site_name" content="{{ $appName }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $appName }} — Gestion municipale pour les mairies">
    <meta name="twitter:description" content="Solution complète de gestion pour les mairies et collectivités.">
    <meta name="twitter:image" content="{{ asset('og-image.png') }}">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "SoftwareApplication",
        "name": "{{ $appName }}",
        "applicationCategory": "BusinessApplication",
        "operatingSystem": "Web",
        "description": "Solution de gestion municipale pour les mairies et collectivités",
        "url": "{{ url('/') }}",
        "publisher": {
            "@type": "Organization",
            "name": "SmallWebConcept",
            "url": "https://smallwebconcept.fr"
        },
        "offers": {
            "@type": "Offer",
            "price": "0",
            "priceCurrency": "EUR"
        }
    }
    </script>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "SmallWebConcept",
        "url": "https://smallwebconcept.fr",
        "logo": "{{ asset('favicon.svg') }}"
    }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        .hero-gradient { background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 50%, #6366f1 100%); }
        .feature-card:hover { transform: translateY(-4px); }
        .feature-card { transition: transform 0.3s ease; }
        @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        .float-anim { animation: float 4s ease-in-out infinite; }
    </style>
</head>
<body class="bg-white">

<!-- Nav -->
<nav class="fixed top-0 w-full bg-white/90 backdrop-blur-md shadow-sm z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <div class="bg-blue-600 rounded-lg w-10 h-10 flex items-center justify-center mr-3">
                    <i class="fas fa-users text-white text-lg"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900">{{ $appName }}</span>
            </div>
            <div class="hidden md:flex items-center space-x-8">
                <a href="#features" class="text-gray-600 hover:text-blue-600 text-sm font-medium">Fonctionnalités</a>
                <a href="#modules" class="text-gray-600 hover:text-blue-600 text-sm font-medium">Modules</a>
                <a href="#pricing" class="text-gray-600 hover:text-blue-600 text-sm font-medium">Tarifs</a>
                <a href="#roadmap" class="text-gray-600 hover:text-blue-600 text-sm font-medium">Roadmap</a>
                <a href="#demo" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                    Demander une démo
                </a>
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 text-sm font-medium">
                    <i class="fas fa-sign-in-alt mr-1"></i> Connexion
                </a>
            </div>
            <div class="md:hidden">
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 text-sm font-medium">
                    <i class="fas fa-sign-in-alt"></i>
                </a>
                <a href="#demo" class="ml-3 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium">Démo</a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero -->
<section class="hero-gradient text-white pt-32 pb-20 px-4">
    <div class="max-w-7xl mx-auto text-center">
        <div class="float-anim inline-block mb-6">
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 inline-flex">
                <i class="fas fa-building text-5xl text-white"></i>
            </div>
        </div>
        <h1 class="text-4xl md:text-6xl font-extrabold mb-6 leading-tight">
            La gestion municipale,<br>
            <span class="text-blue-200">simple et moderne</span>
        </h1>
        <p class="text-lg md:text-xl text-blue-100 max-w-2xl mx-auto mb-8">
            {{ $appName }} digitalise la garderie, la cantine, la gestion des familles et des stocks
            pour les mairies et collectivités. Tout-en-un, accessible sur ordinateur, tablette et mobile.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="#demo" class="bg-white text-blue-700 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-blue-50 transition-colors shadow-lg">
                <i class="fas fa-rocket mr-2"></i> Demander une démo gratuite
            </a>
            <a href="#features" class="bg-white/10 backdrop-blur-sm text-white border border-white/30 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-white/20 transition-colors">
                <i class="fas fa-arrow-down mr-2"></i> Découvrir
            </a>
        </div>
        <div class="mt-12 flex flex-wrap justify-center gap-8 text-blue-100 text-sm">
            <div class="flex items-center"><i class="fas fa-check-circle mr-2 text-green-300"></i> 30 jours d'essai</div>
            <div class="flex items-center"><i class="fas fa-check-circle mr-2 text-green-300"></i> Sans engagement</div>
            <div class="flex items-center"><i class="fas fa-check-circle mr-2 text-green-300"></i> Données hébergées en France</div>
        </div>
    </div>
</section>

<!-- Stats bar -->
<section class="bg-gray-50 py-12 px-4 border-y border-gray-200">
    <div class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
        <div>
            <div class="text-3xl font-bold text-blue-600">100%</div>
            <div class="text-sm text-gray-500 mt-1">Digitalisé</div>
        </div>
        <div>
            <div class="text-3xl font-bold text-blue-600">-80%</div>
            <div class="text-sm text-gray-500 mt-1">Temps administratif</div>
        </div>
        <div>
            <div class="text-3xl font-bold text-blue-600">24/7</div>
            <div class="text-sm text-gray-500 mt-1">Accessible</div>
        </div>
        <div>
            <div class="text-3xl font-bold text-blue-600">RGPD</div>
            <div class="text-sm text-gray-500 mt-1">Conforme</div>
        </div>
    </div>
</section>

<!-- Features -->
<section id="features" class="py-20 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Tout ce dont votre mairie a besoin</h2>
            <p class="text-gray-500 text-lg max-w-2xl mx-auto">Une plateforme unique pour gérer la vie quotidienne des enfants et des familles de votre commune.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="feature-card bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                <div class="bg-blue-100 rounded-2xl w-16 h-16 flex items-center justify-center mb-6">
                    <i class="fas fa-child text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Garderie</h3>
                <p class="text-gray-600 text-sm mb-4">Suivez les présences des enfants en temps réel, matin et soir. Enregistrez les arrivées et départs d'un clic, générez des rapports mensuels automatiques.</p>
                <ul class="text-sm text-gray-500 space-y-1">
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Pointage en temps réel</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Rapports mensuels</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Notifications parents</li>
                    <li><i class="fas fa-tablet-alt text-blue-500 mr-2"></i> Utilisable sur tablette & mobile</li>
                </ul>
            </div>
            <div class="feature-card bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                <div class="bg-orange-100 rounded-2xl w-16 h-16 flex items-center justify-center mb-6">
                    <i class="fas fa-utensils text-orange-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Cantine</h3>
                <p class="text-gray-600 text-sm mb-4">Gérez les repas, les allergies et les régimes alimentaires. Marquez les présents d'un clic, suivez les refus et incidents alimentaires.</p>
                <ul class="text-sm text-gray-500 space-y-1">
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Gestion des allergies</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Suivi des repas</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Export des présences</li>
                    <li><i class="fas fa-tablet-alt text-orange-500 mr-2"></i> Utilisable sur tablette & mobile</li>
                </ul>
            </div>
            <div class="feature-card bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                <div class="bg-green-100 rounded-2xl w-16 h-16 flex items-center justify-center mb-6">
                    <i class="fas fa-users text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Familles</h3>
                <p class="text-gray-600 text-sm mb-4">Centralisez les données des familles : coordonnées, enfants, contacts d'urgence. Importez en masse via CSV, invitez les parents par email.</p>
                <ul class="text-sm text-gray-500 space-y-1">
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Import CSV en masse</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Espace parent dédié</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Invitations par email</li>
                </ul>
            </div>
            <div class="feature-card bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                <div class="bg-indigo-100 rounded-2xl w-16 h-16 flex items-center justify-center mb-6">
                    <i class="fas fa-boxes-stacked text-indigo-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Gestion des stocks</h3>
                <p class="text-gray-600 text-sm mb-4">Suivez les stocks de matériel, fournitures et consommables. Gérez les lieux de stockage, les entrées/sorties et recevez des alertes de réapprovisionnement.</p>
                <ul class="text-sm text-gray-500 space-y-1">
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Lieux de stockage multiples</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Alertes de seuil minimum</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Historique des mouvements</li>
                    <li><i class="fas fa-tablet-alt text-indigo-500 mr-2"></i> Saisie rapide sur mobile</li>
                </ul>
            </div>
            <div class="feature-card bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                <div class="bg-purple-100 rounded-2xl w-16 h-16 flex items-center justify-center mb-6">
                    <i class="fas fa-bell text-purple-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Notifications</h3>
                <p class="text-gray-600 text-sm mb-4">Notifiez automatiquement les parents lors des arrivées, départs et événements. Configuration fine par type de notification.</p>
                <ul class="text-sm text-gray-500 space-y-1">
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Notifications email</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Alertes en temps réel</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Personnalisables</li>
                </ul>
            </div>
            <div class="feature-card bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                <div class="bg-indigo-100 rounded-2xl w-16 h-16 flex items-center justify-center mb-6">
                    <i class="fas fa-file-export text-indigo-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Exports & Rapports</h3>
                <p class="text-gray-600 text-sm mb-4">Exportez les données de présences, d'événements et de familles. Rapports mensuels prêts pour les instances municipales.</p>
                <ul class="text-sm text-gray-500 space-y-1">
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Export Excel/PDF</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Rapports automatiques</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Données filtrables</li>
                </ul>
            </div>
            <div class="feature-card bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                <div class="bg-red-100 rounded-2xl w-16 h-16 flex items-center justify-center mb-6">
                    <i class="fas fa-shield-alt text-red-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Sécurité</h3>
                <p class="text-gray-600 text-sm mb-4">Authentification sécurisée, whitelist IP, code PIN rapide, rôles et permissions fines. Traçabilité complète des actions.</p>
                <ul class="text-sm text-gray-500 space-y-1">
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Rôles & permissions</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Journal d'activité</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Conforme RGPD</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Multi-device -->
<section class="py-20 px-4 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Pensé pour le terrain
                </h2>
                <p class="text-gray-600 text-lg mb-8">
                    Que ce soit à l'accueil de la garderie, en salle de cantine ou en déplacement,
                    {{ $appName }} s'adapte à votre façon de travailler. Pointez les enfants depuis
                    une tablette, un smartphone ou un ordinateur — partout, tout le temps.
                </p>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="bg-blue-100 rounded-xl w-12 h-12 flex items-center justify-center mr-4 flex-shrink-0">
                            <i class="fas fa-tablet-alt text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Tablette en salle</h3>
                            <p class="text-sm text-gray-600">Pointage rapide des présences en garderie et en cantine, directement depuis une tablette. Idéal pour le personnel sur le terrain.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="bg-green-100 rounded-xl w-12 h-12 flex items-center justify-center mr-4 flex-shrink-0">
                            <i class="fas fa-mobile-alt text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Mobile en déplacement</h3>
                            <p class="text-sm text-gray-600">Accédez à toutes les fonctionnalités depuis votre smartphone. Consultez les présences, gérez les enfants et recevez les notifications où que vous soyez.</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="bg-purple-100 rounded-xl w-12 h-12 flex items-center justify-center mr-4 flex-shrink-0">
                            <i class="fas fa-desktop text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 mb-1">Ordinateur au bureau</h3>
                            <p class="text-sm text-gray-600">Gérez les paramètres, les familles, les exports et la facturation depuis votre poste fixe. Une expérience complète sur grand écran.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="bg-white rounded-3xl shadow-2xl p-6 max-w-sm mx-auto">
                    <div class="bg-gray-100 rounded-2xl p-4 mb-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="bg-blue-600 rounded-lg w-8 h-8 flex items-center justify-center mr-2">
                                    <i class="fas fa-child text-white text-sm"></i>
                                </div>
                                <span class="font-semibold text-gray-900 text-sm">Garderie — Matin</span>
                            </div>
                            <span class="text-xs text-gray-400">{{ date('d/m/Y') }}</span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between bg-green-50 rounded-lg p-2">
                                <span class="text-sm text-gray-700">Lucas D.</span>
                                <span class="text-xs text-green-600 font-medium"><i class="fas fa-check-circle mr-1"></i> Présent 7h42</span>
                            </div>
                            <div class="flex items-center justify-between bg-green-50 rounded-lg p-2">
                                <span class="text-sm text-gray-700">Emma L.</span>
                                <span class="text-xs text-green-600 font-medium"><i class="fas fa-check-circle mr-1"></i> Présente 7h55</span>
                            </div>
                            <div class="flex items-center justify-between bg-yellow-50 rounded-lg p-2">
                                <span class="text-sm text-gray-700">Noah P.</span>
                                <span class="text-xs text-yellow-600 font-medium"><i class="fas fa-clock mr-1"></i> En attente</span>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <button class="bg-blue-600 text-white py-3 rounded-xl text-sm font-medium">
                            <i class="fas fa-hand-point-up mr-1"></i> Pointer
                        </button>
                        <button class="bg-gray-100 text-gray-700 py-3 rounded-xl text-sm font-medium">
                            <i class="fas fa-list mr-1"></i> Liste
                        </button>
                    </div>
                </div>
                <div class="absolute -top-4 -right-4 bg-orange-500 text-white px-4 py-2 rounded-full text-sm font-semibold shadow-lg">
                    <i class="fas fa-utensils mr-1"></i> Cantine
                </div>
                <div class="absolute -bottom-4 -left-4 bg-blue-500 text-white px-4 py-2 rounded-full text-sm font-semibold shadow-lg">
                    <i class="fas fa-child mr-1"></i> Garderie
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modules / Pricing -->
<section id="modules" class="bg-gray-50 py-20 px-4" x-data="{ billing: 'monthly' }">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Une tarification adaptée à votre commune</h2>
            <p class="text-gray-500 text-lg max-w-2xl mx-auto">Tous les modules sont inclus dans chaque offre. Le prix dépend uniquement de la taille de votre commune et du niveau de support dont vous avez besoin.</p>
        </div>

        <!-- Toggle Mensuel / Annuel -->
        <div class="flex justify-center mb-12">
            <div class="inline-flex bg-gray-200 rounded-full p-1">
                <button @click="billing = 'monthly'"
                        :class="billing === 'monthly' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500'"
                        class="px-6 py-2.5 rounded-full text-sm font-semibold transition-all">
                    Mensuel
                </button>
                <button @click="billing = 'yearly'"
                        :class="billing === 'yearly' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500'"
                        class="px-6 py-2.5 rounded-full text-sm font-semibold transition-all">
                    Annuel
                    <span class="ml-1 text-xs text-green-600 font-bold">-2 mois</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($plans as $index => $plan)
            @php
                $isPopular = $plan->slug === 'commune-moyenne';
                $isOnRequest = $plan->price_monthly == 0;
                $borderClass = $isPopular ? 'border-2 border-blue-500 relative scale-105 shadow-xl' : 'border-2 border-gray-200 shadow-lg';
                $yearlySavings = $isOnRequest ? 0 : $plan->getYearlySavingsPercentage();
                $popLabel = $plan->population_max === null
                    ? ($plan->population_min ? 'Plus de ' . number_format($plan->population_min, 0, ',', ' ') . ' hab.' : 'Toutes communes')
                    : number_format($plan->population_min, 0, ',', ' ') . ' – ' . number_format($plan->population_max, 0, ',', ' ') . ' hab.';
            @endphp
            <div class="bg-white rounded-2xl p-8 {{ $borderClass }}">
                @if($isPopular)
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-blue-600 text-white px-4 py-1 rounded-full text-xs font-semibold">
                    Le plus choisi
                </div>
                @endif
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h3>
                    @if($isOnRequest)
                    <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-semibold">Sur devis</span>
                    @else
                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Disponible</span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mb-4">
                    <i class="fas fa-users mr-1"></i>{{ $popLabel }}
                </p>
                @if($isOnRequest)
                <p class="text-3xl font-bold text-gray-900 mb-1">Sur devis</p>
                <p class="text-sm text-gray-500 mb-6">Contactez-nous</p>
                @else
                <div>
                    <div x-show="billing === 'monthly'" x-transition.duration.200ms>
                        <p class="text-3xl font-bold text-gray-900 mb-1">
                            {{ number_format($plan->price_monthly, 0, ',', ' ') }}€
                            <span class="text-base font-normal text-gray-500">/mois</span>
                        </p>
                        <p class="text-sm text-gray-500 mb-6">Enfants & utilisateurs illimités</p>
                    </div>
                    <div x-show="billing === 'yearly'" x-transition.duration.200ms style="display: none;">
                        <p class="text-3xl font-bold text-gray-900 mb-1">
                            {{ number_format($plan->price_yearly, 0, ',', ' ') }}€
                            <span class="text-base font-normal text-gray-500">/an</span>
                        </p>
                        @if($yearlySavings > 0)
                        <p class="text-sm text-green-600 font-semibold mb-1">
                            <i class="fas fa-tag mr-1"></i>Économisez {{ $yearlySavings }}%
                        </p>
                        @endif
                        <p class="text-sm text-gray-500 mb-6">Enfants & utilisateurs illimités</p>
                    </div>
                </div>
                @endif
                <ul class="space-y-3 text-sm">
                    @foreach($plan->features as $feature)
                    <li class="flex items-start text-gray-700">
                        <i class="fas fa-check text-green-500 mr-3 mt-1"></i>
                        <span>{{ $feature }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-12">
            <p class="text-gray-500 text-sm mb-4">
                <i class="fas fa-info-circle mr-1"></i>
                Tous les modules (garderie, cantine, stock, et les futurs) sont inclus dans chaque offre. Vous les activez ou désactivez selon vos besoins depuis votre espace d'administration.
            </p>
            <a href="#contact" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors">
                <i class="fas fa-rocket mr-2"></i>
                Demander une démo
            </a>
        </div>
    </div>
</section>

<!-- Roadmap -->
<section id="roadmap" class="py-20 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Ce qui arrive bientôt</h2>
            <p class="text-gray-500 text-lg max-w-2xl mx-auto">Nous enrichissons continuellement {{ $appName }} avec de nouveaux modules pour répondre à tous les besoins des collectivités.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-100">
                <div class="flex items-center mb-4">
                    <div class="bg-green-600 rounded-xl w-12 h-12 flex items-center justify-center mr-3">
                        <i class="fas fa-globe text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Page publique mairie</h3>
                        <span class="text-xs text-green-600 font-medium">Q1 2027</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600">Création d'un site public pour la mairie : actualités, horaires, services en ligne, contact. Une vitrine numérique personnalisable sans compétences techniques.</p>
            </div>
            <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl p-6 border border-orange-100">
                <div class="flex items-center mb-4">
                    <div class="bg-orange-600 rounded-xl w-12 h-12 flex items-center justify-center mr-3">
                        <i class="fas fa-calendar-check text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Réservation de salles & matériels</h3>
                        <span class="text-xs text-orange-600 font-medium">Q2 2027</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600">Mise à disposition des salles municipales et du matériel en libre-service. Calendrier de réservation en ligne, gestion des disponibilités et validation des demandes par les agents.</p>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-100">
                <div class="flex items-center mb-4">
                    <div class="bg-purple-600 rounded-xl w-12 h-12 flex items-center justify-center mr-3">
                        <i class="fas fa-bell text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Alertes citoyens & agenda public</h3>
                        <span class="text-xs text-purple-600 font-medium">Q3 2027</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600">Signalement d'incidents par les citoyens (voirie, éclairage, propreté...), suivi des traitements et agenda public des événements municipaux accessible à tous.</p>
            </div>
            <div class="bg-gradient-to-br from-gray-50 to-slate-50 rounded-2xl p-6 border border-gray-200">
                <div class="flex items-center mb-4">
                    <div class="bg-gray-700 rounded-xl w-12 h-12 flex items-center justify-center mr-3">
                        <i class="fas fa-lightbulb text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Et plus encore...</h3>
                        <span class="text-xs text-gray-600 font-medium">Vos idées</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600">Nous développons {{ $appName }} avec et pour les utilisateurs. Une idée de fonctionnalité ? Partagez-la avec nous, nous l'étudierons.</p>
            </div>
        </div>
    </div>
</section>

<!-- Demo / Registration -->
<section id="demo" class="hero-gradient text-white py-20 px-4">
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-10">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Demandez votre démo gratuite</h2>
            <p class="text-blue-100 text-lg">30 jours d'essai, sans engagement. Nous vous contacterons pour configurer votre espace.</p>
        </div>

        @if(session('error'))
        <div class="bg-red-500/20 border border-red-400 rounded-lg p-4 mb-6 text-white text-sm">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
        @endif

        <form action="{{ route('landing.register') }}" method="POST" class="bg-white rounded-2xl shadow-2xl p-8 space-y-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom et prénom *</label>
                    <input type="text" name="name" required value="{{ old('name') }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 text-sm"
                           placeholder="Jean Dupont">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" required value="{{ old('email') }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 text-sm"
                           placeholder="jean.dupont@mairie.fr">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                    <input type="tel" name="phone" required value="{{ old('phone') }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 text-sm"
                           placeholder="06 12 34 56 78">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Commune / Organisation</label>
                    <input type="text" name="organization_name" value="{{ old('organization_name') }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 text-sm"
                           placeholder="Mairie de...">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Adresse *</label>
                <input type="text" name="address" required value="{{ old('address') }}"
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 text-sm"
                       placeholder="12 rue de la Mairie">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                    <input type="text" name="city" value="{{ old('city') }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 text-sm"
                           placeholder="Paris">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code postal</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code') }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 text-sm"
                           placeholder="75001">
                </div>
            </div>
            <div class="pt-2">
                <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-4 rounded-xl font-semibold text-lg transition-all shadow-lg hover:shadow-xl">
                    <i class="fas fa-rocket mr-2"></i> Demander ma démo gratuite
                </button>
            </div>
            <p class="text-center text-xs text-gray-400">
                <i class="fas fa-shield-alt mr-1"></i>
                Vos données sont protégées et ne seront jamais partagées. Conforme RGPD.
            </p>
        </form>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-gray-400 py-12 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <div>
                <div class="flex items-center mb-4">
                    <div class="bg-blue-600 rounded-lg w-10 h-10 flex items-center justify-center mr-3">
                        <i class="fas fa-users text-white text-lg"></i>
                    </div>
                    <span class="text-xl font-bold text-white">{{ $appName }}</span>
                </div>
                <p class="text-sm">La plateforme de gestion municipale pensée pour les collectivités.</p>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4 text-sm">Produit</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#features" class="hover:text-white transition-colors">Fonctionnalités</a></li>
                    <li><a href="#modules" class="hover:text-white transition-colors">Modules</a></li>
                    <li><a href="#pricing" class="hover:text-white transition-colors">Tarifs</a></li>
                    <li><a href="#roadmap" class="hover:text-white transition-colors">Roadmap</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4 text-sm">Société</h4>
                <ul class="space-y-2 text-sm">
                    @if(Route::has('legal.cgv'))
                    <li><a href="{{ route('legal.cgv') }}" class="hover:text-white transition-colors">CGV</a></li>
                    @endif
                    @if(Route::has('legal.mentions'))
                    <li><a href="{{ route('legal.mentions') }}" class="hover:text-white transition-colors">Mentions légales</a></li>
                    @endif
                    @if(Route::has('legal.rgpd'))
                    <li><a href="{{ route('legal.rgpd') }}" class="hover:text-white transition-colors">RGPD</a></li>
                    @endif
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4 text-sm">Connexion</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('login') }}" class="hover:text-white transition-colors"><i class="fas fa-sign-in-alt mr-2"></i>Se connecter</a></li>
                    <li><a href="#demo" class="hover:text-white transition-colors"><i class="fas fa-rocket mr-2"></i>Demander une démo</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 pt-8 text-center text-sm">
            <p>&copy; {{ date('Y') }} {{ $appName }} — DailyDesk. Tous droits réservés.</p>
            <p class="mt-2 text-gray-500">Site développé à Libourne par <a href="https://smallwebconcept.fr" target="_blank" class="text-orange-400 hover:text-orange-300 font-medium">SmallWebConcept</a></p>
        </div>
    </div>
</footer>

</body>
</html>
