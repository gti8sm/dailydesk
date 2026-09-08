@extends('layouts.app')

@section('title', 'Cantine - Présences')

@section('content')
<div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">

    <!-- Header -->
    <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                <i class="fas fa-utensils text-green-600 mr-2"></i>
                Cantine
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-gray-600">
                Les enfants inscrits sont pré-cochés présents — cliquez pour marquer absent
            </p>
        </div>
        <div class="flex gap-2 w-full sm:w-auto flex-wrap">
            <a href="{{ route('cantine.events.create') }}"
               class="px-4 py-3 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 font-medium transition-colors flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span class="hidden sm:inline">Signaler</span>
            </a>
            <a href="{{ route('cantine.events.index') }}"
               class="px-4 py-3 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 font-medium transition-colors flex items-center">
                <i class="fas fa-list mr-2"></i>
                <span class="hidden sm:inline">Événements</span>
            </a>
            <a href="{{ route('cantine.reports.monthly') }}"
               class="px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition-colors flex items-center">
                <i class="fas fa-chart-bar mr-2"></i>
                <span class="hidden sm:inline">Rapport</span>
            </a>
            <select id="meal-type-selector" class="text-lg px-4 py-3 rounded-lg border-2 border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                <option value="lunch" {{ $mealType === 'lunch' ? 'selected' : '' }}>Déjeuner</option>
                @if(\App\Models\Setting::get('cantine_enable_snack', false))
                <option value="snack" {{ $mealType === 'snack' ? 'selected' : '' }}>Goûter</option>
                @endif
            </select>
            <input type="date" id="date-selector" value="{{ $date }}"
                   class="text-lg px-4 py-3 rounded-lg border-2 border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
        </div>
    </div>

    <!-- Stats rapides -->
    <div class="mb-4 grid grid-cols-3 gap-3">
        <div class="bg-white shadow rounded-lg p-3 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $presences->where('is_present', true)->count() }}</p>
            <p class="text-xs text-gray-500">Présents</p>
        </div>
        <div class="bg-white shadow rounded-lg p-3 text-center">
            <p class="text-2xl font-bold text-red-500">{{ $groupedChildren->flatten()->count() - $presences->where('is_present', true)->count() }}</p>
            <p class="text-xs text-gray-500">Absents</p>
        </div>
        <div class="bg-white shadow rounded-lg p-3 text-center">
            <p class="text-2xl font-bold text-gray-700">{{ $groupedChildren->flatten()->count() }}</p>
            <p class="text-xs text-gray-500">Inscrits</p>
        </div>
    </div>

    <!-- Liste par classe -->
    <div class="space-y-4">
        @foreach($groupedChildren as $className => $classChildren)
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <div class="px-5 py-3 bg-gradient-to-r from-green-600 to-green-500 text-white flex items-center justify-between">
                <h3 class="text-lg font-bold">
                    <i class="fas fa-school mr-2"></i>{{ $className }}
                </h3>
                <span class="text-sm bg-white/20 px-3 py-1 rounded-full">
                    {{ $classChildren->count() }} enfant{{ $classChildren->count() > 1 ? 's' : '' }}
                </span>
            </div>
            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                @foreach($classChildren as $child)
                @php
                    $presence = $presenceByChildId->get($child->id);
                    $isPresent = $presence && $presence->is_present;
                @endphp
                <div class="rounded-xl border-2 transition-all {{ $isPresent ? 'border-green-300 bg-green-50' : 'border-red-200 bg-red-50' }}">
                    <div class="p-3 flex items-center justify-between">
                        <div class="flex items-center gap-2 min-w-0 flex-1">
                            <div class="rounded-full w-9 h-9 flex items-center justify-center font-bold text-sm flex-shrink-0 {{ $isPresent ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                <span>{{ strtoupper(substr($child->first_name, 0, 1)) }}</span>
                            </div>
                            <div class="min-w-0">
                                <div class="font-medium text-sm text-gray-900 truncate">{{ $child->full_name }}</div>
                                @if($child->allergies)
                                    <div class="text-xs text-red-600 font-medium"><i class="fas fa-exclamation-triangle mr-1"></i>Allergies</div>
                                @endif
                            </div>
                        </div>
                        @can('record_cantine_presence')
                        @if($isPresent)
                            <button onclick="toggleCantinePresence({{ $presence->id }}, false)"
                                    class="flex-shrink-0 ml-2 px-3 py-2 bg-green-600 text-white rounded-lg text-xs font-medium hover:bg-green-700 transition-colors">
                                <i class="fas fa-check mr-1"></i>Présent
                            </button>
                        @else
                            <button onclick="toggleCantinePresence({{ $presence->id }}, true)"
                                    class="flex-shrink-0 ml-2 px-3 py-2 bg-red-500 text-white rounded-lg text-xs font-medium hover:bg-red-600 transition-colors">
                                <i class="fas fa-times mr-1"></i>Absent
                            </button>
                        @endif
                        @endcan
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        @if($groupedChildren->isEmpty())
        <div class="bg-white shadow-lg rounded-xl p-12 text-center text-gray-500">
            <i class="fas fa-utensils text-4xl text-gray-300 mb-2"></i>
            <p>Aucun enfant inscrit à la cantine</p>
            <p class="text-sm mt-1">Cochez "Inscrit à la Cantine" dans la fiche des enfants</p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.getElementById('date-selector').addEventListener('change', updateUrl);
document.getElementById('meal-type-selector').addEventListener('change', updateUrl);

function updateUrl() {
    const date = document.getElementById('date-selector').value;
    const mealType = document.getElementById('meal-type-selector').value;
    window.location.href = `?date=${date}&meal_type=${mealType}`;
}

async function toggleCantinePresence(presenceId, makePresent) {
    try {
        await window.axios.patch(`/cantine/presences/${presenceId}`, {
            is_present: makePresent
        });
        window.location.reload();
    } catch (error) {
        alert('Erreur lors de la modification');
    }
}
</script>
@endpush
@endsection
