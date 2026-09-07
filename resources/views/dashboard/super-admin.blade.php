@extends('layouts.app')

@section('title', 'Dashboard Super Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">
            Bonjour, {{ Auth::user()->name }} 👋
        </h1>
        <p class="mt-1 text-sm text-gray-600">
            Rôle : <span class="font-medium text-purple-600">{{ Auth::user()->roles->pluck('name')->join(', ') }}</span>
        </p>
    </div>

    <!-- Statistiques globales - Ligne 1 -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 overflow-hidden shadow-lg rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-building text-3xl text-white opacity-80"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-blue-100 truncate">
                                Total Tenants
                            </dt>
                            <dd class="text-3xl font-bold text-white">
                                {{ $stats['total_tenants'] }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 overflow-hidden shadow-lg rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-3xl text-white opacity-80"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-green-100 truncate">
                                Tenants Actifs
                            </dt>
                            <dd class="text-3xl font-bold text-white">
                                {{ $stats['active_tenants'] }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 overflow-hidden shadow-lg rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-3xl text-white opacity-80"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-yellow-100 truncate">
                                En Période d'Essai
                            </dt>
                            <dd class="text-3xl font-bold text-white">
                                {{ $stats['trial_tenants'] }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 overflow-hidden shadow-lg rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-tags text-3xl text-white opacity-80"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-purple-100 truncate">
                                Plans Disponibles
                            </dt>
                            <dd class="text-3xl font-bold text-white">
                                {{ $stats['total_plans'] }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques globales - Ligne 2 -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 overflow-hidden shadow-lg rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-euro-sign text-3xl text-white opacity-80"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-emerald-100 truncate">
                                Revenus Mensuels
                            </dt>
                            <dd class="text-3xl font-bold text-white">
                                {{ number_format($stats['monthly_revenue'], 0, ',', ' ') }}€
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 overflow-hidden shadow-lg rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-plus-circle text-3xl text-white opacity-80"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-indigo-100 truncate">
                                Nouveaux ce Mois
                            </dt>
                            <dd class="text-3xl font-bold text-white">
                                {{ $stats['new_this_month'] }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 overflow-hidden shadow-lg rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-pause-circle text-3xl text-white opacity-80"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-orange-100 truncate">
                                Suspendus
                            </dt>
                            <dd class="text-3xl font-bold text-white">
                                {{ $stats['suspended_tenants'] }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-600 overflow-hidden shadow-lg rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-3xl text-white opacity-80"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-red-100 truncate">
                                Essais Expirés
                            </dt>
                            <dd class="text-3xl font-bold text-white">
                                {{ $stats['expired_trials'] }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et statistiques détaillées -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Croissance mensuelle -->
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                Croissance (6 derniers mois)
            </h3>
            <div class="space-y-3">
                @foreach($monthlyGrowth as $month => $count)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">{{ $month }}</span>
                    <div class="flex items-center">
                        <div class="w-48 bg-gray-200 rounded-full h-2 mr-3">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $count > 0 ? ($count / max($monthlyGrowth) * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 w-8 text-right">{{ $count }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Répartition par plan -->
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-pie text-purple-600 mr-2"></i>
                Répartition par Plan
            </h3>
            <div class="space-y-4">
                @foreach($planStats as $slug => $data)
                <div class="border-l-4 {{ $slug === 'starter' ? 'border-gray-400' : '' }} {{ $slug === 'pro' ? 'border-blue-500' : '' }} {{ $slug === 'premium' ? 'border-purple-500' : '' }} pl-4">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-medium text-gray-900">{{ $data['name'] }}</span>
                        <span class="text-sm font-semibold text-gray-700">{{ $data['count'] }} tenant{{ $data['count'] > 1 ? 's' : '' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="w-full bg-gray-200 rounded-full h-2 mr-3">
                            <div class="bg-{{ $slug === 'starter' ? 'gray' : ($slug === 'pro' ? 'blue' : 'purple') }}-500 h-2 rounded-full" style="width: {{ $stats['total_tenants'] > 0 ? ($data['count'] / $stats['total_tenants'] * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-sm text-gray-600 whitespace-nowrap">{{ number_format($data['revenue'], 0, ',', ' ') }}€/mois</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Liste des tenants -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">
                    <i class="fas fa-building mr-2 text-blue-600"></i>
                    Gestion des Tenants
                </h2>
                <a href="{{ route('central.tenants.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-block">
                    <i class="fas fa-plus mr-2"></i>
                    Nouveau Tenant
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tenant
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Domaine
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Plan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Statut
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Expiration
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tenants as $tenant)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-bold text-lg">
                                        {{ strtoupper(substr($tenant->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $tenant->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $tenant->email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($tenant->domains->count() > 0)
                                    <a href="http://{{ $tenant->domains->first()->domain }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        {{ $tenant->domains->first()->domain }}
                                        <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                                    </a>
                                @else
                                    <span class="text-gray-400">Aucun domaine</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $tenant->subscription_plan === 'starter' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $tenant->subscription_plan === 'pro' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $tenant->subscription_plan === 'premium' ? 'bg-purple-100 text-purple-800' : '' }}">
                                {{ ucfirst($tenant->subscription_plan) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $tenant->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $tenant->status === 'suspended' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $tenant->status === 'trial' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                {{ ucfirst($tenant->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($tenant->subscription_expires_at)
                                {{ $tenant->subscription_expires_at->format('d/m/Y') }}
                                @if($tenant->subscription_expires_at->isPast())
                                    <span class="text-red-600 font-medium">Expiré</span>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-3">
                                <!-- Voir -->
                                <a href="{{ route('central.tenants.show', $tenant) }}" class="text-blue-600 hover:text-blue-900" title="Voir les détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <!-- Modifier -->
                                <a href="{{ route('central.tenants.edit', $tenant) }}" class="text-green-600 hover:text-green-900" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <!-- Se connecter en tant que -->
                                <form action="{{ route('central.impersonate', $tenant) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="text-purple-600 hover:text-purple-900" 
                                            title="Se connecter en tant que ce tenant">
                                        <i class="fas fa-sign-in-alt"></i>
                                    </button>
                                </form>
                                
                                <!-- Activer/Suspendre -->
                                <form action="{{ route('central.tenants.toggle-status', $tenant) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="{{ $tenant->status === 'active' ? 'text-orange-600 hover:text-orange-900' : 'text-green-600 hover:text-green-900' }}" 
                                            title="{{ $tenant->status === 'active' ? 'Suspendre' : 'Activer' }}"
                                            onclick="return confirm('Voulez-vous vraiment {{ $tenant->status === 'active' ? 'suspendre' : 'activer' }} ce tenant ?')">
                                        <i class="fas fa-{{ $tenant->status === 'active' ? 'pause-circle' : 'play-circle' }}"></i>
                                    </button>
                                </form>
                                
                                <!-- Supprimer -->
                                <button type="button" 
                                        onclick="confirmDelete('{{ $tenant->id }}', '{{ $tenant->name }}')"
                                        class="text-red-600 hover:text-red-900" 
                                        title="Supprimer">
                                    <i class="fas fa-trash"></i>
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
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-gray-400">
                                <i class="fas fa-building text-4xl mb-3"></i>
                                <p class="text-lg font-medium">Aucun tenant pour le moment</p>
                                <p class="text-sm mt-1">Créez votre premier tenant pour commencer</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center mb-4">
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-plus text-blue-600 text-xl"></i>
                </div>
                <h3 class="ml-3 text-lg font-semibold text-gray-900">Créer un Tenant</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">
                Ajoutez une nouvelle mairie à la plateforme
            </p>
            <a href="{{ route('central.tenants.create') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-center">
                Créer
            </a>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center mb-4">
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-tags text-purple-600 text-xl"></i>
                </div>
                <h3 class="ml-3 text-lg font-semibold text-gray-900">Gérer les Plans</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">
                Modifier les plans d'abonnement disponibles
            </p>
            <a href="{{ route('central.plans.index') }}" class="block w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-center">
                Gérer
            </a>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center mb-4">
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-chart-line text-green-600 text-xl"></i>
                </div>
                <h3 class="ml-3 text-lg font-semibold text-gray-900">Statistiques</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">
                Voir les statistiques globales de la plateforme
            </p>
            <a href="{{ route('central.statistics') }}" class="block w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-center">
                Voir Détails
            </a>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center mb-4">
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-file-download text-purple-600 text-xl"></i>
                </div>
                <h3 class="ml-3 text-lg font-semibold text-gray-900">Exports</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">
                Exporter les données de présences par tenant
            </p>
            <a href="{{ route('central.exports') }}" class="block w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-center">
                Gérer les Exports
            </a>
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
