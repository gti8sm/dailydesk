@extends('layouts.app')

@section('title', 'Écoles')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour au dashboard
            </a>
        </div>
        <a href="{{ route('schools.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Nouvelle École
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-500 to-blue-600">
            <h1 class="text-2xl font-bold text-white">
                <i class="fas fa-school mr-2"></i>
                Écoles
            </h1>
            <p class="text-blue-100 mt-1">Gérez les écoles de votre commune</p>
        </div>

        @if(session('success'))
        <div class="mx-6 mt-6 bg-green-50 border-l-4 border-green-400 p-4 rounded">
            <div class="flex">
                <i class="fas fa-check-circle text-green-400 mt-1"></i>
                <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mx-6 mt-6 bg-red-50 border-l-4 border-red-400 p-4 rounded">
            <div class="flex">
                <i class="fas fa-exclamation-circle text-red-400 mt-1"></i>
                <p class="ml-3 text-sm text-red-700">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        <div class="p-6">
            @if($schools->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($schools as $school)
                <div class="border-2 {{ $school->is_active ? 'border-blue-300' : 'border-gray-300' }} rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xl font-bold text-gray-900">{{ $school->name }}</h3>
                            <div class="flex items-center gap-1">
                                @if($school->is_shared)
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-indigo-100 text-indigo-800" title="École mutualisée dans {{ $school->intercommunality?->name }}">
                                    <i class="fas fa-handshake mr-0.5"></i>Partagée
                                </span>
                                @endif
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $school->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $school->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">
                            <i class="fas fa-tag mr-1"></i>{{ $school->type_label }}
                            @if($school->address)
                            <br><i class="fas fa-map-marker-alt mr-1"></i>{{ $school->address }}
                            @endif
                        </p>
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div class="bg-blue-50 rounded-lg p-3">
                                <div class="text-2xl font-bold text-blue-600">{{ $school->children_count }}</div>
                                <div class="text-xs text-gray-500">Enfants</div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-3">
                                <div class="text-2xl font-bold text-green-600">{{ $school->classes_count }}</div>
                                <div class="text-xs text-gray-500">Classes</div>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-3">
                                <div class="text-2xl font-bold text-purple-600">{{ $school->users_count }}</div>
                                <div class="text-xs text-gray-500">Agents</div>
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex gap-2">
                        <a href="{{ route('schools.edit', $school) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-center text-sm font-medium transition-colors">
                            <i class="fas fa-edit mr-1"></i> Modifier
                        </a>
                        @if($school->children_count === 0)
                        <form action="{{ route('schools.destroy', $school) }}" method="POST" class="flex-1" onsubmit="return confirm('Supprimer cette école ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded text-sm font-medium transition-colors">
                                <i class="fas fa-trash mr-1"></i> Supprimer
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <i class="fas fa-school text-gray-400 text-5xl mb-4"></i>
                <p class="text-gray-500 text-lg">Aucune école configurée</p>
                <a href="{{ route('schools.create') }}" class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    <i class="fas fa-plus mr-2"></i> Créer la première école
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
