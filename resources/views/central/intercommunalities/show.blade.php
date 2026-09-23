@extends('layouts.app')

@section('title', 'Intercommunalité')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('central.intercommunalities.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Retour aux intercommunalités
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-500 to-indigo-600 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $intercommunality->name }}</h1>
                <p class="text-indigo-100 text-sm">{{ $intercommunality->type_label }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('central.intercommunalities.edit', $intercommunality) }}" class="bg-white text-indigo-600 px-3 py-1.5 rounded-lg font-medium text-sm hover:bg-indigo-50">
                    <i class="fas fa-edit mr-1"></i> Modifier
                </a>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-gray-500 uppercase">SIREN</p>
                <p class="text-sm text-gray-800">{{ $intercommunality->siren ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase">Statut</p>
                @if($intercommunality->is_active)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                @endif
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase">Adresse</p>
                <p class="text-sm text-gray-800">{{ $intercommunality->address ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase">Ville</p>
                <p class="text-sm text-gray-800">{{ $intercommunality->postal_code ?? '' }} {{ $intercommunality->city ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase">Téléphone</p>
                <p class="text-sm text-gray-800">{{ $intercommunality->phone ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase">Email</p>
                <p class="text-sm text-gray-800">{{ $intercommunality->email ?? '—' }}</p>
            </div>
        </div>
    </div>

    <!-- Communes membres -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
        <div class="px-5 py-3 border-b border-gray-200">
            <h3 class="font-bold text-gray-900"><i class="fas fa-building text-indigo-500 mr-2"></i>Communes membres ({{ $intercommunality->tenants->count() }})</h3>
        </div>
        <div class="p-5">
            @if($intercommunality->tenants->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @foreach($intercommunality->tenants as $tenant)
                <div class="border border-gray-200 rounded-lg p-3">
                    <div class="font-medium text-gray-900">{{ $tenant->name }}</div>
                    <div class="text-xs text-gray-500">{{ $tenant->city ?? '' }} — {{ $tenant->postal_code ?? '' }}</div>
                    <a href="{{ route('central.tenants.show', $tenant) }}" class="text-xs text-indigo-600 hover:text-indigo-800 mt-1 inline-block">Voir le tenant →</a>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-sm">Aucune commune rattachée.</p>
            @endif
        </div>
    </div>

    <!-- Écoles partagées -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200">
            <h3 class="font-bold text-gray-900"><i class="fas fa-school text-indigo-500 mr-2"></i>Écoles partagées ({{ $intercommunality->schools->count() }})</h3>
        </div>
        <div class="p-5">
            @if($intercommunality->schools->isNotEmpty())
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">École</th>
                        <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Propriétaire</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($intercommunality->schools as $school)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-800">{{ $school->name }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $school->type_label }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $school->ownerTenant?->name ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-gray-500 text-sm">Aucune école partagée dans cette intercommunalité. Rattachez des écoles via l'édition d'une école côté tenant.</p>
            @endif
        </div>
    </div>
</div>
@endsection
