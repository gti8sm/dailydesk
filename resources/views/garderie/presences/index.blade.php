@extends('layouts.app')

@section('title', 'Garderie - Présences')

@section('content')
<div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8" x-data="alphabeticalSearch()" x-init="init()">

    <!-- Header -->
    <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                <i class="fas fa-child text-blue-600 mr-2"></i>
                Garderie
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-gray-600">
                Enregistrement des arrivées et départs
            </p>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            <a href="{{ route('garderie.events.create') }}"
               class="px-4 py-3 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 font-medium transition-colors flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span class="hidden sm:inline">Signaler</span>
                <i class="fas fa-exclamation-triangle sm:hidden"></i>
            </a>
            <a href="{{ route('garderie.events.index') }}"
               class="px-4 py-3 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 font-medium transition-colors flex items-center">
                <i class="fas fa-list mr-2"></i>
                <span class="hidden sm:inline">Événements</span>
                <i class="fas fa-list sm:hidden"></i>
            </a>
            <a href="{{ route('garderie.reports.monthly') }}"
               class="px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition-colors flex items-center">
                <i class="fas fa-chart-bar mr-2"></i>
                <span class="hidden sm:inline">Rapport</span>
                <i class="fas fa-chart-bar sm:hidden"></i>
            </a>
            <input type="date" id="date-selector" value="{{ $date }}"
                   class="flex-1 sm:flex-none text-lg px-4 py-3 rounded-lg border-2 border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>

    <!-- Panneau d'action : enfant sélectionné (en haut, bien visible) -->
    <div x-show="selectedChild" x-transition class="mb-4 bg-white shadow-xl rounded-2xl overflow-hidden border-2 border-blue-400">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-5 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 rounded-full w-12 h-12 flex items-center justify-center text-2xl">
                    <i class="fas fa-child text-white"></i>
                </div>
                <div>
                    <p class="text-white font-bold text-xl" x-text="selectedChild?.first_name + ' ' + selectedChild?.last_name"></p>
                    <p class="text-blue-100 text-sm">
                        <span x-text="selectedChild?.class"></span> ·
                        <span x-text="selectedChild?.family?.family_name"></span>
                    </p>
                </div>
            </div>
            <button @click="selectedChild = null" class="text-white/80 hover:text-white text-2xl px-2">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @can('record_garderie_presence')
        <div class="p-4 grid grid-cols-2 gap-3">
            <button x-show="isMorning" @click="recordArrival()"
                    class="bg-gradient-to-r from-green-600 to-green-500 text-white py-5 rounded-xl font-bold text-lg shadow-lg active:scale-95 transition-all touch-manipulation flex items-center justify-center">
                <i class="fas fa-sign-in-alt mr-3 text-2xl"></i>
                ARRIVÉE
            </button>
            <button x-show="!isMorning" @click="recordDeparture()"
                    class="bg-gradient-to-r from-orange-600 to-orange-500 text-white py-5 rounded-xl font-bold text-lg shadow-lg active:scale-95 transition-all touch-manipulation flex items-center justify-center">
                <i class="fas fa-sign-out-alt mr-3 text-2xl"></i>
                DÉPART
            </button>
            <button x-show="!isMorning" @click="recordArrival()"
                    class="col-span-2 bg-gray-200 text-gray-600 py-3 rounded-xl font-medium text-sm active:scale-95 transition-all touch-manipulation flex items-center justify-center">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Enregistrer une arrivée (exception)
            </button>
            <button x-show="isMorning" @click="recordDeparture()"
                    class="col-span-2 bg-gray-200 text-gray-600 py-3 rounded-xl font-medium text-sm active:scale-95 transition-all touch-manipulation flex items-center justify-center">
                <i class="fas fa-sign-out-alt mr-2"></i>
                Enregistrer un départ (exception)
            </button>
        </div>
        @endcan
        <div class="px-4 pb-4">
            <a :href="'{{ route('garderie.events.create') }}?child_id=' + (selectedChild?.id || '')"
               class="block w-full text-center py-3 bg-orange-50 text-orange-700 rounded-xl font-medium text-sm hover:bg-orange-100 transition-all border-2 border-orange-200">
                <i class="fas fa-exclamation-triangle mr-2"></i> Signaler un événement
            </a>
        </div>
    </div>

    <!-- Layout principal -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

        <!-- Colonne principale : liste enfants + clavier -->
        <div class="lg:col-span-3 flex flex-col gap-4">

            <!-- Liste des enfants (zone centrale) -->
            <div class="bg-white shadow-lg rounded-xl p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-search mr-2 text-blue-600"></i>
                        <span x-show="!searchString">Tous les enfants</span>
                        <span x-show="searchString" x-text="'Recherche : ' + searchString" class="text-blue-600"></span>
                    </h3>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500" x-text="filteredChildren.length + ' trouvé(s)'"></span>
                        <button x-show="searchString" @click="reset()" class="text-sm px-3 py-1.5 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                            <i class="fas fa-redo mr-1"></i>Effacer
                        </button>
                    </div>
                </div>

                <div x-show="!allChildren.length" class="text-center py-12">
                    <i class="fas fa-spinner fa-spin text-4xl text-blue-600"></i>
                    <p class="mt-2 text-gray-600">Chargement des enfants...</p>
                </div>

                <div x-show="allChildren.length" class="space-y-2 max-h-[50vh] lg:max-h-[55vh] overflow-y-auto pr-1">
                    <template x-for="child in filteredChildren" :key="child.id">
                        <button @click="selectChild(child)"
                                :class="selectedChild?.id === child.id ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-white hover:border-blue-300 hover:bg-blue-50/50'"
                                class="w-full p-3 sm:p-4 rounded-xl border-2 transition-all text-left shadow-sm active:scale-[0.98] touch-manipulation flex items-center justify-between">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="bg-blue-100 text-blue-700 rounded-full w-10 h-10 flex items-center justify-center font-bold text-lg flex-shrink-0">
                                    <span x-text="child.first_name.charAt(0).toUpperCase()"></span>
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-base sm:text-lg text-gray-900 truncate" x-text="child.first_name + ' ' + child.last_name"></div>
                                    <div class="text-xs sm:text-sm text-gray-500 truncate">
                                        <span x-text="child.class"></span> · <span x-text="child.family?.family_name"></span>
                                    </div>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-blue-500 text-lg flex-shrink-0 ml-2"></i>
                        </button>
                    </template>
                    <div x-show="allChildren.length > 0 && filteredChildren.length === 0" class="text-center py-8 text-gray-500">
                        <i class="fas fa-search text-3xl text-gray-300 mb-2"></i>
                        <p>Aucun enfant trouvé</p>
                    </div>
                </div>
            </div>

            <!-- Clavier alphabétique (en bas, accessible au pouce sur tablette/téléphone) -->
            <div class="bg-white shadow-lg rounded-xl p-3 sm:p-4 lg:sticky lg:bottom-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">Recherche par lettre</span>
                    <button x-show="searchString" @click="searchString = searchString.slice(0, -1); filterChildren()" class="text-sm px-2 py-1 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-backspace"></i>
                    </button>
                </div>
                <div class="grid gap-1 sm:gap-1.5" style="grid-template-columns: repeat(13, minmax(0, 1fr));">
                    <template x-for="letter in availableLetters" :key="letter">
                        <button
                            @click="addLetter(letter)"
                            :disabled="!isLetterAvailable(letter)"
                            :class="isLetterAvailable(letter) ? 'bg-blue-600 hover:bg-blue-700 text-white shadow-md' : 'bg-gray-100 text-gray-300 cursor-not-allowed'"
                            class="aspect-square rounded-lg text-base sm:text-lg font-bold transition-all active:scale-90 touch-manipulation flex items-center justify-center">
                            <span x-text="letter"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <!-- Colonne latérale : présences du jour -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg lg:sticky lg:top-4">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        Présences du jour
                        <span class="ml-2 text-sm font-normal text-gray-500">({{ $groupedChildren->flatten()->count() }})</span>
                    </h3>
                </div>

                <div class="max-h-[70vh] overflow-y-auto">
                    @php $allChildren = $groupedChildren->flatten(); @endphp
                    @forelse($allChildren as $child)
                    <div class="px-5 py-3 border-b border-gray-100 {{ $child->presence ? 'bg-green-50' : '' }}">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-gray-900 truncate">
                                    {{ $child->full_name }}
                                </div>
                                <div class="text-xs text-gray-500">{{ $child->schoolClass?->name ?? 'Sans classe' }}</div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                                @if($child->presence && $child->presence->arrival_time)
                                    <span class="text-xs text-green-700 bg-green-100 px-2 py-1 rounded-full whitespace-nowrap cursor-pointer hover:bg-green-200 transition-colors"
                                          onclick="editGarderieTime({{ $child->presence->id }}, 'arrival_time', '{{ \Carbon\Carbon::parse($child->presence->arrival_time)->format('H:i') }}')"
                                          title="Cliquer pour modifier l'heure">
                                        <i class="fas fa-sign-in-alt mr-1"></i>{{ \Carbon\Carbon::parse($child->presence->arrival_time)->format('H:i') }}
                                    </span>
                                @endif
                                @if($child->presence && $child->presence->departure_time)
                                    <span class="text-xs text-orange-700 bg-orange-100 px-2 py-1 rounded-full whitespace-nowrap cursor-pointer hover:bg-orange-200 transition-colors"
                                          onclick="editGarderieTime({{ $child->presence->id }}, 'departure_time', '{{ \Carbon\Carbon::parse($child->presence->departure_time)->format('H:i') }}')"
                                          title="Cliquer pour modifier l'heure">
                                        <i class="fas fa-sign-out-alt mr-1"></i>{{ \Carbon\Carbon::parse($child->presence->departure_time)->format('H:i') }}
                                    </span>
                                @elseif($child->presence && $child->presence->arrival_time)
                                    <span class="text-xs text-green-700 bg-green-200 px-2 py-1 rounded-full whitespace-nowrap">
                                        <i class="fas fa-check mr-1"></i>Présent
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                                @if($child->presence)
                                    <button onclick="deleteGarderiePresence({{ $child->presence->id }})"
                                            class="text-xs text-red-500 hover:text-red-700 hover:bg-red-100 px-2 py-1 rounded-full transition-colors"
                                            title="Annuler la présence">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        @if($child->presence && $child->presence->duration_minutes)
                        <div class="mt-1 text-xs text-blue-600 font-medium">
                            <i class="fas fa-clock mr-1"></i>
                            {{ floor($child->presence->duration_minutes / 60) }}h{{ str_pad($child->presence->duration_minutes % 60, 2, '0', STR_PAD_LEFT) }}
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-500">
                        <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                        <p>Aucun enfant inscrit à la garderie</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function alphabeticalSearch() {
    return {
        searchString: '',
        allChildren: [],
        filteredChildren: [],
        selectedChild: null,
        availableLetters: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split(''),
        get isMorning() {
            return new Date().getHours() < 13;
        },
        
        async init() {
            // Attendre que axios soit disponible
            await new Promise(resolve => setTimeout(resolve, 100));
            
            try {
                if (!window.axios) {
                    console.error('Axios non disponible');
                    this.allChildren = [];
                    this.filteredChildren = [];
                    return;
                }
                
                const response = await window.axios.get('/garderie/search?q=');
                const children = response.data || [];
                this.allChildren = Array.isArray(children) ? children : [];
                this.filteredChildren = Array.isArray(children) ? children : [];
                console.log('Enfants chargés:', this.allChildren.length);
            } catch (error) {
                console.error('Erreur chargement enfants:', error);
                this.allChildren = [];
                this.filteredChildren = [];
            }
        },
        
        isLetterAvailable(letter) {
            if (this.searchString.length === 0) {
                return this.allChildren.some(child => 
                    child.first_name.toUpperCase().startsWith(letter)
                );
            }
            
            return this.filteredChildren.some(child => 
                child.first_name.toUpperCase().charAt(this.searchString.length) === letter
            );
        },
        
        addLetter(letter) {
            if (!this.isLetterAvailable(letter)) return;
            
            this.searchString += letter;
            this.filterChildren();
        },
        
        filterChildren() {
            this.filteredChildren = this.allChildren.filter(child =>
                child.first_name.toUpperCase().startsWith(this.searchString)
            );
            
            if (this.filteredChildren.length === 1) {
                this.selectChild(this.filteredChildren[0]);
            }
        },
        
        reset() {
            this.searchString = '';
            this.filteredChildren = this.allChildren;
            this.selectedChild = null;
        },
        
        selectChild(child) {
            this.selectedChild = child;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        
        async recordArrival() {
            if (!this.selectedChild) {
                console.error('Aucun enfant sélectionné');
                return;
            }
            
            const date = document.getElementById('date-selector').value;
            const now = new Date();
            const time = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
            
            console.log('Enregistrement arrivée:', this.selectedChild.first_name, time);
            
            try {
                const response = await window.axios.post(`/garderie/children/${this.selectedChild.id}/arrival`, {
                    date: date,
                    arrival_time: time
                });
                
                console.log('Réponse:', response);
                alert(response.data.message || 'Arrivée enregistrée');
                window.location.reload();
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors de l\'enregistrement: ' + (error.message || 'Erreur inconnue'));
            }
        },
        
        async recordDeparture() {
            if (!this.selectedChild) return;
            
            const date = document.getElementById('date-selector').value;
            const now = new Date();
            const time = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
            
            try {
                const response = await window.axios.post(`/garderie/children/${this.selectedChild.id}/departure`, {
                    date: date,
                    departure_time: time
                });
                
                alert(response.data.message || 'Départ enregistré');
                window.location.reload();
            } catch (error) {
                alert('Erreur lors de l\'enregistrement');
            }
        }
    }
}

document.getElementById('date-selector').addEventListener('change', function() {
    window.location.href = `?date=${this.value}`;
});

async function deleteGarderiePresence(presenceId) {
    if (!confirm('Annuler cette présence ?')) return;
    try {
        await window.axios.delete(`/garderie/presences/${presenceId}`);
        window.location.reload();
    } catch (error) {
        alert('Erreur lors de la suppression');
    }
}

async function editGarderieTime(presenceId, field, currentTime) {
    const label = field === 'arrival_time' ? 'heure d\'arrivée' : 'heure de départ';
    const newTime = prompt(`Nouvelle ${label} (format HH:MM) :`, currentTime);
    if (newTime === null) return;
    if (!/^\d{2}:\d{2}$/.test(newTime)) {
        alert('Format invalide. Utilisez HH:MM (ex: 08:30)');
        return;
    }
    try {
        await window.axios.patch(`/garderie/presences/${presenceId}`, {
            [field]: newTime
        });
        window.location.reload();
    } catch (error) {
        alert('Erreur lors de la modification');
    }
}
</script>
@endpush
@endsection
