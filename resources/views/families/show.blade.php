@extends('layouts.app')

@section('title', 'Famille ' . $family->family_name)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('families.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Retour à la liste
        </a>
        <div class="flex gap-2">
            <a href="{{ route('families.edit', $family) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                <i class="fas fa-edit mr-1"></i>Modifier
            </a>
            <form action="{{ route('families.destroy', $family) }}" method="POST" onsubmit="return confirm('Supprimer cette famille ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-trash mr-1"></i>Supprimer
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Informations famille -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-users text-purple-600 mr-2"></i>
                Famille {{ $family->family_name }}
            </h1>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Adresse</p>
                <p class="text-sm font-medium text-gray-900">{{ $family->address ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Téléphone</p>
                <p class="text-sm font-medium text-gray-900">{{ $family->phone ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Email</p>
                <p class="text-sm font-medium text-gray-900">{{ $family->email ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Ville</p>
                <p class="text-sm font-medium text-gray-900">{{ $family->postal_code ?? '-' }} {{ $family->city ?? '-' }}</p>
            </div>
            @if($family->notes)
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500">Notes</p>
                <p class="text-sm font-medium text-gray-900">{{ $family->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Enfants -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-900">
                <i class="fas fa-child text-blue-600 mr-2"></i>
                Enfants ({{ $family->children->count() }})
            </h2>
            <a href="{{ route('families.children.create', $family) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-1"></i>Ajouter un enfant
            </a>
        </div>
        <div class="p-6">
            @forelse($family->children as $child)
            <div class="mb-4 p-4 border rounded-lg hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="font-bold text-lg text-gray-900">{{ $child->full_name }}</h3>
                        <p class="text-sm text-gray-600">
                            Né(e) le {{ \Carbon\Carbon::parse($child->birth_date)->format('d/m/Y') }}
                            ({{ $child->age }} ans)
                        </p>
                        @if($child->class)
                        <p class="text-sm text-gray-600">Classe : {{ $child->class }}</p>
                        @endif
                        @if($child->allergies)
                        <p class="text-sm text-red-600 mt-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Allergies : {{ $child->allergies }}
                        </p>
                        @endif
                        @if($child->dietary_restrictions)
                        <p class="text-sm text-yellow-700 mt-1">
                            <i class="fas fa-info-circle mr-1"></i> Restrictions : {{ $child->dietary_restrictions }}
                        </p>
                        @endif
                    </div>
                    <div class="flex gap-2 ml-4">
                        <a href="{{ route('children.edit', $child) }}" class="text-purple-600 hover:text-purple-900" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('children.destroy', $child) }}" method="POST" onsubmit="return confirm('Supprimer cet enfant ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8">
                <i class="fas fa-child text-4xl text-gray-300 mb-2"></i>
                <p class="text-gray-500">Aucun enfant enregistré</p>
                <a href="{{ route('families.children.create', $family) }}" class="mt-2 inline-block text-blue-600 hover:text-blue-700">
                    Ajouter le premier enfant
                </a>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
