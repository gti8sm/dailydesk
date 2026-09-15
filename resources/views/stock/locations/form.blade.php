@extends('layouts.app')

@section('title', $location->exists ? 'Modifier le lieu' : 'Nouveau lieu de stockage')

@section('content')
<div class="max-w-2xl mx-auto px-2 sm:px-4 lg:px-8">
    <div class="mb-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
            <i class="fas fa-warehouse text-indigo-600 mr-2"></i>
            {{ $location->exists ? 'Modifier le lieu' : 'Nouveau lieu' }}
        </h1>
    </div>

    <div class="bg-white shadow-lg rounded-xl p-6">
        <form action="{{ $location->exists ? route('stock.locations.update', $location) : route('stock.locations.store') }}" method="POST">
            @csrf
            @if($location->exists) @method('PUT') @endif

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $location->name) }}"
                       placeholder="Ex. Cuisine centrale" required
                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3" placeholder="Ex. Réserve principale de la cantine"
                          class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $location->description) }}</textarea>
            </div>

            <div class="mb-6">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $location->is_active ?? true))
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    Actif
                </label>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('stock.locations.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">Annuler</a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium shadow-md">
                    <i class="fas fa-save mr-2"></i>{{ $location->exists ? 'Enregistrer' : 'Créer' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
