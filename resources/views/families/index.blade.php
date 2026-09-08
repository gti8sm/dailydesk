@extends('layouts.app')

@section('title', 'Gestion des Familles')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-users text-purple-600 mr-2"></i>
                Gestion des Familles
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Liste des familles inscrites
            </p>
        </div>
        <div>
            <a href="{{ route('families.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                <i class="fas fa-plus mr-2"></i>
                Nouvelle Famille
            </a>
        </div>
    </div>

    @php
        $families = \App\Models\Family::with('children')->where('is_active', true)->get();
    @endphp

    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                    <i class="fas fa-users text-2xl text-purple-600"></i>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500">Total Familles</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $families->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <i class="fas fa-child text-2xl text-blue-600"></i>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500">Total Enfants</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $families->sum(fn($f) => $f->children->count()) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <i class="fas fa-chart-line text-2xl text-green-600"></i>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500">Moyenne Enfants/Famille</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ $families->count() > 0 ? number_format($families->sum(fn($f) => $f->children->count()) / $families->count(), 1) : 0 }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">
                    Liste des Familles
                </h3>
                <div class="flex gap-2">
                    <input type="text" id="family-search" placeholder="Rechercher..." 
                           class="rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                           onkeyup="filterFamilies()">
                    <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-8"></th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Famille
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Contact
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ville
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Enfants
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($families as $family)
                    <tr x-data="{ open: false }" class="hover:bg-gray-50 family-row" data-name="{{ strtolower($family->family_name) }}" data-city="{{ strtolower($family->city ?? '') }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button @click="open = !open" class="text-gray-400 hover:text-purple-600 transition-colors">
                                <i class="fas fa-chevron-right transition-transform duration-200" :class="{ 'rotate-90': open }"></i>
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-users text-purple-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $family->family_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $family->address }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <i class="fas fa-phone mr-1"></i> {{ $family->phone }}
                            </div>
                            @if($family->email)
                            <div class="text-sm text-gray-500">
                                <i class="fas fa-envelope mr-1"></i> {{ $family->email }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $family->city }}</div>
                            <div class="text-sm text-gray-500">{{ $family->postal_code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-child mr-1"></i>
                                {{ $family->children->count() }} enfant(s)
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('families.show', $family) }}" class="text-blue-600 hover:text-blue-900 mr-3" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('families.edit', $family) }}" class="text-purple-600 hover:text-purple-900 mr-3" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('families.destroy', $family) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette famille ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <tr x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="family-detail">
                        <td colspan="6" class="bg-gray-50 px-6 py-4">
                            <div class="rounded-lg bg-white border border-gray-200 overflow-hidden">
                                <div class="px-4 py-2 bg-purple-50 border-b border-purple-100">
                                    <p class="text-sm font-medium text-purple-700">
                                        <i class="fas fa-child mr-2"></i> Enfants — {{ $family->family_name }}
                                    </p>
                                </div>
                                @if($family->children->count() > 0)
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Enfant</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Âge</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Allergies / Régime</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($family->children as $child)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm">
                                                <div class="flex items-center gap-2">
                                                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-700">
                                                        {{ strtoupper(substr($child->first_name, 0, 1) . substr($child->last_name, 0, 1)) }}
                                                    </div>
                                                    <span class="font-medium text-gray-900">{{ $child->full_name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ $child->class }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ $child->age }} ans</td>
                                            <td class="px-4 py-3 text-sm">
                                                @if($child->allergies)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i> {{ $child->allergies }}
                                                    </span>
                                                @endif
                                                @if($child->dietary_restrictions)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 ml-1">
                                                        <i class="fas fa-utensils mr-1"></i> {{ $child->dietary_restrictions }}
                                                    </span>
                                                @endif
                                                @if(!$child->allergies && !$child->dietary_restrictions)
                                                    <span class="text-gray-400 text-xs">Aucun</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                @if($child->is_active)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                        <i class="fas fa-check mr-1"></i> Actif
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                                        <i class="fas fa-times mr-1"></i> Inactif
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm whitespace-nowrap">
                                                <a href="{{ route('children.edit', $child) }}" class="text-purple-600 hover:text-purple-900 mr-2" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('children.destroy', $child) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cet enfant ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @else
                                <p class="text-sm text-gray-500 text-center py-4">
                                    <i class="fas fa-info-circle mr-1"></i> Aucun enfant enregistré pour cette famille
                                </p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                            <p>Aucune famille enregistrée</p>
                            <a href="{{ route('families.create') }}" class="mt-2 inline-block text-purple-600 hover:text-purple-700">
                                Créer la première famille
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterFamilies() {
    const query = document.getElementById('family-search').value.toLowerCase();
    const rows = document.querySelectorAll('.family-row');
    const details = document.querySelectorAll('.family-detail');
    
    rows.forEach((row, i) => {
        const name = row.dataset.name || '';
        const city = row.dataset.city || '';
        const match = name.includes(query) || city.includes(query);
        row.style.display = match ? '' : 'none';
        if (details[i]) details[i].style.display = match ? '' : 'none';
    });
}
</script>
@endsection
