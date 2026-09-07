@extends('layouts.app')

@section('title', 'Cantine - Présences')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-utensils text-green-600 mr-2"></i>
                Cantine - Présences
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Enregistrement des repas
            </p>
        </div>
        <div class="flex gap-4">
            <select id="meal-type-selector" class="rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                <option value="lunch" {{ $mealType === 'lunch' ? 'selected' : '' }}>Déjeuner</option>
                <option value="snack" {{ $mealType === 'snack' ? 'selected' : '' }}>Goûter</option>
            </select>
            <input type="date" id="date-selector" value="{{ $date }}"
                   class="rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg p-6" x-data="childSearch()">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-search mr-2"></i>
                    Rechercher un enfant
                </h3>
                
                <div class="relative">
                    <input type="text" 
                           x-model="search"
                           @input.debounce.300ms="searchChildren()"
                           placeholder="Prénom ou nom..."
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-lg py-3">
                    
                    <div x-show="results.length > 0" 
                         class="absolute z-10 mt-2 w-full bg-white shadow-lg rounded-md border border-gray-200 max-h-96 overflow-y-auto">
                        <template x-for="child in results" :key="child.id">
                            <div @click="selectChild(child)"
                                 class="p-4 hover:bg-gray-50 cursor-pointer border-b border-gray-100">
                                <div class="font-medium text-gray-900" x-text="child.first_name + ' ' + child.last_name"></div>
                                <div class="text-sm text-gray-500" x-text="child.class"></div>
                                <div class="text-xs text-gray-400" x-text="child.family.family_name"></div>
                                <div x-show="child.allergies" class="mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Allergies
                                    </span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="selectedChild" class="mt-6 p-4 bg-green-50 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-3">Enfant sélectionné</h4>
                    <div class="space-y-2">
                        <p class="text-lg font-semibold" x-text="selectedChild?.first_name + ' ' + selectedChild?.last_name"></p>
                        <p class="text-sm text-gray-600" x-text="selectedChild?.class"></p>
                        <p class="text-xs text-gray-500" x-text="selectedChild?.family?.family_name"></p>
                        
                        <div x-show="selectedChild?.allergies" class="mt-2 p-2 bg-red-50 border border-red-200 rounded">
                            <p class="text-xs font-medium text-red-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Allergies
                            </p>
                            <p class="text-xs text-red-700 mt-1" x-text="selectedChild?.allergies"></p>
                        </div>
                        
                        <div x-show="selectedChild?.dietary_restrictions" class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded">
                            <p class="text-xs font-medium text-yellow-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                Restrictions alimentaires
                            </p>
                            <p class="text-xs text-yellow-700 mt-1" x-text="selectedChild?.dietary_restrictions"></p>
                        </div>
                    </div>

                    @can('record_cantine_presence')
                    <div class="mt-4">
                        <button @click="recordPresence()" 
                                class="w-full bg-green-600 text-white px-4 py-3 rounded-md hover:bg-green-700 font-medium">
                            <i class="fas fa-check mr-2"></i>
                            Enregistrer Présence
                        </button>
                    </div>
                    @endcan
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        Présences - {{ $mealType === 'lunch' ? 'Déjeuner' : 'Goûter' }}
                        <span class="ml-2 text-sm font-normal text-gray-500">({{ $presences->count() }} enfants)</span>
                    </h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Enfant
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Classe
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Enregistré par
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($presences as $presence)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $presence->child->full_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $presence->child->family->family_name }}
                                            </div>
                                        </div>
                                        @if($presence->child->allergies)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $presence->child->class }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($presence->is_present)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i>
                                        Présent
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Absent
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $presence->recordedBy?->name ?? '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                    <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                                    <p>Aucune présence enregistrée pour cette date</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function childSearch() {
    return {
        search: '',
        results: [],
        selectedChild: null,
        
        async searchChildren() {
            if (this.search.length < 2) {
                this.results = [];
                return;
            }
            
            const response = await window.axios.get(`/cantine/search?q=${this.search}`);
            this.results = response;
        },
        
        selectChild(child) {
            this.selectedChild = child;
            this.results = [];
            this.search = '';
        },
        
        async recordPresence() {
            if (!this.selectedChild) return;
            
            const date = document.getElementById('date-selector').value;
            const mealType = document.getElementById('meal-type-selector').value;
            
            try {
                const response = await window.axios.post(`/cantine/children/${this.selectedChild.id}/record`, {
                    date: date,
                    meal_type: mealType,
                    is_present: true
                });
                
                alert(response.message);
                window.location.reload();
            } catch (error) {
                alert('Erreur lors de l\'enregistrement');
            }
        }
    }
}

document.getElementById('date-selector').addEventListener('change', function() {
    updateUrl();
});

document.getElementById('meal-type-selector').addEventListener('change', function() {
    updateUrl();
});

function updateUrl() {
    const date = document.getElementById('date-selector').value;
    const mealType = document.getElementById('meal-type-selector').value;
    window.location.href = `?date=${date}&meal_type=${mealType}`;
}
</script>
@endpush
@endsection
