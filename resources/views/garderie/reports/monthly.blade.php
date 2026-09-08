@extends('layouts.app')

@section('title', 'Rapport Mensuel Garderie')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-chart-bar text-blue-600 mr-3"></i>
                Rapport Mensuel Garderie
            </h1>
            <p class="mt-2 text-gray-600">
                {{ \Carbon\Carbon::create($year, $month)->locale('fr')->isoFormat('MMMM YYYY') }}
            </p>
        </div>
        <a href="{{ route('garderie.export.monthly', ['year' => $year, 'month' => $month]) }}" 
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
            <i class="fas fa-file-csv mr-2"></i>
            Exporter CSV
        </a>
    </div>

    <!-- Sélecteur de mois -->
    <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
        <form method="GET" action="{{ route('garderie.reports.monthly') }}" class="flex items-end gap-4">
            <div class="flex-1">
                <label for="month" class="block text-sm font-medium text-gray-700 mb-2">
                    Mois
                </label>
                <select name="month" id="month" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(null, $m)->locale('fr')->isoFormat('MMMM') }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                    Année
                </label>
                <select name="year" id="year" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach(range(now()->year - 2, now()->year + 1) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-search mr-2"></i>
                Afficher
            </button>
        </form>
    </div>

    <!-- Statistiques globales -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Enfants concernés</p>
                    <p class="text-3xl font-bold mt-2">{{ $report->count() }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Jours de présence</p>
                    <p class="text-3xl font-bold mt-2">{{ $report->sum('total_days') }}</p>
                    <p class="text-green-100 text-xs mt-1">dont {{ $report->sum('days_count') }} complets</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-calendar-check text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total temps garderie</p>
                    @php $totalMin = $report->sum('total_minutes'); @endphp
                    <p class="text-3xl font-bold mt-2">{{ floor($totalMin / 60) }}h{{ str_pad($totalMin % 60, 2, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau détaillé -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-list text-blue-600 mr-2"></i>
                Détail par enfant
            </h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Enfant
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Famille
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jours présence
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jours complets
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Temps garderie
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Moyenne / jour complet
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($report as $item)
                    @php
                        $incomplete = $item->total_days - $item->days_count;
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-child text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $item->child->full_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $item->child->age }} ans
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $item->child->family->family_name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $item->total_days }} j
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $incomplete > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                {{ $item->days_count }} j
                            </span>
                            @if($incomplete > 0)
                            <span class="block text-xs text-yellow-600 mt-1">{{ $incomplete }} incomplet(s)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm font-medium text-gray-900">
                                {{ floor($item->total_minutes / 60) }}h{{ str_pad($item->total_minutes % 60, 2, '0', STR_PAD_LEFT) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm text-gray-900">
                                @if($item->days_count > 0)
                                    {{ floor(($item->total_minutes / $item->days_count) / 60) }}h{{ str_pad(intval(($item->total_minutes / $item->days_count) % 60), 2, '0', STR_PAD_LEFT) }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-4 text-gray-300"></i>
                            <p>Aucune présence enregistrée pour ce mois.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Info facturation -->
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-600 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-900">
                    Données pour la facturation
                </h3>
                <div class="mt-2 text-sm text-blue-800">
                    <p>Ce rapport contient les données nécessaires pour la facturation :</p>
                    <ul class="list-disc list-inside mt-2">
                        <li><strong>Jours présence</strong> : Nombre total de jours où l'enfant est venu (arrivée ou départ enregistré)</li>
                        <li><strong>Jours complets</strong> : Jours où l'arrivée ET le départ ont été enregistrés (en jaune si incomplet)</li>
                        <li><strong>Temps garderie</strong> : Temps total réel en garderie (hors heures scolaires)</li>
                        <li><strong>Moyenne / jour complet</strong> : Temps moyen par jour complet (basé sur les jours avec arrivée + départ)</li>
                    </ul>
                    <p class="mt-2">Exportez ce rapport en CSV (compatible Excel) pour l'importer dans votre logiciel de facturation.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
