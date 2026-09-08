@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">
            Bonjour, {{ Auth::user()->name }} 👋
        </h1>
        <p class="mt-1 text-sm text-gray-600">
            Rôle : <span class="font-medium">{{ Auth::user()->roles->pluck('name')->join(', ') }}</span>
        </p>
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

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        @can('view_garderie')
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
        @endcan

        @can('view_cantine')
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
        @endcan

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
