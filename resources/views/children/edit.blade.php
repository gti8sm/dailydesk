@extends('layouts.app')

@section('title', 'Modifier ' . $child->full_name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('families.show', $child->family) }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Retour à la famille
        </a>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-500 to-blue-600">
            <h1 class="text-2xl font-bold text-white">
                <i class="fas fa-edit mr-2"></i>
                Modifier {{ $child->full_name }}
            </h1>
            <p class="text-blue-100 mt-1">Famille {{ $child->family->family_name }}</p>
        </div>

        @if($errors->any())
        <div class="mx-6 mt-6 bg-red-50 border-l-4 border-red-400 p-4 rounded">
            <div class="flex">
                <i class="fas fa-exclamation-circle text-red-400 mt-1"></i>
                <ul class="ml-3 text-sm text-red-700">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('children.update', $child) }}" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Prénom <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="first_name" id="first_name" required
                           value="{{ old('first_name', $child->first_name) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('first_name') border-red-500 @enderror">
                    @error('first_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="last_name" id="last_name" required
                           value="{{ old('last_name', $child->last_name) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('last_name') border-red-500 @enderror">
                    @error('last_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Date de naissance <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="birth_date" id="birth_date" required
                           value="{{ old('birth_date', $child->birth_date->format('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('birth_date') border-red-500 @enderror">
                    @error('birth_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Classe
                    </label>
                    <select name="class_id" id="class_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">-- Aucune --</option>
                        @foreach(\App\Models\SchoolClass::active()->orderBy('name')->get() as $cls)
                            <option value="{{ $cls->id }}" {{ old('class_id', $child->class_id) == $cls->id ? 'selected' : '' }}>{{ $cls->name }} ({{ $cls->school_year }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Abonnements -->
            <div class="mt-6 bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-bold text-gray-700 mb-3">
                    <i class="fas fa-bell mr-1"></i> Abonnements
                </h3>
                <div class="flex flex-col sm:flex-row gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="garderie_subscribed" value="1"
                               {{ old('garderie_subscribed', $child->garderie_subscribed) ? 'checked' : '' }}
                               class="h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700"><i class="fas fa-child text-blue-500 mr-1"></i> Inscrit à la Garderie</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="cantine_subscribed" value="1"
                               {{ old('cantine_subscribed', $child->cantine_subscribed) ? 'checked' : '' }}
                               class="h-5 w-5 text-green-600 rounded border-gray-300 focus:ring-green-500">
                        <span class="ml-2 text-sm text-gray-700"><i class="fas fa-utensils text-green-500 mr-1"></i> Inscrit à la Cantine</span>
                    </label>
                </div>
            </div>

            <div class="mt-6">
                <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                    Photo de l'enfant
                </label>
                @if($child->photo_path)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $child->photo_path) }}" alt="{{ $child->full_name }}" class="h-24 w-24 rounded-full object-cover">
                </div>
                @endisset
                <input type="file" name="photo" id="photo" accept="image/*"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG. Taille max: 2 Mo</p>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-1"></i>
                    Allergies
                </label>
                <textarea name="allergies" rows="3"
                          placeholder="Listez toutes les allergies connues..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('allergies', $child->allergies) }}</textarea>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-utensils text-orange-500 mr-1"></i>
                    Restrictions alimentaires
                </label>
                <textarea name="dietary_restrictions" rows="3"
                          placeholder="Régime sans gluten, végétarien, etc..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('dietary_restrictions', $child->dietary_restrictions) }}</textarea>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-notes-medical text-blue-500 mr-1"></i>
                    Notes médicales
                </label>
                <textarea name="medical_notes" rows="3"
                          placeholder="Traitements, conditions médicales, etc..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('medical_notes', $child->medical_notes) }}</textarea>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-phone text-green-500 mr-1"></i>
                    Contact d'urgence
                </label>
                <textarea name="emergency_contact" rows="2"
                          placeholder="Nom et numéro de téléphone..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('emergency_contact', $child->emergency_contact ?? '') }}</textarea>
            </div>

            <div class="mt-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $child->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Enfant actif</span>
                </label>
            </div>

            <div class="mt-8 flex justify-end gap-4">
                <a href="{{ route('families.show', $child->family) }}"
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-times mr-2"></i>
                    Annuler
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-lg">
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
