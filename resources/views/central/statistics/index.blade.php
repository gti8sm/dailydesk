@extends('layouts.app')

@section('title', 'Statistiques Détaillées')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour au dashboard
        </a>
    </div>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-chart-line text-green-600 mr-3"></i>
            Statistiques Détaillées
        </h1>
        <p class="mt-2 text-gray-600">Vue d'ensemble complète de votre plateforme multi-tenant</p>
    </div>

    <!-- KPIs Principaux -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white shadow-lg rounded-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Tenants</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_tenants'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-building text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Taux d'Activité</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $activeRate }}%</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-percentage text-green-600 text-2xl"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">{{ $stats['active_tenants'] }} actifs / {{ $stats['total_tenants'] }} total</p>
        </div>

        <div class="bg-white shadow-lg rounded-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Taux de Conversion</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $conversionRate }}%</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-chart-pie text-purple-600 text-2xl"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Essai → Payant</p>
        </div>

        <div class="bg-white shadow-lg rounded-lg p-6 border-l-4 border-emerald-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Revenus Mensuels</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($revenue['monthly'], 0, ',', ' ') }}€</p>
                </div>
                <div class="bg-emerald-100 rounded-full p-3">
                    <i class="fas fa-euro-sign text-emerald-600 text-2xl"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">{{ number_format($revenue['yearly'], 0, ',', ' ') }}€ /an</p>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Croissance sur 12 mois -->
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-chart-area text-blue-600 mr-2"></i>
                Croissance (12 derniers mois)
            </h3>
            <div class="space-y-3">
                @foreach($monthlyGrowth as $month => $count)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 w-20">{{ $month }}</span>
                    <div class="flex items-center flex-1 ml-4">
                        <div class="flex-1 bg-gray-200 rounded-full h-3 mr-3">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-500" 
                                 style="width: {{ $count > 0 ? ($count / max($monthlyGrowth) * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 w-8 text-right">{{ $count }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Statut des tenants -->
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-chart-donut text-purple-600 mr-2"></i>
                Répartition par Statut
            </h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                        <span class="font-medium text-gray-900">Actifs</span>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-bold text-gray-900">{{ $stats['active_tenants'] }}</span>
                        <span class="text-sm text-gray-500 ml-2">({{ $stats['total_tenants'] > 0 ? round(($stats['active_tenants'] / $stats['total_tenants']) * 100, 1) : 0 }}%)</span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                        <span class="font-medium text-gray-900">En Essai</span>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-bold text-gray-900">{{ $stats['trial_tenants'] }}</span>
                        <span class="text-sm text-gray-500 ml-2">({{ $stats['total_tenants'] > 0 ? round(($stats['trial_tenants'] / $stats['total_tenants']) * 100, 1) : 0 }}%)</span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-4 bg-orange-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-orange-500 rounded-full mr-3"></div>
                        <span class="font-medium text-gray-900">Suspendus</span>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-bold text-gray-900">{{ $stats['suspended_tenants'] }}</span>
                        <span class="text-sm text-gray-500 ml-2">({{ $stats['total_tenants'] > 0 ? round(($stats['suspended_tenants'] / $stats['total_tenants']) * 100, 1) : 0 }}%)</span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                        <span class="font-medium text-gray-900">Essais Expirés</span>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-bold text-gray-900">{{ $stats['expired_trials'] }}</span>
                        <span class="text-sm text-gray-500 ml-2">({{ $stats['total_tenants'] > 0 ? round(($stats['expired_trials'] / $stats['total_tenants']) * 100, 1) : 0 }}%)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques par Plan -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
        <div class="px-6 py-4 bg-gradient-to-r from-purple-500 to-purple-600 border-b border-purple-700">
            <h3 class="text-xl font-semibold text-white flex items-center">
                <i class="fas fa-tags mr-2"></i>
                Analyse par Plan d'Abonnement
            </h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b-2 border-gray-200">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actifs</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Part de Marché</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Revenus Mensuels</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Revenus Annuels</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($planStats as $plan)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-3 {{ $plan['slug'] === 'starter' ? 'bg-gray-400' : '' }} {{ $plan['slug'] === 'pro' ? 'bg-blue-500' : '' }} {{ $plan['slug'] === 'premium' ? 'bg-purple-500' : '' }}"></div>
                                    <span class="font-medium text-gray-900">{{ $plan['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-lg font-semibold text-gray-900">{{ $plan['count'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm text-gray-600">{{ $plan['active'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center">
                                    <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-{{ $plan['slug'] === 'starter' ? 'gray' : ($plan['slug'] === 'pro' ? 'blue' : 'purple') }}-500 h-2 rounded-full" 
                                             style="width: {{ $stats['total_tenants'] > 0 ? ($plan['count'] / $stats['total_tenants'] * 100) : 0 }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">{{ $stats['total_tenants'] > 0 ? round(($plan['count'] / $stats['total_tenants']) * 100, 1) : 0 }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-lg font-bold text-emerald-600">{{ number_format($plan['revenue_monthly'], 0, ',', ' ') }}€</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm text-gray-600">{{ number_format($plan['revenue_yearly'], 0, ',', ' ') }}€</span>
                            </td>
                        </tr>
                        @endforeach
                        <tr class="bg-gray-50 font-semibold">
                            <td class="px-6 py-4 whitespace-nowrap">TOTAL</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-lg">{{ $stats['total_tenants'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">{{ $stats['active_tenants'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">100%</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-lg text-emerald-600">{{ number_format($revenue['monthly'], 0, ',', ' ') }}€</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-gray-600">{{ number_format($revenue['yearly'], 0, ',', ' ') }}€</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
