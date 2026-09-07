@extends('layouts.app')

@section('title', 'Rapport Mensuel Cantine')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-chart-bar text-green-600 mr-3"></i>
                Rapport Mensuel Cantine
            </h1>
            <p class="mt-2 text-gray-600">
                {{ \Carbon\Carbon::create($year, $month)->locale('fr')->isoFormat('MMMM YYYY') }}
            </p>
        </div>
        <a href="{{ route('cantine.export.monthly', ['year' => $year, 'month' => $month]) }}" 
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
            <i class="fas fa-file-csv mr-2"></i>
            Exporter CSV
        </a>
    </div>

    <!-- Sélecteur de mois -->
    <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
        <form method="GET" action="{{ route('cantine.reports.monthly') }}" class="flex items-end gap-4">
            <div class="flex-1">
                <label for="month" class="block text-sm font-medium text-gray-700 mb-2">
                    Mois
                </label>
                <select name="month" id="month" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
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
                <select name="year" id="year" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    @foreach(range(now()->year - 2, now()->year + 1) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-search mr-2"></i>
                Afficher
            </button>
        </form>
    </div>

    <!-- Statistiques globales -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Enfants inscrits</p>
                    <p class="text-3xl font-bold mt-2">{{ $report->unique('child_id')->count() }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total repas</p>
                    <p class="text-3xl font-bold mt-2">{{ $report->sum('meal_count') }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-utensils text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Moyenne/enfant</p>
                    <p class="text-3xl font-bold mt-2">
                        {{ $report->unique('child_id')->count() > 0 ? number_format($report->sum('meal_count') / $report->unique('child_id')->count(), 1) : 0 }}
                    </p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau détaillé -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-list text-green-600 mr-2"></i>
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
                            Type de repas
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nombre de repas
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $groupedReport = $report->groupBy('child_id');
                    @endphp
                    @forelse($groupedReport as $childId => $meals)
                    @php
                        $child = $meals->first()->child;
                        $lunchCount = $meals->where('meal_type', 'lunch')->sum('meal_count');
                        $snackCount = $meals->where('meal_type', 'snack')->sum('meal_count');
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap" rowspan="2">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-child text-green-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $child->full_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $child->age }} ans
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" rowspan="2">
                            <div class="text-sm text-gray-900">{{ $child->family->family_name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                <i class="fas fa-sun mr-1"></i> Déjeuner
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $lunchCount }} repas
                            </div>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition-colors border-t border-gray-100">
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                <i class="fas fa-cookie-bite mr-1"></i> Goûter
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $snackCount }} repas
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
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
    <div class="mt-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-green-600 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-green-900">
                    Données pour la facturation
                </h3>
                <div class="mt-2 text-sm text-green-800">
                    <p>Ce rapport contient les données nécessaires pour la facturation :</p>
                    <ul class="list-disc list-inside mt-2">
                        <li><strong>Déjeuners</strong> : Nombre de repas du midi pris dans le mois</li>
                        <li><strong>Goûters</strong> : Nombre de goûters pris dans le mois</li>
                    </ul>
                    <p class="mt-2">Exportez ce rapport en CSV (compatible Excel) pour l'importer dans votre logiciel de facturation.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
