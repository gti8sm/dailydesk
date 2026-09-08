<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Communeo — Gestion municipale pour les mairies</title>
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
                <span class="text-2xl font-bold text-gray-900">Communeo</span>
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
            Communeo digitalise la garderie, la cantine et la gestion des familles
            pour les mairies et collectivités. Tout-en-un, accessible depuis anywhere.
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

<!-- Modules -->
<section id="modules" class="bg-gray-50 py-20 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Des modules adaptés à vos besoins</h2>
            <p class="text-gray-500 text-lg max-w-2xl mx-auto">Activez uniquement les modules dont vous avez besoin. Évoluez à votre rythme.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl shadow-lg p-8 border-2 border-blue-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Starter</h3>
                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">Actuel</span>
                </div>
                <p class="text-3xl font-bold text-gray-900 mb-1">49€<span class="text-base font-normal text-gray-500">/mois</span></p>
                <p class="text-sm text-gray-500 mb-6">Pour les petites communes</p>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-center text-gray-700"><i class="fas fa-check text-green-500 mr-3"></i> Module Garderie</li>
                    <li class="flex items-center text-gray-700"><i class="fas fa-check text-green-500 mr-3"></i> Jusqu'à 50 enfants</li>
                    <li class="flex items-center text-gray-700"><i class="fas fa-check text-green-500 mr-3"></i> Gestion des familles</li>
                    <li class="flex items-center text-gray-700"><i class="fas fa-check text-green-500 mr-3"></i> Support email</li>
                </ul>
            </div>
            <div class="bg-white rounded-2xl shadow-xl p-8 border-2 border-blue-500 relative scale-105">
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-blue-600 text-white px-4 py-1 rounded-full text-xs font-semibold">
                    Populaire
                </div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Pro</h3>
                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">Actuel</span>
                </div>
                <p class="text-3xl font-bold text-gray-900 mb-1">99€<span class="text-base font-normal text-gray-500">/mois</span></p>
                <p class="text-sm text-gray-500 mb-6">Pour les communes moyennes</p>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-center text-gray-700"><i class="fas fa-check text-green-500 mr-3"></i> Module Garderie</li>
                    <li class="flex items-center text-gray-700"><i class="fas fa-check text-green-500 mr-3"></i> Module Cantine</li>
                    <li class="flex items-center text-gray-700"><i class="fas fa-check text-green-500 mr-3"></i> Jusqu'à 150 enfants</li>
                    <li class="flex items-center text-gray-700"><i class="fas fa-check text-green-500 mr-3"></i> Notifications parents</li>
                    <li class="flex items-center text-gray-700"><i class="fas fa-check text-green-500 mr-3"></i> Support prioritaire</li>
                </ul>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-8 border-2 border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Premium</h3>
                    <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-semibold">Sur devis</span>
                </div>
                <p class="text-3xl font-bold text-gray-900 mb-1">199€<span class="text-base font-normal text-gray-500">/mois</span></p>
                <p class="text-sm text-gray-500 mb-6">Pour les grandes collectivités</p>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-center text-gray-700"><i class="fas fa-check text-green-500 mr-3"></i> Tous les modules</li>
                    <li class="flex items-center text-gray-700"><i class="fas fa-check text-green-500 mr-3"></i> Enfants illimités</li>
                    <li class="flex items-center text-gray-700"><i class="fas fa-check text-green-500 mr-3"></i> Multi-sites</li>
                    <li class="flex items-center text-gray-700"><i class="fas fa-check text-green-500 mr-3"></i> API & intégrations</li>
                    <li class="flex items-center text-gray-700"><i class="fas fa-check text-green-500 mr-3"></i> Support dédié</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Roadmap -->
