@extends('layouts.app')

@section('title', 'Gestion des Plans')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour au dashboard
            </a>
        </div>
        <a href="{{ route('central.plans.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Nouveau Plan
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-500 to-purple-600">
            <h1 class="text-2xl font-bold text-white">
                <i class="fas fa-tags mr-2"></i>
                Plans d'Abonnement
            </h1>
            <p class="text-purple-100 mt-1">Gérez les offres disponibles pour les tenants</p>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($plans as $plan)
                <div class="border-2 {{ $plan->is_active ? 'border-purple-300' : 'border-gray-300' }} rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- En-tête du plan -->
                    <div class="bg-gradient-to-br {{ $plan->slug === 'starter' ? 'from-gray-400 to-gray-500' : '' }} {{ $plan->slug === 'pro' ? 'from-blue-500 to-blue-600' : '' }} {{ $plan->slug === 'premium' ? 'from-purple-500 to-purple-600' : '' }} {{ !in_array($plan->slug, ['starter', 'pro', 'premium']) ? 'from-green-500 to-green-600' : '' }} p-6 text-white">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-2xl font-bold">{{ $plan->name }}</h3>
                            <span class="px-2 py-1 text-xs font-semibold rounded {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $plan->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                        @if($plan->description)
                        <p class="text-sm opacity-90">{{ $plan->description }}</p>
                        @endif
                    </div>

                    <!-- Prix -->
                    <div class="p-6 bg-gray-50">
                        <div class="text-center mb-4">
                            <span class="text-4xl font-bold text-gray-900">{{ number_format($plan->price_monthly, 0, ',', ' ') }}€</span>
                            <span class="text-gray-500">/mois</span>
                        </div>
                        <div class="text-center text-sm text-gray-600 mb-4">
                            <i class="fas fa-users mr-1"></i>
                            Jusqu'à <strong>{{ number_format($plan->max_children, 0, ',', ' ') }}</strong> enfants
                        </div>
                    </div>

                    <!-- Modules -->
                    <div class="px-6 py-4 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Modules inclus :</h4>
                        <ul class="space-y-2">
                            @foreach($plan->modules as $module)
                            <li class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                {{ ucfirst($module) }}
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Features -->
                    @if($plan->features && count($plan->features) > 0)
                    <div class="px-6 py-4 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Fonctionnalités :</h4>
                        <ul class="space-y-2">
                            @foreach($plan->features as $feature)
                            <li class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-star text-yellow-500 mr-2"></i>
                                {{ $feature }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Statistiques -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        @php
                            $tenantsCount = \App\Models\Tenant::where('subscription_plan', $plan->slug)->count();
                        @endphp
                        <div class="text-sm text-gray-600 text-center">
                            <i class="fas fa-building mr-1"></i>
                            <strong>{{ $tenantsCount }}</strong> tenant{{ $tenantsCount > 1 ? 's' : '' }} {{ $tenantsCount > 1 ? 'utilisent' : 'utilise' }} ce plan
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="px-6 py-4 bg-white border-t border-gray-200 flex gap-2">
                        <a href="{{ route('central.plans.edit', $plan) }}" 
                           class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-center text-sm font-medium transition-colors">
                            <i class="fas fa-edit mr-1"></i>
                            Modifier
                        </a>
                        
                        <form action="{{ route('central.plans.toggle-status', $plan) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" 
                                    class="w-full {{ $plan->is_active ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700' }} text-white px-3 py-2 rounded text-sm font-medium transition-colors">
                                <i class="fas fa-{{ $plan->is_active ? 'pause' : 'play' }} mr-1"></i>
                                {{ $plan->is_active ? 'Désactiver' : 'Activer' }}
                            </button>
                        </form>

                        @if($tenantsCount === 0)
                        <button type="button" 
                                onclick="confirmDelete('{{ $plan->id }}', '{{ $plan->name }}')"
                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded text-sm font-medium transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                        
                        <form id="delete-form-{{ $plan->id }}" 
                              action="{{ route('central.plans.destroy', $plan) }}" 
                              method="POST" 
                              class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="col-span-3 text-center py-12">
                    <i class="fas fa-tags text-gray-400 text-5xl mb-4"></i>
                    <p class="text-gray-500 text-lg">Aucun plan d'abonnement</p>
                    <a href="{{ route('central.plans.create') }}" class="mt-4 inline-block bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Créer le premier plan
                    </a>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            <div class="mt-4 text-center">
                <h3 class="text-lg leading-6 font-bold text-gray-900">Supprimer le Plan</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-600">
                        Êtes-vous sûr de vouloir supprimer le plan <strong id="planNameToDelete"></strong> ?
                    </p>
                    <p class="text-sm text-red-600 font-semibold mt-2">
                        ⚠️ Cette action est irréversible !
                    </p>
                </div>
                <div class="flex gap-4 px-4 py-3">
                    <button onclick="closeDeleteModal()" 
                            class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-lg hover:bg-gray-300 transition-colors">
                        Annuler
                    </button>
                    <button onclick="submitDelete()" 
                            class="flex-1 px-4 py-2 bg-red-600 text-white text-base font-medium rounded-lg hover:bg-red-700 transition-colors">
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentDeleteFormId = null;

function confirmDelete(planId, planName) {
    currentDeleteFormId = 'delete-form-' + planId;
    document.getElementById('planNameToDelete').textContent = planName;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    currentDeleteFormId = null;
}

function submitDelete() {
    if (currentDeleteFormId) {
        document.getElementById(currentDeleteFormId).submit();
    }
}

document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});
</script>
@endsection
