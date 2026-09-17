<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DailyDesk — Présentation pour les mairies</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .slide { display: none; }
        .slide.active { display: flex; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .slide.active > * { animation: fadeInUp 0.5s ease-out; }
        .screen-mock {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        }
        .screen-mock-header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .screen-dot { width: 10px; height: 10px; border-radius: 50%; }
    </style>
</head>
<body class="bg-gray-900" x-data="{ current: 0, total: 14 }" x-init="window.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowRight' || e.key === ' ') { e.preventDefault(); if (current < total - 1) current++; }
    if (e.key === 'ArrowLeft') { e.preventDefault(); if (current > 0) current--; }
    if (e.key === 'Home') { current = 0; }
    if (e.key === 'End') { current = total - 1; }
})">

<!-- Slides -->
<div class="min-h-screen flex items-center justify-center p-4 sm:p-8 relative">

    <!-- SLIDE 0 : Page de garde -->
    <div class="slide" :class="{ 'active': current === 0 }">
        <div class="max-w-4xl text-center text-white">
            <div class="mb-8 inline-flex items-center justify-center w-24 h-24 bg-blue-600 rounded-2xl shadow-2xl">
                <i class="fas fa-building text-5xl text-white"></i>
            </div>
            <h1 class="text-5xl sm:text-7xl font-bold mb-4">DailyDesk</h1>
            <p class="text-xl sm:text-2xl text-blue-300 mb-2">La suite administrative de votre commune</p>
            <p class="text-lg text-gray-400 mb-12">Garderie · Cantine · Stock · Multi-écoles · Portail parent</p>
            <div class="flex flex-wrap justify-center gap-3 mb-12">
                <span class="px-4 py-2 bg-blue-600/20 border border-blue-500 rounded-full text-sm text-blue-300"><i class="fas fa-check mr-1"></i> 100% web</span>
                <span class="px-4 py-2 bg-blue-600/20 border border-blue-500 rounded-full text-sm text-blue-300"><i class="fas fa-check mr-1"></i> Multi-écoles</span>
                <span class="px-4 py-2 bg-blue-600/20 border border-blue-500 rounded-full text-sm text-blue-300"><i class="fas fa-check mr-1"></i> Portail parent</span>
                <span class="px-4 py-2 bg-blue-600/20 border border-blue-500 rounded-full text-sm text-blue-300"><i class="fas fa-check mr-1"></i> Support inclus</span>
            </div>
            <p class="text-sm text-gray-500">Présentation interactive — Utilisez les flèches ← → ou Espace pour naviguer</p>
        </div>
    </div>

    <!-- SLIDE 1 : Le constat -->
    <div class="slide" :class="{ 'active': current === 1 }">
        <div class="max-w-5xl text-white">
            <div class="text-center mb-10">
                <span class="px-4 py-1 bg-red-500/20 text-red-400 rounded-full text-sm font-medium">Le constat</span>
                <h2 class="text-3xl sm:text-5xl font-bold mt-4">La gestion périscolaire, un casse-tête quotidien</h2>
            </div>
            <div class="grid sm:grid-cols-2 gap-6">
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <i class="fas fa-clipboard-list text-red-500 text-3xl mb-3"></i>
                    <h3 class="text-lg font-semibold mb-2">Papier et tableurs</h3>
                    <p class="text-gray-400 text-sm">Présences notées à la main, feuilles Excel non synchronisées, pertes d'informations entre équipes.</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <i class="fas fa-users-slash text-red-500 text-3xl mb-3"></i>
                    <h3 class="text-lg font-semibold mb-2">Communication parents</h3>
                    <p class="text-gray-400 text-sm">Cahiers de liaison perdus, menus cantine non communiqués, informations médicales difficiles à suivre.</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <i class="fas fa-school text-red-500 text-3xl mb-3"></i>
                    <h3 class="text-lg font-semibold mb-2">Multi-écoles</h3>
                    <p class="text-gray-400 text-sm">Plusieurs écoles dans la commune sans vision centralisée. Les agents ne savent pas où intervenir.</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <i class="fas fa-clock text-red-500 text-3xl mb-3"></i>
                    <h3 class="text-lg font-semibold mb-2">Temps perdu</h3>
                    <p class="text-gray-400 text-sm">Saisie manuelle, reports de données, calculs de facturation fastidieux à la fin du mois.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- SLIDE 2 : La solution -->
    <div class="slide" :class="{ 'active': current === 2 }">
        <div class="max-w-5xl text-white">
            <div class="text-center mb-10">
                <span class="px-4 py-1 bg-green-500/20 text-green-400 rounded-full text-sm font-medium">La solution</span>
                <h2 class="text-3xl sm:text-5xl font-bold mt-4">Une plateforme unique pour toute la commune</h2>
                <p class="text-gray-400 mt-4">Tout le périscolaire et l'administratif dans un seul outil, accessible à chaque acteur</p>
            </div>
            <div class="grid sm:grid-cols-3 gap-6">
                <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-6 text-center">
                    <i class="fas fa-child text-4xl mb-3"></i>
                    <h3 class="text-lg font-semibold">Garderie</h3>
                    <p class="text-blue-100 text-sm mt-1">Présences, arrivées/départs, événements</p>
                </div>
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-center">
                    <i class="fas fa-utensils text-4xl mb-3"></i>
                    <h3 class="text-lg font-semibold">Cantine</h3>
                    <p class="text-orange-100 text-sm mt-1">Présences, menus, allergènes, facturation</p>
                </div>
                <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-xl p-6 text-center">
                    <i class="fas fa-boxes-stacked text-4xl mb-3"></i>
                    <h3 class="text-lg font-semibold">Stock</h3>
                    <p class="text-indigo-100 text-sm mt-1">Lieux, mouvements, seuils d'alerte</p>
                </div>
            </div>
            <div class="mt-8 grid sm:grid-cols-4 gap-4 text-center">
                <div><i class="fas fa-school text-2xl text-blue-400 mb-2"></i><p class="text-sm text-gray-400">Multi-écoles</p></div>
                <div><i class="fas fa-home text-2xl text-purple-400 mb-2"></i><p class="text-sm text-gray-400">Portail parent</p></div>
                <div><i class="fas fa-mobile-screen text-2xl text-green-400 mb-2"></i><p class="text-sm text-gray-400">Responsive</p></div>
                <div><i class="fas fa-life-ring text-2xl text-orange-400 mb-2"></i><p class="text-sm text-gray-400">Support inclus</p></div>
            </div>
        </div>
    </div>

    <!-- SLIDE 3 : Module Garderie -->
    <div class="slide" :class="{ 'active': current === 3 }">
        <div class="max-w-6xl w-full text-white">
            <div class="text-center mb-8">
                <span class="px-4 py-1 bg-blue-500/20 text-blue-400 rounded-full text-sm font-medium">Module 1</span>
                <h2 class="text-3xl sm:text-5xl font-bold mt-4"><i class="fas fa-child text-blue-500 mr-3"></i>Garderie / ALSH</h2>
                <p class="text-gray-400 mt-3">Gérez les présences matin, midi et soir en un clic</p>
            </div>
            <div class="grid lg:grid-cols-2 gap-8 items-center">
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-hand-pointer text-white"></i></div>
                        <div><h3 class="font-semibold">Saisie en un clic</h3><p class="text-sm text-gray-400">Cliquez sur un enfant pour enregistrer son arrivée ou son départ. L'heure est horodatée automatiquement.</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-calendar text-white"></i></div>
                        <div><h3 class="font-semibold">Historique par date</h3><p class="text-sm text-gray-400">Consultez les présences de n'importe quel jour. Exportez en Excel ou PDF pour la facturation.</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-exclamation-triangle text-white"></i></div>
                        <div><h3 class="font-semibold">Événements</h3><p class="text-sm text-gray-400">Signalez un incident, un comportement ou un accident. Les parents sont notifiés automatiquement.</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-school text-white"></i></div>
                        <div><h3 class="font-semibold">Filtré par école</h3><p class="text-sm text-gray-400">L'agent ne voit que les enfants de son école. L'admin voit toutes les écoles.</p></div>
                    </div>
                </div>
                <div class="screen-mock">
                    <div class="screen-mock-header">
                        <div class="screen-dot bg-red-400"></div>
                        <div class="screen-dot bg-yellow-400"></div>
                        <div class="screen-dot bg-green-400"></div>
                        <span class="ml-2 text-xs text-gray-400">DailyDesk — Garderie</span>
                    </div>
                    <div class="p-4 text-gray-800">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-bold text-blue-600"><i class="fas fa-child mr-1"></i> Garderie</h4>
                            <input type="date" class="text-xs px-2 py-1 border rounded" value="2026-09-17" disabled>
                        </div>
                        <div class="grid grid-cols-3 gap-2 mb-3">
                            <div class="bg-green-100 rounded p-2 text-center"><p class="text-lg font-bold text-green-600">24</p><p class="text-xs text-gray-500">Présents</p></div>
                            <div class="bg-red-100 rounded p-2 text-center"><p class="text-lg font-bold text-red-500">3</p><p class="text-xs text-gray-500">Absents</p></div>
                            <div class="bg-gray-100 rounded p-2 text-center"><p class="text-lg font-bold text-gray-700">27</p><p class="text-xs text-gray-500">Inscrits</p></div>
                        </div>
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg p-2">
                                <span class="text-sm font-medium">Lucas Martin</span>
                                <span class="text-xs text-green-600"><i class="fas fa-check-circle"></i> Arrivé 07:42</span>
                            </div>
                            <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg p-2">
                                <span class="text-sm font-medium">Emma Dubois</span>
                                <span class="text-xs text-green-600"><i class="fas fa-check-circle"></i> Arrivé 07:55</span>
                            </div>
                            <div class="flex items-center justify-between bg-red-50 border border-red-200 rounded-lg p-2">
                                <span class="text-sm font-medium">Léa Bernard</span>
                                <span class="text-xs text-red-500"><i class="fas fa-times-circle"></i> Absente</span>
                            </div>
                            <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg p-2">
                                <span class="text-sm font-medium">Tom Petit</span>
                                <span class="text-xs text-green-600"><i class="fas fa-check-circle"></i> Arrivé 08:10</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SLIDE 4 : Module Cantine -->
    <div class="slide" :class="{ 'active': current === 4 }">
        <div class="max-w-6xl w-full text-white">
            <div class="text-center mb-8">
                <span class="px-4 py-1 bg-orange-500/20 text-orange-400 rounded-full text-sm font-medium">Module 2</span>
                <h2 class="text-3xl sm:text-5xl font-bold mt-4"><i class="fas fa-utensils text-orange-500 mr-3"></i>Cantine</h2>
                <p class="text-gray-400 mt-3">Présences, menus et allergènes — tout est centralisé</p>
            </div>
            <div class="grid lg:grid-cols-2 gap-8 items-center">
                <div class="screen-mock order-2 lg:order-1">
                    <div class="screen-mock-header">
                        <div class="screen-dot bg-red-400"></div>
                        <div class="screen-dot bg-yellow-400"></div>
                        <div class="screen-dot bg-green-400"></div>
                        <span class="ml-2 text-xs text-gray-400">DailyDesk — Cantine</span>
                    </div>
                    <div class="p-4 text-gray-800">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-bold text-orange-600"><i class="fas fa-utensils mr-1"></i> Cantine — Déjeuner</h4>
                            <input type="date" class="text-xs px-2 py-1 border rounded" value="2026-09-17" disabled>
                        </div>
                        <div class="grid grid-cols-3 gap-2 mb-3">
                            <div class="bg-green-100 rounded p-2 text-center"><p class="text-lg font-bold text-green-600">32</p><p class="text-xs text-gray-500">Présents</p></div>
                            <div class="bg-red-100 rounded p-2 text-center"><p class="text-lg font-bold text-red-500">5</p><p class="text-xs text-gray-500">Absents</p></div>
                            <div class="bg-orange-100 rounded p-2 text-center"><p class="text-lg font-bold text-orange-600">2</p><p class="text-xs text-gray-500">Allergies</p></div>
                        </div>
                        <div class="bg-orange-50 rounded-lg p-3 mb-2">
                            <p class="text-xs font-semibold text-orange-700 mb-1">Menu du jour</p>
                            <p class="text-xs text-gray-600">Salade composée · Poulet rôti · Riz · Yaourt</p>
                        </div>
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg p-2">
                                <span class="text-sm font-medium">Lucas Martin</span>
                                <span class="text-xs text-green-600"><i class="fas fa-check-circle"></i> Présent</span>
                            </div>
                            <div class="flex items-center justify-between bg-red-50 border border-red-200 rounded-lg p-2">
                                <span class="text-sm font-medium">Emma Dubois <span class="text-xs text-red-500">⚠ Allergie gluten</span></span>
                                <span class="text-xs text-green-600"><i class="fas fa-check-circle"></i> Présent</span>
                            </div>
                            <div class="flex items-center justify-between bg-red-50 border border-red-200 rounded-lg p-2">
                                <span class="text-sm font-medium">Léa Bernard</span>
                                <span class="text-xs text-red-500"><i class="fas fa-times-circle"></i> Absente</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="space-y-4 order-1 lg:order-2">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-check text-white"></i></div>
                        <div><h3 class="font-semibold">Présences par repas</h3><p class="text-sm text-gray-400">Marquez les présents/absents d'un clic. Choisissez Déjeuner ou Goûter selon le moment.</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-exclamation text-white"></i></div>
                        <div><h3 class="font-semibold">Allergies & régimes</h3><p class="text-sm text-gray-400">Les allergies de chaque enfant sont affichées à côté de son nom. Aucune erreur possible.</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-file-lines text-white"></i></div>
                        <div><h3 class="font-semibold">Menus cantine</h3><p class="text-sm text-gray-400">Le cuisinier crée les menus (entrée, plat, garniture, dessert). Les parents les voient sur l'agenda.</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-school text-white"></i></div>
                        <div><h3 class="font-semibold">Mode global ou par école</h3><p class="text-sm text-gray-400">Un menu commun (cuisine centrale) ou un menu par école — au choix de la commune.</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SLIDE 5 : Module Stock -->
    <div class="slide" :class="{ 'active': current === 5 }">
        <div class="max-w-6xl w-full text-white">
            <div class="text-center mb-8">
                <span class="px-4 py-1 bg-indigo-500/20 text-indigo-400 rounded-full text-sm font-medium">Module 3</span>
                <h2 class="text-3xl sm:text-5xl font-bold mt-4"><i class="fas fa-boxes-stacked text-indigo-500 mr-3"></i>Gestion de stock</h2>
                <p class="text-gray-400 mt-3">Suivez les articles, les mouvements et les alertes</p>
            </div>
            <div class="grid lg:grid-cols-2 gap-8 items-center">
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-warehouse text-white"></i></div>
                        <div><h3 class="font-semibold">Lieux de stockage</h3><p class="text-sm text-gray-400">Cuisine, réserve, cellier — créez autant de lieux que nécessaire et suivez les quantités par article.</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-arrows-up-down text-white"></i></div>
                        <div><h3 class="font-semibold">Mouvements</h3><p class="text-sm text-gray-400">Enregistrez les entrées et sorties. Chaque mouvement est horodaté et tracé avec l'utilisateur.</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-bell text-white"></i></div>
                        <div><h3 class="font-semibold">Seuils d'alerte</h3><p class="text-sm text-gray-400">Définissez un seuil minimum par article. En dessous, une alerte est générée automatiquement.</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-user-shield text-white"></i></div>
                        <div><h3 class="font-semibold">Permissions fines</h3><p class="text-sm text-gray-400">Voir, Saisir, Gérer — attribuez les droits stock individuellement par utilisateur.</p></div>
                    </div>
                </div>
                <div class="screen-mock">
                    <div class="screen-mock-header">
                        <div class="screen-dot bg-red-400"></div>
                        <div class="screen-dot bg-yellow-400"></div>
                        <div class="screen-dot bg-green-400"></div>
                        <span class="ml-2 text-xs text-gray-400">DailyDesk — Stock</span>
                    </div>
                    <div class="p-4 text-gray-800">
                        <h4 class="font-bold text-indigo-600 mb-3"><i class="fas fa-boxes-stacked mr-1"></i> Stock — Cuisine</h4>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg p-2">
                                <div><span class="text-sm font-medium">Farine (kg)</span><p class="text-xs text-gray-400">Réserve</p></div>
                                <div class="text-right"><span class="text-sm font-bold text-green-600">25</span><p class="text-xs text-gray-400">seuil: 10</p></div>
                            </div>
                            <div class="flex items-center justify-between bg-red-50 border border-red-200 rounded-lg p-2">
                                <div><span class="text-sm font-medium">Lait (L)</span><p class="text-xs text-gray-400">Cuisine</p></div>
                                <div class="text-right"><span class="text-sm font-bold text-red-500">8</span><p class="text-xs text-red-500">⚠ seuil: 15</p></div>
                            </div>
                            <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg p-2">
                                <div><span class="text-sm font-medium">Pâtes (kg)</span><p class="text-xs text-gray-400">Réserve</p></div>
                                <div class="text-right"><span class="text-sm font-bold text-green-600">40</span><p class="text-xs text-gray-400">seuil: 20</p></div>
                            </div>
                            <div class="flex items-center justify-between bg-yellow-50 border border-yellow-200 rounded-lg p-2">
                                <div><span class="text-sm font-medium">Œufs (boîte)</span><p class="text-xs text-gray-400">Cuisine</p></div>
                                <div class="text-right"><span class="text-sm font-bold text-yellow-600">12</span><p class="text-xs text-gray-400">seuil: 12</p></div>
                            </div>
                        </div>
                        <div class="mt-3 bg-red-50 border border-red-200 rounded-lg p-2">
                            <p class="text-xs text-red-600"><i class="fas fa-bell mr-1"></i> 1 article sous le seuil d'alerte</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SLIDE 6 : Multi-écoles -->
    <div class="slide" :class="{ 'active': current === 6 }">
        <div class="max-w-5xl text-white">
            <div class="text-center mb-10">
                <span class="px-4 py-1 bg-blue-500/20 text-blue-400 rounded-full text-sm font-medium">Architecture</span>
                <h2 class="text-3xl sm:text-5xl font-bold mt-4"><i class="fas fa-school text-blue-500 mr-3"></i>Multi-écoles</h2>
                <p class="text-gray-400 mt-3">Pour les communes avec plusieurs écoles — chaque agent voit sa école</p>
            </div>
            <div class="grid sm:grid-cols-3 gap-6 mb-8">
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 text-center">
                    <i class="fas fa-school text-3xl text-blue-400 mb-3"></i>
                    <h3 class="font-semibold mb-1">École Jean Jaurès</h3>
                    <p class="text-xs text-gray-400">Maternelle · 3 classes · 68 enfants</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 text-center">
                    <i class="fas fa-school text-3xl text-green-400 mb-3"></i>
                    <h3 class="font-semibold mb-1">École Victor Hugo</h3>
                    <p class="text-xs text-gray-400">Élémentaire · 5 classes · 112 enfants</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 text-center">
                    <i class="fas fa-school text-3xl text-orange-400 mb-3"></i>
                    <h3 class="font-semibold mb-1">Collège Pasteur</h3>
                    <p class="text-xs text-gray-400">Collège · 8 classes · 180 enfants</p>
                </div>
            </div>
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <div class="grid sm:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-semibold mb-2"><i class="fas fa-user-tie text-blue-400 mr-2"></i>Agent limité à une école</h3>
                        <ul class="text-sm text-gray-400 space-y-1">
                            <li><i class="fas fa-check text-green-500 mr-1"></i> Ne voit que les enfants de son école</li>
                            <li><i class="fas fa-check text-green-500 mr-1"></i> Présences et menus filtrés automatiquement</li>
                            <li><i class="fas fa-check text-green-500 mr-1"></i> Sélecteur d'école masqué (lecture seule)</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-2"><i class="fas fa-user-shield text-green-400 mr-2"></i>Admin global</h3>
                        <ul class="text-sm text-gray-400 space-y-1">
                            <li><i class="fas fa-check text-green-500 mr-1"></i> Voit toutes les écoles ou en sélectionne une</li>
                            <li><i class="fas fa-check text-green-500 mr-1"></i> Sélecteur d'école dans le header</li>
                            <li><i class="fas fa-check text-green-500 mr-1"></i> Vue agrégée ou par école</li>
                        </ul>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-700">
                    <h3 class="font-semibold mb-2"><i class="fas fa-people-arrows text-orange-400 mr-2"></i>Remplacements</h3>
                    <p class="text-sm text-gray-400">Un agent peut être autorisé à accéder à d'autres écoles pour des remplacements. Il bascule entre ses écoles via le sélecteur du header.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- SLIDE 7 : Rôles & Permissions -->
    <div class="slide" :class="{ 'active': current === 7 }">
        <div class="max-w-5xl text-white">
            <div class="text-center mb-10">
                <span class="px-4 py-1 bg-purple-500/20 text-purple-400 rounded-full text-sm font-medium">Sécurité</span>
                <h2 class="text-3xl sm:text-5xl font-bold mt-4"><i class="fas fa-id-badge text-purple-500 mr-3"></i>Rôles & Permissions</h2>
                <p class="text-gray-400 mt-3">Chaque utilisateur a le bon niveau d'accès — ni plus, ni moins</p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
                    <i class="fas fa-user-shield text-red-400 text-2xl mb-2"></i>
                    <h3 class="font-semibold">Admin mairie</h3>
                    <p class="text-xs text-gray-400 mt-1">Accès complet : utilisateurs, écoles, paramètres, imports</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
                    <i class="fas fa-user-clock text-blue-400 text-2xl mb-2"></i>
                    <h3 class="font-semibold">Personnel mairie</h3>
                    <p class="text-xs text-gray-400 mt-1">Saisie des présences garderie + cantine</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
                    <i class="fas fa-chalkboard-teacher text-green-400 text-2xl mb-2"></i>
                    <h3 class="font-semibold">Enseignant</h3>
                    <p class="text-xs text-gray-400 mt-1">Création d'événements sur ses élèves</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
                    <i class="fas fa-child text-purple-400 text-2xl mb-2"></i>
                    <h3 class="font-semibold">Agent ALSH</h3>
                    <p class="text-xs text-gray-400 mt-1">Présences garderie + cantine, événements</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
                    <i class="fas fa-utensils text-orange-400 text-2xl mb-2"></i>
                    <h3 class="font-semibold">Cuisinier</h3>
                    <p class="text-xs text-gray-400 mt-1">Création et publication des menus cantine</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
                    <i class="fas fa-home text-indigo-400 text-2xl mb-2"></i>
                    <h3 class="font-semibold">Parent</h3>
                    <p class="text-xs text-gray-400 mt-1">Portail parent : présences, menus, signalements</p>
                </div>
            </div>
            <p class="text-center text-sm text-gray-500 mt-6"><i class="fas fa-info-circle mr-1"></i> Un utilisateur peut avoir plusieurs rôles (ex: agent garderie + cantine)</p>
        </div>
    </div>

    <!-- SLIDE 8 : Portail Parent -->
    <div class="slide" :class="{ 'active': current === 8 }">
        <div class="max-w-6xl w-full text-white">
            <div class="text-center mb-8">
                <span class="px-4 py-1 bg-indigo-500/20 text-indigo-400 rounded-full text-sm font-medium">Communication</span>
                <h2 class="text-3xl sm:text-5xl font-bold mt-4"><i class="fas fa-home text-indigo-500 mr-3"></i>Portail Parent</h2>
                <p class="text-gray-400 mt-3">Les parents suivent leurs enfants — sans appeler la mairie</p>
            </div>
            <div class="grid lg:grid-cols-2 gap-8 items-center">
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-calendar-check text-white"></i></div>
                        <div><h3 class="font-semibold">Présences en temps réel</h3><p class="text-sm text-gray-400">Les parents consultent les arrivées et départs de leurs enfants, à la garderie comme à la cantine.</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-utensils text-white"></i></div>
                        <div><h3 class="font-semibold">Menus cantine</h3><p class="text-sm text-gray-400">Les menus publiés par le cuisinier apparaissent sur l'agenda parent. Plus besoin de les imprimer.</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-bell text-white"></i></div>
                        <div><h3 class="font-semibold">Notifications</h3><p class="text-sm text-gray-400">Recevez une notification à l'arrivée ou au départ de l'enfant (si activé par la mairie).</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0"><i class="fas fa-exclamation-circle text-white"></i></div>
                        <div><h3 class="font-semibold">Signalements</h3><p class="text-sm text-gray-400">Le parent signale une allergie, un régime ou une info médicale. L'équipe est notifiée immédiatement.</p></div>
                    </div>
                </div>
                <div class="screen-mock">
                    <div class="screen-mock-header">
                        <div class="screen-dot bg-red-400"></div>
                        <div class="screen-dot bg-yellow-400"></div>
                        <div class="screen-dot bg-green-400"></div>
                        <span class="ml-2 text-xs text-gray-400">DailyDesk — Espace Parent</span>
                    </div>
                    <div class="p-4 text-gray-800">
                        <div class="bg-indigo-50 rounded-lg p-3 mb-3">
                            <h4 class="font-bold text-indigo-700">Bonjour, Famille Martin</h4>
                            <p class="text-xs text-gray-500">2 enfants inscrits</p>
                        </div>
                        <div class="space-y-3">
                            <div class="border rounded-lg p-3">
                                <p class="text-sm font-semibold">Lucas — CP</p>
                                <div class="mt-2 space-y-1">
                                    <div class="flex justify-between text-xs"><span><i class="fas fa-child text-blue-500"></i> Garderie</span><span class="text-green-600">Arrivé 07:42</span></div>
                                    <div class="flex justify-between text-xs"><span><i class="fas fa-utensils text-orange-500"></i> Cantine</span><span class="text-green-600">Présent (Déjeuner)</span></div>
                                </div>
                            </div>
                            <div class="border rounded-lg p-3">
                                <p class="text-sm font-semibold">Emma — CE1</p>
                                <div class="mt-2 space-y-1">
                                    <div class="flex justify-between text-xs"><span><i class="fas fa-child text-blue-500"></i> Garderie</span><span class="text-green-600">Arrivé 07:55</span></div>
                                    <div class="flex justify-between text-xs"><span><i class="fas fa-utensils text-orange-500"></i> Cantine</span><span class="text-green-600">Présent (Déjeuner)</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 bg-orange-50 rounded-lg p-2">
                            <p class="text-xs text-orange-700"><i class="fas fa-utensils mr-1"></i> Menu du jour : Salade · Poulet rôti · Riz · Yaourt</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SLIDE 9 : Dashboard -->
    <div class="slide" :class="{ 'active': current === 9 }">
        <div class="max-w-6xl w-full text-white">
            <div class="text-center mb-8">
                <span class="px-4 py-1 bg-gray-500/20 text-gray-400 rounded-full text-sm font-medium">Vue d'ensemble</span>
                <h2 class="text-3xl sm:text-5xl font-bold mt-4"><i class="fas fa-chart-line text-green-500 mr-3"></i>Tableau de bord</h2>
                <p class="text-gray-400 mt-3">Pilotez toute la commune d'un seul regard</p>
            </div>
            <div class="screen-mock">
                <div class="screen-mock-header">
                    <div class="screen-dot bg-red-400"></div>
                    <div class="screen-dot bg-yellow-400"></div>
                    <div class="screen-dot bg-green-400"></div>
                    <span class="ml-2 text-xs text-gray-400">DailyDesk — Dashboard</span>
                </div>
                <div class="p-6 text-gray-800">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <i class="fas fa-child text-blue-500 text-xl mb-1"></i>
                            <p class="text-2xl font-bold text-blue-600">24</p>
                            <p class="text-xs text-gray-500">Présents garderie</p>
                        </div>
                        <div class="bg-orange-50 rounded-lg p-4">
                            <i class="fas fa-utensils text-orange-500 text-xl mb-1"></i>
                            <p class="text-2xl font-bold text-orange-600">32</p>
                            <p class="text-xs text-gray-500">Présents cantine</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <i class="fas fa-users text-green-500 text-xl mb-1"></i>
                            <p class="text-2xl font-bold text-green-600">187</p>
                            <p class="text-xs text-gray-500">Enfants inscrits</p>
                        </div>
                        <div class="bg-red-50 rounded-lg p-4">
                            <i class="fas fa-bell text-red-500 text-xl mb-1"></i>
                            <p class="text-2xl font-bold text-red-600">1</p>
                            <p class="text-xs text-gray-500">Alerte stock</p>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="border rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-calendar-day text-blue-500 mr-1"></i> Événements récents</h4>
                            <div class="space-y-1 text-xs text-gray-500">
                                <div>• Sortie scolaire — 20/09</div>
                                <div>• Réunion parents — 25/09</div>
                                <div>• Vaccination CP — 28/09</div>
                            </div>
                        </div>
                        <div class="border rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-utensils text-orange-500 mr-1"></i> Menu du jour</h4>
                            <p class="text-xs text-gray-500">Salade · Poulet rôti · Riz · Yaourt</p>
                            <p class="text-xs text-green-600 mt-1"><i class="fas fa-check"></i> Publié sur le portail parent</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SLIDE 10 : Sécurité & RGPD -->
    <div class="slide" :class="{ 'active': current === 10 }">
        <div class="max-w-5xl text-white">
            <div class="text-center mb-10">
                <span class="px-4 py-1 bg-green-500/20 text-green-400 rounded-full text-sm font-medium">Conformité</span>
                <h2 class="text-3xl sm:text-5xl font-bold mt-4"><i class="fas fa-shield-alt text-green-500 mr-3"></i>Sécurité & RGPD</h2>
                <p class="text-gray-400 mt-3">Les données des enfants sont protégées</p>
            </div>
            <div class="grid sm:grid-cols-2 gap-6">
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <i class="fas fa-lock text-green-400 text-3xl mb-3"></i>
                    <h3 class="font-semibold mb-2">Données chiffrées</h3>
                    <p class="text-sm text-gray-400">Toutes les communications sont chiffrées (HTTPS). Les mots de passe sont hachés (bcrypt).</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <i class="fas fa-user-shield text-green-400 text-3xl mb-3"></i>
                    <h3 class="font-semibold mb-2">Isolation par tenant</h3>
                    <p class="text-sm text-gray-400">Chaque mairie a ses propres données. Aucune fuite entre communes.</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <i class="fas fa-network-wired text-green-400 text-3xl mb-3"></i>
                    <h3 class="font-semibold mb-2">Whitelist IP</h3>
                    <p class="text-sm text-gray-400">Les admins peuvent restreindre les connexions à certaines adresses IP (mairie, écoles).</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <i class="fas fa-fingerprint text-green-400 text-3xl mb-3"></i>
                    <h3 class="font-semibold mb-2">Code PIN</h3>
                    <p class="text-sm text-gray-400">Accès rapide par code PIN (4-6 chiffres) pour les agents en mobilité (tablette, smartphone).</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <i class="fas fa-file-contract text-green-400 text-3xl mb-3"></i>
                    <h3 class="font-semibold mb-2">Conformité RGPD</h3>
                    <p class="text-sm text-gray-400">Registre de traitement, droit à l'oubli, export des données. Hébergement France.</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <i class="fas fa-history text-green-400 text-3xl mb-3"></i>
                    <h3 class="font-semibold mb-2">Journal d'activité</h3>
                    <p class="text-sm text-gray-400">Toutes les actions sont tracées : qui, quoi, quand. Audit complet à tout moment.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- SLIDE 11 : Support -->
    <div class="slide" :class="{ 'active': current === 11 }">
        <div class="max-w-5xl text-white">
            <div class="text-center mb-10">
                <span class="px-4 py-1 bg-orange-500/20 text-orange-400 rounded-full text-sm font-medium">Accompagnement</span>
                <h2 class="text-3xl sm:text-5xl font-bold mt-4"><i class="fas fa-life-ring text-orange-500 mr-3"></i>Support inclus</h2>
                <p class="text-gray-400 mt-3">Une équipe à votre écoute — pas de ticket d'assistance perdu</p>
            </div>
            <div class="grid sm:grid-cols-2 gap-6 mb-8">
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <i class="fas fa-ticket text-orange-400 text-3xl mb-3"></i>
                    <h3 class="font-semibold mb-2">Tickets de support</h3>
                    <p class="text-sm text-gray-400">Ouvrez un ticket depuis l'outil. Suivez les réponses, joignez des captures d'écran. Catégorisez (technique, bug, suggestion).</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <i class="fas fa-bell text-orange-400 text-3xl mb-3"></i>
                    <h3 class="font-semibold mb-2">Badge de notification</h3>
                    <p class="text-sm text-gray-400">Un badge rouge indique les nouvelles réponses non lues. Le badge disparaît à la lecture.</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <i class="fas fa-archive text-orange-400 text-3xl mb-3"></i>
                    <h3 class="font-semibold mb-2">Archivage</h3>
                    <p class="text-sm text-gray-400">Les tickets résolus sont archivés. Les tickets des tenants suspendus sont auto-archivés.</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <i class="fas fa-graduation-cap text-orange-400 text-3xl mb-3"></i>
                    <h3 class="font-semibold mb-2">Onboarding</h3>
                    <p class="text-sm text-gray-400">Assistant en 4 étapes à l'installation : mairie, classes, couleurs, notifications. Formation à distance incluse.</p>
                </div>
            </div>
            <div class="bg-gradient-to-r from-orange-600 to-orange-500 rounded-xl p-6 text-center">
                <p class="text-lg font-semibold">Temps de réponse selon le plan</p>
                <div class="grid grid-cols-3 gap-4 mt-4">
                    <div><p class="text-2xl font-bold">48h</p><p class="text-sm text-orange-100">Village</p></div>
                    <div><p class="text-2xl font-bold">24h</p><p class="text-sm text-orange-100">Petite commune</p></div>
                    <div><p class="text-2xl font-bold">J</p><p class="text-sm text-orange-100">Commune moyenne +</p></div>
                </div>
            </div>
        </div>
    </div>

    <!-- SLIDE 12 : Tarification -->
    <div class="slide" :class="{ 'active': current === 12 }">
        <div class="max-w-5xl text-white">
            <div class="text-center mb-10">
                <span class="px-4 py-1 bg-green-500/20 text-green-400 rounded-full text-sm font-medium">Tarification</span>
                <h2 class="text-3xl sm:text-5xl font-bold mt-4"><i class="fas fa-tags text-green-500 mr-3"></i>Une tarification simple</h2>
                <p class="text-gray-400 mt-3">Basée sur la population de votre commune — tous les modules inclus</p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <h3 class="text-lg font-bold text-gray-300">Village</h3>
                    <p class="text-xs text-gray-500 mb-3">< 1 000 habitants</p>
                    <p class="text-3xl font-bold text-white">39 €<span class="text-sm font-normal text-gray-400">/mois</span></p>
                    <p class="text-xs text-gray-400 mt-2">Support email (48h)</p>
                </div>
                <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-6 border-2 border-blue-500 transform scale-105 relative">
                    <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-blue-500 text-white text-xs px-3 py-1 rounded-full">Le plus choisi</span>
                    <h3 class="text-lg font-bold">Petite commune</h3>
                    <p class="text-xs text-blue-200 mb-3">1 000 - 4 999 hab.</p>
                    <p class="text-3xl font-bold">79 €<span class="text-sm font-normal text-blue-200">/mois</span></p>
                    <p class="text-xs text-blue-100 mt-2">Email + téléphone (24h)</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <h3 class="text-lg font-bold text-gray-300">Commune moyenne</h3>
                    <p class="text-xs text-gray-500 mb-3">5 000 - 19 999 hab.</p>
                    <p class="text-3xl font-bold text-white">149 €<span class="text-sm font-normal text-gray-400">/mois</span></p>
                    <p class="text-xs text-gray-400 mt-2">Support prioritaire (J)</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <h3 class="text-lg font-bold text-gray-300">Grande commune</h3>
                    <p class="text-xs text-gray-500 mb-3">20 000 - 99 999 hab.</p>
                    <p class="text-3xl font-bold text-white">299 €<span class="text-sm font-normal text-gray-400">/mois</span></p>
                    <p class="text-xs text-gray-400 mt-2">Support dédié 7j/7</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 sm:col-span-2">
                    <h3 class="text-lg font-bold text-gray-300">Agglomération</h3>
                    <p class="text-xs text-gray-500 mb-3">100 000+ habitants</p>
                    <p class="text-3xl font-bold text-white">Sur devis</p>
                    <p class="text-xs text-gray-400 mt-2">Support sur mesure · SLA personnalisé</p>
                </div>
            </div>
            <div class="bg-gray-800 rounded-xl p-4 border border-gray-700 text-center">
                <p class="text-sm text-gray-400"><i class="fas fa-check-circle text-green-500 mr-1"></i> Tous les modules inclus · <i class="fas fa-check-circle text-green-500 mr-1"></i> Enfants illimités · <i class="fas fa-check-circle text-green-500 mr-1"></i> Utilisateurs illimités · <i class="fas fa-check-circle text-green-500 mr-1"></i> Hébergement France</p>
            </div>
        </div>
    </div>

    <!-- SLIDE 13 : Contact / CTA -->
    <div class="slide" :class="{ 'active': current === 13 }">
        <div class="max-w-3xl text-center text-white">
            <div class="mb-8 inline-flex items-center justify-center w-24 h-24 bg-blue-600 rounded-2xl shadow-2xl">
                <i class="fas fa-rocket text-5xl text-white"></i>
            </div>
            <h1 class="text-4xl sm:text-6xl font-bold mb-4">Prêt à digitaliser votre commune ?</h1>
            <p class="text-xl text-gray-400 mb-10">Démarrez en 15 minutes. L'assistant d'onboarding vous guide.</p>
            <div class="grid sm:grid-cols-3 gap-4 mb-10">
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <i class="fas fa-rocket text-blue-400 text-3xl mb-2"></i>
                    <h3 class="font-semibold">Démarrez</h3>
                    <p class="text-xs text-gray-400 mt-1">14 jours d'essai gratuit, sans carte bancaire</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <i class="fas fa-phone text-green-400 text-3xl mb-2"></i>
                    <h3 class="font-semibold">Parlons-en</h3>
                    <p class="text-xs text-gray-400 mt-1">Démonstration personnalisée à distance</p>
                </div>
                <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                    <i class="fas fa-envelope text-orange-400 text-3xl mb-2"></i>
                    <h3 class="font-semibold">Contact</h3>
                    <p class="text-xs text-gray-400 mt-1">contact@dailydesk.fr</p>
                </div>
            </div>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="/accueil" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 rounded-xl font-semibold text-lg transition-colors">
                    <i class="fas fa-arrow-right mr-2"></i> Voir la landing page
                </a>
                <a href="/accueil/register" class="px-8 py-4 bg-gray-700 hover:bg-gray-600 rounded-xl font-semibold text-lg transition-colors">
                    <i class="fas fa-user-plus mr-2"></i> Demander une démo
                </a>
            </div>
            <p class="text-sm text-gray-600 mt-10">DailyDesk — La suite administrative de votre commune</p>
        </div>
    </div>

