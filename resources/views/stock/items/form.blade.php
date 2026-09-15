@extends('layouts.app')

@section('title', $item->exists ? 'Modifier l\'article' : 'Nouvel article')

@section('content')
<div class="max-w-2xl mx-auto px-2 sm:px-4 lg:px-8">
    <div class="mb-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
            <i class="fas fa-box text-indigo-600 mr-2"></i>
            {{ $item->exists ? 'Modifier l\'article' : 'Nouvel article' }}
        </h1>
    </div>

    <div class="bg-white shadow-lg rounded-xl p-6">
        <form action="{{ $item->exists ? route('stock.items.update', $item) : route('stock.items.store') }}" method="POST">
            @csrf
            @if($item->exists) @method('PUT') @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $item->name) }}"
                           placeholder="Ex. Farine T55" required
                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lieu de stockage <span class="text-red-500">*</span></label>
                    <select name="location_id" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">— Choisir —</option>
                        @foreach($locations as $location)
                        <option value="{{ $location->id }}" @selected(old('location_id', $item->location_id) == $location->id)>{{ $location->name }}</option>
                        @endforeach
                    </select>
                    @error('location_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Référence</label>
                    <input type="text" name="reference" value="{{ old('reference', $item->reference) }}"
                           placeholder="Ex. FAR-001"
                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                    <input type="text" name="category" value="{{ old('category', $item->category) }}"
                           placeholder="Ex. Féculents"
                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unité <span class="text-red-500">*</span></label>
                    <select name="unit" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @php $units = ['pièce','kg','g','L','cL','mL','boîte','sac','cartouche','rouleau','paquet','litre','portion']; @endphp
                        @foreach($units as $unit)
                        <option value="{{ $unit }}" @selected(old('unit', $item->unit) == $unit)>{{ $unit }}</option>
                        @endforeach
                    </select>
                </div>

                @if(!$item->exists)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantité initiale <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity" value="{{ old('quantity', $item->quantity) }}" step="0.01" min="0" required
                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('quantity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                @else
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantité actuelle</label>
                    <input type="text" value="{{ number_format($item->quantity, 2) }} {{ $item->unit }}" disabled
                           class="w-full rounded-lg border-gray-200 bg-gray-50 text-gray-500">
                    <p class="text-xs text-gray-400 mt-1">Modifiable via les mouvements</p>
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Seuil d'alerte (min)</label>
                    <input type="number" name="min_quantity" value="{{ old('min_quantity', $item->min_quantity) }}" step="0.01" min="0"
                           placeholder="0 = pas d'alerte"
                           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="text-xs text-gray-400 mt-1">Alerte envoyée à l'admin si quantité ≤ seuil</p>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2"
                              class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $item->description) }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active ?? true))
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        Actif
                    </label>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between">
                <a href="{{ route('stock.items.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">Annuler</a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium shadow-md">
                    <i class="fas fa-save mr-2"></i>{{ $item->exists ? 'Enregistrer' : 'Créer' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
