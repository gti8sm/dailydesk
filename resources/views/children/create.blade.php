@extends('layouts.app')

@section('title', 'Ajouter un enfant')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('families.show', $family) }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Retour à la famille
        </a>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-child text-blue-600 mr-2"></i>
                Ajouter un enfant à la famille {{ $family->family_name }}
            </h1>
        </div>

        <form method="POST" action="{{ route('families.children.store', $family) }}" enctype="multipart/form-data" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Prénom -->
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Prénom <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="first_name" id="first_name" required
                           value="{{ old('first_name') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('first_name') border-red-500 @enderror">
                    @error('first_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nom -->
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="last_name" id="last_name" required
                           value="{{ old('last_name', $family->family_name) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('last_name') border-red-500 @enderror">
                    @error('last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date de naissance -->
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Date de naissance <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="birth_date" id="birth_date" required
                           value="{{ old('birth_date') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('birth_date') border-red-500 @enderror">
                    @error('birth_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Classe -->
                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Classe
                    </label>
                    <select name="class_id" id="class_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Aucune --</option>
                        @foreach(\App\Models\SchoolClass::active()->orderBy('name')->get() as $cls)
                            <option value="{{ $cls->id }}" {{ old('class_id') == $cls->id ? 'selected' : '' }}>{{ $cls->name }} ({{ $cls->school_year }})</option>
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
                               {{ old('garderie_subscribed') ? 'checked' : '' }}
                               class="h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700"><i class="fas fa-child text-blue-500 mr-1"></i> Inscrit à la Garderie</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="cantine_subscribed" value="1"
                               {{ old('cantine_subscribed') ? 'checked' : '' }}
                               class="h-5 w-5 text-green-600 rounded border-gray-300 focus:ring-green-500">
                        <span class="ml-2 text-sm text-gray-700"><i class="fas fa-utensils text-green-500 mr-1"></i> Inscrit à la Cantine</span>
                    </label>
                </div>
            </div>

            <!-- Photo -->
            <div class="mt-6">
                <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                    Photo de l'enfant
                </label>
                <input type="file" name="photo" id="photo" accept="image/*"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG. Taille max: 2 Mo</p>
            </div>

            <!-- Allergies -->
            <div class="mt-6">
                <label for="allergies" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-1"></i>
                    Allergies
                </label>
                <textarea name="allergies" id="allergies" rows="3"
                          placeholder="Listez toutes les allergies connues..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('allergies') }}</textarea>
            </div>

            <!-- Restrictions alimentaires -->
            <div class="mt-6">
                <label for="dietary_restrictions" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-utensils text-orange-500 mr-1"></i>
                    Restrictions alimentaires
                </label>
                <textarea name="dietary_restrictions" id="dietary_restrictions" rows="3"
                          placeholder="Régime sans gluten, végétarien, etc..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('dietary_restrictions') }}</textarea>
            </div>

            <!-- Notes médicales -->
            <div class="mt-6">
                <label for="medical_notes" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-notes-medical text-blue-500 mr-1"></i>
                    Notes médicales
                </label>
                <textarea name="medical_notes" id="medical_notes" rows="3"
                          placeholder="Traitements, conditions médicales, etc..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('medical_notes') }}</textarea>
            </div>

            <!-- Contact d'urgence -->
            <div class="mt-6">
                <label for="emergency_contact" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-phone text-green-500 mr-1"></i>
                    Contact d'urgence
                </label>
                <textarea name="emergency_contact" id="emergency_contact" rows="2"
                          placeholder="Nom et numéro de téléphone..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('emergency_contact') }}</textarea>
            </div>

            <!-- Boutons -->
            <div class="mt-8 flex justify-end gap-4">
                <a href="{{ route('families.show', $family) }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>
                    Ajouter l'enfant
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
