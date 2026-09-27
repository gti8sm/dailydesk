@extends('layouts.app')

@section('title', 'Créer un événement')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('site.events.index') }}" class="text-emerald-600 hover:text-emerald-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Retour aux événements
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-emerald-500 to-emerald-600">
            <h1 class="text-2xl font-bold text-white"><i class="fas fa-plus-circle mr-2"></i>Créer un événement</h1>
        </div>

        <form action="{{ route('site.events.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Titre <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Slug (URL)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500"
                           placeholder="auto-généré si vide">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date de début <span class="text-red-500">*</span></label>
                    <input type="date" name="starts_at" value="{{ old('starts_at') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date de fin</label>
                    <input type="date" name="ends_at" value="{{ old('ends_at') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Lieu</label>
                <input type="text" name="location" value="{{ old('location') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500"
                       placeholder="Ex: Salle des fêtes, Place de la Mairie...">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                <p class="mt-1 text-xs text-gray-500">Image d'illustration (max 2 Mo)</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="6"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <span class="text-sm font-medium text-gray-700">Publier cet événement</span>
                </label>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-200">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-lg font-medium shadow-lg">
                    <i class="fas fa-check mr-2"></i>Créer l'événement
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
