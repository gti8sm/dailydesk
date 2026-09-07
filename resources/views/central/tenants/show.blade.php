@extends('layouts.app')

@section('title', 'Détails du Tenant')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour au dashboard
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- En-tête -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-500 to-blue-600">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white">
                        <i class="fas fa-building mr-2"></i>
                        {{ $tenant->name }}
                    </h1>
                    <p class="text-blue-100 mt-1">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $tenant->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $tenant->status === 'suspended' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $tenant->status === 'trial' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                            {{ ucfirst($tenant->status) }}
                        </span>
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('central.tenants.edit', $tenant) }}" 
                       class="bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-blue-50 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Modifier
                    </a>
                    <form action="{{ route('central.tenants.toggle-status', $tenant) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="bg-white {{ $tenant->status === 'active' ? 'text-orange-600 hover:bg-orange-50' : 'text-green-600 hover:bg-green-50' }} px-4 py-2 rounded-lg font-medium transition-colors"
                                onclick="return confirm('Voulez-vous vraiment {{ $tenant->status === 'active' ? 'suspendre' : 'activer' }} ce tenant ?')">
                            <i class="fas fa-{{ $tenant->status === 'active' ? 'pause-circle' : 'play-circle' }} mr-2"></i>
                            {{ $tenant->status === 'active' ? 'Suspendre' : 'Activer' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informations générales -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Informations Générales
                    </h2>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $tenant->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Slug</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $tenant->slug }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="mailto:{{ $tenant->email }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $tenant->email }}
                                </a>
                            </dd>
                        </div>
                        @if($tenant->phone)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $tenant->phone }}</dd>
                        </div>
                        @endif
                        @if($tenant->address)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Adresse</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $tenant->address }}<br>
                                {{ $tenant->postal_code }} {{ $tenant->city }}
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>

                <!-- Abonnement -->
                <div class="bg-purple-50 rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-tags text-purple-600 mr-2"></i>
                        Abonnement
                    </h2>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Plan</dt>
                            <dd class="mt-1">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                    {{ $tenant->subscription_plan === 'starter' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $tenant->subscription_plan === 'pro' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $tenant->subscription_plan === 'premium' ? 'bg-purple-100 text-purple-800' : '' }}">
                                    {{ ucfirst($tenant->subscription_plan) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Début</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $tenant->subscription_starts_at ? $tenant->subscription_starts_at->format('d/m/Y') : '-' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Expiration</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $tenant->subscription_expires_at ? $tenant->subscription_expires_at->format('d/m/Y') : '-' }}
                                @if($tenant->subscription_expires_at && $tenant->subscription_expires_at->isPast())
                                    <span class="ml-2 text-red-600 font-medium">Expiré</span>
                                @endif
                            </dd>
                        </div>
                        @if($tenant->trial_ends_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fin d'essai</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $tenant->trial_ends_at->format('d/m/Y') }}
                                @if($tenant->trial_ends_at->isFuture())
                                    @php
                                        $days = $tenant->trial_ends_at->diffInDays(now());
                                        $daysRemaining = (int) ceil($days);
                                    @endphp
                                    <span class="ml-2 text-yellow-600 font-medium">
                                        ({{ $daysRemaining }} jour{{ $daysRemaining != 1 ? 's' : '' }} restant{{ $daysRemaining != 1 ? 's' : '' }})
                                    </span>
                                @else
                                    <span class="ml-2 text-gray-500 font-medium">
                                        (Expiré)
                                    </span>
                                @endif
                            </dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Enfants max</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($tenant->max_children, 0, ',', ' ') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Domaines -->
                <div class="bg-green-50 rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-globe text-green-600 mr-2"></i>
                        Domaines
                    </h2>
                    @if($tenant->domains->count() > 0)
                    <ul class="space-y-2">
                        @foreach($tenant->domains as $domain)
                        <li class="flex items-center justify-between bg-white rounded-lg p-3">
                            <span class="text-sm font-mono text-gray-900">{{ $domain->domain }}</span>
                            <a href="http://{{ $domain->domain }}:8001" 
                               target="_blank" 
                               class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-external-link-alt mr-1"></i>
                                Ouvrir
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-sm text-gray-500">Aucun domaine configuré</p>
                    @endif
                </div>

                <!-- Modules activés -->
                <div class="bg-blue-50 rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-puzzle-piece text-blue-600 mr-2"></i>
                        Modules Activés
                    </h2>
                    @if(is_array($tenant->modules_enabled) && count($tenant->modules_enabled) > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($tenant->modules_enabled as $module)
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                            <i class="fas fa-check mr-1"></i>
                            {{ ucfirst($module) }}
                        </span>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-gray-500">Aucun module activé</p>
                    @endif
                </div>
            </div>

            <!-- Dates de création/modification -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">
                    <div>
                        <i class="fas fa-calendar-plus mr-2"></i>
                        Créé le {{ $tenant->created_at->format('d/m/Y à H:i') }}
                    </div>
                    <div>
                        <i class="fas fa-calendar-check mr-2"></i>
                        Modifié le {{ $tenant->updated_at->format('d/m/Y à H:i') }}
                    </div>
                </div>
            </div>

            <!-- Zone de danger -->
            <div class="mt-8 pt-6 border-t-2 border-red-200 bg-red-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-red-900 mb-2 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Zone de Danger
                </h3>
                <p class="text-sm text-red-700 mb-4">
                    La suppression d'un tenant est irréversible. Toutes les données seront définitivement perdues.
                </p>
                <button type="button" 
                        onclick="confirmDelete('{{ $tenant->id }}', '{{ $tenant->name }}')"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-trash mr-2"></i>
                    Supprimer ce Tenant
                </button>
                
                <!-- Formulaire de suppression caché -->
                <form id="delete-form-{{ $tenant->id }}" 
                      action="{{ route('central.tenants.destroy', $tenant) }}" 
                      method="POST" 
                      class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
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
                <h3 class="text-lg leading-6 font-bold text-gray-900">Supprimer le Tenant</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-600">
                        Êtes-vous sûr de vouloir supprimer le tenant <strong id="tenantNameToDelete"></strong> ?
                    </p>
                    <p class="text-sm text-red-600 font-semibold mt-2">
                        ⚠️ Cette action est irréversible !
                    </p>
                    <p class="text-xs text-gray-500 mt-2">
                        La base de données et toutes les données associées seront définitivement supprimées.
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

function confirmDelete(tenantId, tenantName) {
    currentDeleteFormId = 'delete-form-' + tenantId;
    document.getElementById('tenantNameToDelete').textContent = tenantName;
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

// Fermer le modal en cliquant en dehors
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Fermer avec la touche Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});
</script>
@endsection
