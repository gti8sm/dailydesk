@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Bonjour, {{ Auth::user()->name }} 👋
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    Rôle : <span class="font-medium">{{ Auth::user()->roles->pluck('name')->join(', ') }}</span>
                    · {{ now()->translatedFormat('l d F Y') }}
                </p>
            </div>
            @if($tenant && auth()->user()->can('manage_public_site'))
            <a href="{{ route('public.site', ['tenant' => $tenant->slug]) }}" target="_blank"
               class="hidden sm:inline-flex items-center px-4 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded-lg font-medium text-sm transition-colors">
                <i class="fas fa-globe mr-2"></i>Voir le site public
            </a>
            @endif
        </div>
    </div>

    @if(auth()->user()->hasRole(['admin', 'admin_mairie']) && $tenant)
    <!-- Carte Abonnement -->
    <div class="mb-6 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-5 text-white">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-4">
                    <div class="bg-white bg-opacity-20 rounded-full p-3">
                        <i class="fas fa-credit-card text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">
                            Abonnement {{ $plan ? $plan->name : ucfirst($tenant->subscription_plan ?? '-') }}
                        </h2>
                        <p class="text-sm text-indigo-100 mt-1">
                            @if($plan)
                                {{ number_format($plan->price_monthly, 2) }} €/mois
                                · Limite : {{ $tenant->max_children ?? '∞' }} enfants
                            @else
                                Plan : {{ ucfirst($tenant->subscription_plan ?? '-') }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex gap-6 text-sm">
                    @if($tenant->subscription_starts_at)
                    <div class="text-center">
                        <p class="text-indigo-200 text-xs uppercase tracking-wide">Début</p>
                        <p class="font-semibold mt-1">{{ $tenant->subscription_starts_at->format('d/m/Y') }}</p>
                    </div>
                    @endif
                    @if($tenant->subscription_expires_at)
                    <div class="text-center">
                        <p class="text-indigo-200 text-xs uppercase tracking-wide">Expiration</p>
                        <p class="font-semibold mt-1">{{ $tenant->subscription_expires_at->format('d/m/Y') }}</p>
                        @if($tenant->subscription_expires_at->isPast())
                            <span class="text-red-200 text-xs font-medium">Expiré</span>
                        @elseif($tenant->subscription_expires_at->diffInDays(now()) <= 30)
                            <span class="text-yellow-200 text-xs font-medium">{{ ceil($tenant->subscription_expires_at->diffInDays(now())) }}j restants</span>
                        @endif
                    </div>
                    @endif
                    @if($tenant->trial_ends_at && $tenant->trial_ends_at->isFuture())
                    <div class="text-center">
                        <p class="text-indigo-200 text-xs uppercase tracking-wide">Essai</p>
                        <p class="font-semibold mt-1">{{ $tenant->trial_ends_at->format('d/m/Y') }}</p>
                        <span class="text-yellow-200 text-xs font-medium">{{ ceil($tenant->trial_ends_at->diffInDays(now())) }}j restants</span>
                    </div>
                    @endif
                </div>
            </div>
            @if($tenant->status === 'suspended')
            <div class="mt-3 bg-red-500 bg-opacity-30 rounded-lg px-4 py-2 text-sm">
                <i class="fas fa-exclamation-triangle mr-2"></i> Compte suspendu — contactez votre administrateur
            </div>
            @endif
        </div>
    </div>
    @endif

    @if($pendingActions && $pendingActions->count() > 0)
    <!-- Section À traiter -->
    <div class="mb-6 bg-white shadow rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 bg-yellow-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                <i class="fas fa-tasks text-yellow-500 mr-2"></i>
                À traiter
                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-200 text-yellow-800">
                    {{ $pendingActions->count() }}
                </span>
            </h3>
        </div>
        <div class="px-4 py-4 sm:p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($pendingActions as $action)
                <a href="{{ $action['url'] }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:shadow-md transition-all hover:border-{{ $action['color'] }}-300">
                    <div class="w-10 h-10 rounded-full bg-{{ $action['color'] }}-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $action['icon'] }} text-{{ $action['color'] }}-600"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-700">{{ $action['label'] }}</p>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Actions rapides -->
    @if(auth()->user()->hasRole(['admin', 'admin_mairie']))
    <div class="mb-6">
        <div class="flex flex-wrap gap-2">
            @can('view_garderie')
            <a href="{{ route('garderie.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 font-medium text-sm transition-colors">
                <i class="fas fa-child mr-2"></i> Saisir présences garderie
            </a>
            @endcan
            @can('view_cantine')
            <a href="{{ route('cantine.index') }}" class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 font-medium text-sm transition-colors">
                <i class="fas fa-utensils mr-2"></i> Saisir présences cantine
            </a>
            @endcan
            @can('manage_cantine_menus')
            <a href="{{ route('cantine.menus.index') }}" class="inline-flex items-center px-4 py-2 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 font-medium text-sm transition-colors">
                <i class="fas fa-file-lines mr-2"></i> Créer un menu
            </a>
            @endcan
            @can('manage_families')
            <a href="{{ route('families.index') }}" class="inline-flex items-center px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 font-medium text-sm transition-colors">
                <i class="fas fa-users mr-2"></i> Gérer les familles
            </a>
            @endcan
            @can('manage_users')
            <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm transition-colors">
                <i class="fas fa-user-cog mr-2"></i> Utilisateurs
            </a>
            @endcan
            @can('view_stock')
            <a href="{{ route('stock.items.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 font-medium text-sm transition-colors">
                <i class="fas fa-boxes-stacked mr-2"></i> Stock
            </a>
            @endcan
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        @foreach($activeModules as $key => $module)
        @if($key === 'garderie' && auth()->user()->can('view_garderie'))
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-child text-3xl text-blue-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Présences Garderie
                            </dt>
                            <dd class="text-2xl font-semibold text-gray-900">
                                {{ $stats['garderie_today'] ?? 0 }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <a href="{{ route('garderie.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                    Voir détails <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        @elseif($key === 'cantine' && auth()->user()->can('view_cantine'))
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-utensils text-3xl text-green-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Repas Cantine
                            </dt>
                            <dd class="text-2xl font-semibold text-gray-900">
                                {{ $stats['cantine_today'] ?? 0 }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <a href="{{ route('cantine.index') }}" class="text-sm font-medium text-green-600 hover:text-green-500">
                    Voir détails <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        @elseif($key === 'stock' && auth()->user()->can('view_stock'))
        <div class="bg-white overflow-hidden shadow rounded-lg {{ ($stats['stock_alerts'] ?? 0) > 0 ? 'ring-2 ring-red-400' : '' }}">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-boxes-stacked text-3xl text-indigo-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Articles en stock
                            </dt>
                            <dd class="text-2xl font-semibold text-gray-900">
                                {{ $stats['stock_items'] ?? 0 }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3 flex items-center justify-between">
                <a href="{{ route('stock.items.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    Voir détails <i class="fas fa-arrow-right ml-1"></i>
                </a>
                @if(($stats['stock_alerts'] ?? 0) > 0)
                <a href="{{ route('stock.alerts.index') }}" class="text-sm font-medium text-red-600 hover:text-red-500">
                    <i class="fas fa-bell mr-1"></i>{{ $stats['stock_alerts'] }} alerte(s)
                </a>
                @endif
            </div>
        </div>
        @endif
        @endforeach

        @can('manage_families')
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-3xl text-purple-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Familles
                            </dt>
                            <dd class="text-2xl font-semibold text-gray-900">
                                {{ $stats['families_count'] ?? 0 }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <a href="{{ route('families.index') }}" class="text-sm font-medium text-purple-600 hover:text-purple-500">
                    Gérer <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        @endcan

        @can('manage_children')
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-baby text-3xl text-orange-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Enfants
                            </dt>
                            <dd class="text-2xl font-semibold text-gray-900">
                                {{ $stats['children_count'] ?? 0 }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <a href="{{ route('families.index') }}" class="text-sm font-medium text-orange-600 hover:text-orange-500">
                    Voir liste <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        @endcan
    </div>

    @if(!empty($weeklyStats) && auth()->user()->hasRole(['admin', 'admin_mairie']))
    <!-- Mini-graphique : présences 7 derniers jours -->
    <div class="mb-8 bg-white shadow rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                <i class="fas fa-chart-line text-blue-500 mr-2"></i>
                Présences des 7 derniers jours
            </h3>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-end justify-between gap-2 h-48">
                @foreach($weeklyStats as $day)
                <div class="flex-1 flex flex-col items-center gap-1">
                    <div class="w-full flex flex-col items-center justify-end h-40 gap-0.5">
                        <div class="w-full max-w-[2rem] bg-blue-500 rounded-t transition-all hover:bg-blue-600"
                             style="height: {{ $weeklyMax > 0 ? ($day['garderie'] / $weeklyMax * 100) : 0 }}%; min-height: {{ $day['garderie'] > 0 ? '4px' : '0' }};"
                             title="Garderie : {{ $day['garderie'] }}"></div>
                        <div class="w-full max-w-[2rem] bg-green-500 rounded-b transition-all hover:bg-green-600"
                             style="height: {{ $weeklyMax > 0 ? ($day['cantine'] / $weeklyMax * 100) : 0 }}%; min-height: {{ $day['cantine'] > 0 ? '4px' : '0' }};"
                             title="Cantine : {{ $day['cantine'] }}"></div>
                    </div>
                    <div class="text-center">
                        <p class="text-xs font-medium text-gray-700">{{ $day['total'] }}</p>
                        <p class="text-[10px] text-gray-400">{{ $day['day'] }}</p>
                        <p class="text-[10px] text-gray-400">{{ $day['day_num'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4 flex items-center justify-center gap-6 text-xs">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-blue-500 rounded"></div>
                    <span class="text-gray-600">Garderie</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-green-500 rounded"></div>
                    <span class="text-gray-600">Cantine</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($schoolStats && $schoolStats->count() > 0)
    <!-- Répartition par école -->
    <div class="mb-8 bg-white shadow rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                <i class="fas fa-school text-indigo-500 mr-2"></i>
                Répartition par école — Aujourd'hui
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">École</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Enfants</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Garderie</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Cantine</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($schoolStats as $school)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $school['name'] }}
                            @if(!empty($school['is_shared']))
                            <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800" title="{{ $school['intercommunality_name'] ?? '' }}">
                                <i class="fas fa-handshake mr-0.5"></i>Partagée
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $school['type'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-700">{{ $school['children_count'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $school['garderie_present'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $school['cantine_present'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td class="px-6 py-3 text-sm font-semibold text-gray-900" colspan="2">Total</td>
                        <td class="px-6 py-3 text-sm text-center font-semibold text-gray-900">{{ $schoolStats->sum('children_count') }}</td>
                        <td class="px-6 py-3 text-sm text-center font-semibold text-blue-700">{{ $schoolStats->sum('garderie_present') }}</td>
                        <td class="px-6 py-3 text-sm text-center font-semibold text-green-700">{{ $schoolStats->sum('cantine_present') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        @can('view_garderie_events')
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                    Événements Garderie Récents
                </h3>
                <a href="{{ route('garderie.events.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                    Voir tout <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="px-4 py-5 sm:p-6">
                @if(isset($recent_garderie_events) && $recent_garderie_events->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($recent_garderie_events as $event)
                    <li class="py-3">
                        <a href="{{ route('garderie.events.show', $event) }}" class="flex items-center space-x-4 hover:bg-blue-50 -mx-2 px-2 rounded-lg transition-colors">
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center justify-center h-10 w-10 rounded-full 
                                    @if($event->severity === 'high') bg-red-100 text-red-600
                                    @elseif($event->severity === 'medium') bg-yellow-100 text-yellow-600
                                    @else bg-blue-100 text-blue-600 @endif">
                                    <i class="fas fa-bell"></i>
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $event->child->full_name }}
                                </p>
                                <p class="text-sm text-gray-500 truncate">
                                    {{ $event->title }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $event->event_date->format('d/m/Y') }}
                                    @if($event->event_time) à {{ $event->event_time }} @endif
                                    @if(!$event->parents_notified)
                                        <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700">Non notifié</span>
                                    @endif
                                </p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300"></i>
                        </a>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-sm text-gray-500 text-center py-4">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    Aucun événement récent
                </p>
                @endif
            </div>
        </div>
        @endcan

        @can('view_cantine_events')
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                    Événements Cantine Récents
                </h3>
                <a href="{{ route('cantine.events.index') }}" class="text-sm font-medium text-green-600 hover:text-green-500">
                    Voir tout <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="px-4 py-5 sm:p-6">
                @if(isset($recent_cantine_events) && $recent_cantine_events->count() > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($recent_cantine_events as $event)
                    <li class="py-3">
                        <a href="{{ route('cantine.events.show', $event) }}" class="flex items-center space-x-4 hover:bg-green-50 -mx-2 px-2 rounded-lg transition-colors">
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-orange-100 text-orange-600">
                                    <i class="fas fa-bell"></i>
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $event->child->full_name }}
                                </p>
                                <p class="text-sm text-gray-500 truncate">
                                    {{ $event->title }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $event->event_date->format('d/m/Y') }}
                                    @if($event->event_time) à {{ $event->event_time }} @endif
                                    @if(!$event->parents_notified)
                                        <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700">Non notifié</span>
                                    @endif
                                </p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300"></i>
                        </a>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-sm text-gray-500 text-center py-4">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    Aucun événement récent
                </p>
                @endif
            </div>
        </div>
        @endcan
    </div>

    @hasrole('parent')
    <div class="mt-6">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    <i class="fas fa-baby mr-2"></i>
                    Mes Enfants
                </h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                @if(isset($my_children) && $my_children->count() > 0)
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($my_children as $child)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900">{{ $child->full_name }}</h4>
                        <p class="text-sm text-gray-500">{{ $child->class }}</p>
                        <div class="mt-3 space-y-1">
                            <p class="text-xs text-gray-600">
                                <i class="fas fa-birthday-cake mr-1"></i>
                                {{ $child->birth_date->format('d/m/Y') }} ({{ $child->age }} ans)
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-500 text-center py-4">
                    Aucun enfant enregistré
                </p>
                @endif
            </div>
        </div>
    </div>
    @endhasrole
</div>
@endsection
