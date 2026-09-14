@extends('layouts.app')

@section('title', 'Nouveau ticket de support')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('support.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Retour aux tickets
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-orange-500 to-orange-400 text-white">
            <h1 class="text-2xl font-bold">
                <i class="fas fa-life-ring mr-2"></i> Nouveau ticket
            </h1>
            <p class="text-orange-100 mt-1">Décrivez votre demande, notre équipe vous répondra rapidement</p>
        </div>

        @if(session('error'))
        <div class="mx-6 mt-6 bg-red-50 border-l-4 border-red-400 p-4 rounded">
            <p class="text-sm text-red-700">{{ session('error') }}</p>
        </div>
        @endif

        <form action="{{ route('support.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                    Sujet <span class="text-red-500">*</span>
                </label>
                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('subject') border-red-500 @enderror"
                       placeholder="Ex: Problème d'affichage du planning">
                @error('subject')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                        Catégorie <span class="text-red-500">*</span>
                    </label>
                    <select name="category" id="category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="general" {{ old('category') === 'general' ? 'selected' : '' }}>Général</option>
                        <option value="technical" {{ old('category') === 'technical' ? 'selected' : '' }}>Technique</option>
                        <option value="bug" {{ old('category') === 'bug' ? 'selected' : '' }}>Bug</option>
                        <option value="billing" {{ old('category') === 'billing' ? 'selected' : '' }}>Facturation</option>
                        <option value="feature_request" {{ old('category') === 'feature_request' ? 'selected' : '' }}>Demande de fonctionnalité</option>
                    </select>
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                        Priorité <span class="text-red-500">*</span>
                    </label>
                    <select name="priority" id="priority" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Basse</option>
                        <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normale</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Haute</option>
                        <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea name="description" id="description" rows="6" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('description') border-red-500 @enderror"
                          placeholder="Décrivez votre problème ou demande en détail...">{{ old('description') }}</textarea>
                @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">
                    Captures d'écran / Pièces jointes
                </label>
                <input type="file" name="attachments[]" id="attachments" multiple
                       accept="image/*,application/pdf,.doc,.docx"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                <p class="mt-1 text-xs text-gray-500">JPG, PNG, GIF, WebP, PDF, DOC — 5 Mo max par fichier</p>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <a href="{{ route('support.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                    <i class="fas fa-times mr-2"></i> Annuler
                </a>
                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg font-medium shadow-lg">
                    <i class="fas fa-paper-plane mr-2"></i> Envoyer le ticket
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
