@extends('layouts.app')

@section('title', 'Intercommunalités')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-handshake text-indigo-600 mr-2"></i>Intercommunalités
            </h1>
            <p class="text-sm text-gray-500 mt-1">Gestion des regroupements de communes (écoles partagées)</p>
        </div>
        <a href="{{ route('central.intercommunalities.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-plus mr-1"></i> Nouvelle intercommunalité
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Communes</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($intercommunalities as $interco)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $interco->name }}</div>
                        @if($interco->siren)<div class="text-xs text-gray-400">SIREN: {{ $interco->siren }}</div>@endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $interco->type_label }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            {{ $interco->tenants_count }} commune(s)
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($interco->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('central.intercommunalities.show', $interco) }}" class="text-blue-600 hover:text-blue-900 mr-3" title="Voir"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('central.intercommunalities.edit', $interco) }}" class="text-green-600 hover:text-green-900 mr-3" title="Modifier"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('central.intercommunalities.destroy', $interco) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette intercommunalité ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" title="Supprimer"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-handshake text-4xl text-gray-300 mb-2"></i>
                        <p>Aucune intercommunalité créée pour le moment.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($intercommunalities->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">{{ $intercommunalities->links() }}</div>
        @endif
    </div>
</div>
@endsection
