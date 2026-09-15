@extends('layouts.app')

@section('title', $dish->exists ? 'Modifier le plat' : 'Nouveau plat')

@section('content')
<div class="max-w-2xl mx-auto px-2 sm:px-4 lg:px-8">
    <div class="mb-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
            <i class="fas fa-utensils text-green-600 mr-2"></i>
            {{ $dish->exists ? 'Modifier le plat' : 'Nouveau plat' }}
        </h1>
        <p class="mt-1 text-xs sm:text-sm text-gray-600">Enregistrez un plat réutilisable dans les menus</p>
    </div>

    <div class="bg-white shadow-lg rounded-xl p-6">
        <form action="{{ $dish->exists ? route('cantine.dishes.update', $dish) : route('cantine.dishes.store') }}" method="POST">
            @csrf
            @if($dish->exists) @method('PUT') @endif

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom du plat <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $dish->name) }}"
                       placeholder="Ex. Blanquette de veau" required
                       class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie <span class="text-red-500">*</span></label>
                <select name="category" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="starter" @selected(old('category', $dish->category) === 'starter')>Entrée</option>
                    <option value="main_course" @selected(old('category', $dish->category) === 'main_course')>Plat principal</option>
                    <option value="side_dish" @selected(old('category', $dish->category) === 'side_dish')>Accompagnement</option>
                    <option value="dessert" @selected(old('category', $dish->category) === 'dessert')>Dessert</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Allergènes (séparés par des virgules)</label>
                <input type="text" name="allergens_input" value="{{ old('allergens_input', is_array($dish->allergens) ? implode(', ', $dish->allergens) : '') }}"
                       placeholder="Ex. gluten, lactose, œufs"
                       class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
            </div>

            <div class="mb-4">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                    <input type="checkbox" name="vegetarian" value="1" @checked(old('vegetarian', $dish->vegetarian))
                           class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                    Végétarien
                </label>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2" placeholder="Variantes, conseils…"
                          class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">{{ old('notes', $dish->notes) }}</textarea>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('cantine.dishes.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">Annuler</a>
                <button type="submit" class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium shadow-md">
                    <i class="fas fa-save mr-2"></i>{{ $dish->exists ? 'Enregistrer' : 'Créer' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