</div>

<!-- Navigation -->
<div class="fixed bottom-0 left-0 right-0 bg-gray-800/90 backdrop-blur border-t border-gray-700 px-4 py-3 flex items-center justify-between z-50" x-cloak>
    <button @click="if (current > 0) current--" :disabled="current === 0"
            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 disabled:opacity-30 disabled:cursor-not-allowed rounded-lg text-white text-sm font-medium transition-colors">
        <i class="fas fa-chevron-left mr-1"></i> Précédent
    </button>

    <div class="flex items-center gap-2">
        <template x-for="i in total" :key="i">
            <button @click="current = i - 1"
                    :class="current === i - 1 ? 'bg-blue-600 w-8' : 'bg-gray-600 hover:bg-gray-500 w-2'"
                    class="h-2 rounded-full transition-all"
                    :aria-label="'Slide ' + i"></button>
        </template>
        <span class="text-gray-400 text-sm ml-3" x-text="(current + 1) + ' / ' + total"></span>
    </div>

    <button @click="if (current < total - 1) current++" :disabled="current === total - 1"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-30 disabled:cursor-not-allowed rounded-lg text-white text-sm font-medium transition-colors">
        Suivant <i class="fas fa-chevron-right ml-1"></i>
    </button>
</div>

</body>
</html>
