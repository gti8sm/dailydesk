@extends('layouts.app')

@section('title', 'Modifier - ' . $child->full_name)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('parent.dashboard') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Retour
        </a>
    </div>

    <h1 class="text-3xl font-bold text-gray-900 mb-6">
        <i class="fas fa-child text-blue-600 mr-2"></i>{{ $child->full_name }}
    </h1>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <form action="{{ route('parent.children.update', $child) }}" method="POST" class="bg-white shadow-lg rounded-xl p-6 space-y-6">
        @csrf
        @method('PUT')

        <!-- Abonnements -->
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-sm font-bold text-gray-700 mb-3">
                <i class="fas fa-bell mr-1"></i> Inscriptions
            </h3>
            <div class="flex flex-col sm:flex-row gap-4">
                <label class="flex items-center">
                    <input type="checkbox" name="garderie_subscribed" value="1"
                           {{ old('garderie_subscribed', $child->garderie_subscribed) ? 'checked' : '' }}
                           class="h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700"><i class="fas fa-child text-blue-500 mr-1"></i> Garderie (matin/soir)</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="cantine_subscribed" value="1"
                           {{ old('cantine_subscribed', $child->cantine_subscribed) ? 'checked' : '' }}
                           class="h-5 w-5 text-green-600 rounded border-gray-300 focus:ring-green-500">
                    <span class="ml-2 text-sm text-gray-700"><i class="fas fa-utensils text-green-500 mr-1"></i> Cantine</span>
                </label>
            </div>
        </div>

        <!-- Allergies -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-exclamation-triangle text-red-500 mr-1"></i> Allergies
            </label>
            <textarea name="allergies" rows="3"
                      placeholder="Listez toutes les allergies connues..."
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('allergies', $child->allergies) }}</textarea>
        </div>

        <!-- Restrictions alimentaires -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-utensils text-orange-500 mr-1"></i> Restrictions alimentaires
            </label>
            <textarea name="dietary_restrictions" rows="3"
                      placeholder="Régime sans gluten, végétarien, etc..."
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('dietary_restrictions', $child->dietary_restrictions) }}</textarea>
        </div>

        <!-- Notes médicales -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-notes-medical text-blue-500 mr-1"></i> Notes médicales
            </label>
            <textarea name="medical_notes" rows="3"
                      placeholder="Traitements, conditions médicales, etc..."
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('medical_notes', $child->medical_notes) }}</textarea>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                <i class="fas fa-save mr-2"></i>Enregistrer
            </button>
        </div>
    </form>
</div>
@endsection