<section id="roadmap" class="py-20 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Ce qui arrive bientôt</h2>
            <p class="text-gray-500 text-lg max-w-2xl mx-auto">Nous enrichissons continuellement Communeo avec de nouveaux modules pour répondre à tous les besoins des collectivités.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-100">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-600 rounded-xl w-12 h-12 flex items-center justify-center mr-3">
                        <i class="fas fa-calendar-check text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Réservation d'activités</h3>
                        <span class="text-xs text-blue-600 font-medium">Q1 2027</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600">Réservation en ligne des activités périscolaires, ALSH et vacances. Calendrier interactif, gestion des places et listes d'attente.</p>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-100">
                <div class="flex items-center mb-4">
                    <div class="bg-green-600 rounded-xl w-12 h-12 flex items-center justify-center mr-3">
                        <i class="fas fa-comments text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Communication parents-mairie</h3>
                        <span class="text-xs text-green-600 font-medium">Q1 2027</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600">Messagerie intégrée entre parents et mairie. Annonces groupées, alertes, et suivi des échanges par famille.</p>
            </div>
            <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl p-6 border border-orange-100">
                <div class="flex items-center mb-4">
                    <div class="bg-orange-600 rounded-xl w-12 h-12 flex items-center justify-center mr-3">
                        <i class="fas fa-euro-sign text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Facturation & paiements</h3>
                        <span class="text-xs text-orange-600 font-medium">Q2 2027</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600">Facturation automatique des repas et activités. Paiement en ligne, échéanciers, et rappels automatiques.</p>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-100">
                <div class="flex items-center mb-4">
                    <div class="bg-purple-600 rounded-xl w-12 h-12 flex items-center justify-center mr-3">
                        <i class="fas fa-bus text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Transport scolaire</h3>
                        <span class="text-xs text-purple-600 font-medium">Q2 2027</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600">Gestion des circuits de transport, affectation des enfants, suivi des présences dans les bus et alertes retards.</p>
            </div>
            <div class="bg-gradient-to-br from-red-50 to-rose-50 rounded-2xl p-6 border border-red-100">
                <div class="flex items-center mb-4">
                    <div class="bg-red-600 rounded-xl w-12 h-12 flex items-center justify-center mr-3">
                        <i class="fas fa-chart-bar text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Tableaux de bord avancés</h3>
                        <span class="text-xs text-red-600 font-medium">Q3 2027</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600">Statistiques détaillées : taux de fréquentation, coûts par enfant, tendances saisonnières, et exports pour les conseils municipaux.</p>
            </div>
            <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl p-6 border border-indigo-100">
                <div class="flex items-center mb-4">
                    <div class="bg-indigo-600 rounded-xl w-12 h-12 flex items-center justify-center mr-3">
                        <i class="fas fa-mobile-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Application mobile</h3>
                        <span class="text-xs text-indigo-600 font-medium">Q3 2027</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600">App iOS & Android pour les parents : notifications push, pointage, consultation des présences et signalements en un geste.</p>
            </div>
            <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-2xl p-6 border border-teal-100">
                <div class="flex items-center mb-4">
                    <div class="bg-teal-600 rounded-xl w-12 h-12 flex items-center justify-center mr-3">
                        <i class="fas fa-file-signature text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Documents & consentements</h3>
                        <span class="text-xs text-teal-600 font-medium">Q4 2027</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600">Gestion des documents administratifs, autorisations parentales numériques, et signatures électroniques pour les sorties et activités.</p>
            </div>
            <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-2xl p-6 border border-yellow-100">
                <div class="flex items-center mb-4">
                    <div class="bg-yellow-600 rounded-xl w-12 h-12 flex items-center justify-center mr-3">
                        <i class="fas fa-plug text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">API & intégrations</h3>
                        <span class="text-xs text-yellow-600 font-medium">Q4 2027</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600">API ouverte pour interconnecter Communeo avec votre logiciel comptable, votre SI existant, et les services de l'Éducation Nationale.</p>
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
                <p class="text-sm text-gray-600">Nous développons Communeo avec et pour les utilisateurs. Une idée de fonctionnalité ? Partagez-la avec nous, nous l'étudierons.</p>
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
                    <span class="text-xl font-bold text-white">Communeo</span>
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
                    <li><a href="#" class="hover:text-white transition-colors">À propos</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Contact</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Mentions légales</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">RGPD</a></li>
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
            <p>&copy; {{ date('Y') }} Communeo — DailyDesk. Tous droits réservés.</p>
        </div>
    </div>
</footer>

</body>
</html>
