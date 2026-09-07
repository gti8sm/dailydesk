@extends('layouts.app')

@section('title', 'Modifier le Plan')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('central.plans.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour aux plans
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-500 to-green-600">
            <h1 class="text-2xl font-bold text-white">
                <i class="fas fa-edit mr-2"></i>
                Modifier le Plan
            </h1>
            <p class="text-green-100 mt-1">{{ $plan->name }}</p>
        </div>

        <form action="{{ route('central.plans.update', $plan) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Informations générales -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-info-circle text-purple-600 mr-2"></i>
                    Informations Générales
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom du Plan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', $plan->name) }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                            Slug (identifiant)
                        </label>
                        <input type="text" 
                               name="slug" 
                               id="slug" 
                               value="{{ $plan->slug }}"
                               disabled
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed">
                        <p class="mt-1 text-xs text-gray-500">Le slug ne peut pas être modifié</p>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('description') border-red-500 @enderror"
                                  placeholder="Description courte du plan...">{{ old('description', $plan->description) }}</textarea>
                        @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Tarification -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-euro-sign text-green-600 mr-2"></i>
                    Tarification
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="price_monthly" class="block text-sm font-medium text-gray-700 mb-2">
                            Prix Mensuel (€) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="price_monthly" 
                               id="price_monthly" 
                               value="{{ old('price_monthly', $plan->price_monthly) }}"
                               required
                               min="0"
                               step="0.01"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('price_monthly') border-red-500 @enderror">
                        @error('price_monthly')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price_yearly" class="block text-sm font-medium text-gray-700 mb-2">
                            Prix Annuel (€)
                        </label>
                        <input type="number" 
                               name="price_yearly" 
                               id="price_yearly" 
                               value="{{ old('price_yearly', $plan->price_yearly) }}"
                               min="0"
                               step="0.01"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('price_yearly') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Optionnel - Tarif annuel avec réduction</p>
                        @error('price_yearly')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="max_children" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre d'Enfants Max <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="max_children" 
                               id="max_children" 
                               value="{{ old('max_children', $plan->max_children) }}"
                               required
                               min="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('max_children') border-red-500 @enderror">
                        @error('max_children')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Modules -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-puzzle-piece text-blue-600 mr-2"></i>
                    Modules Inclus <span class="text-red-500">*</span>
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition-colors">
                        <input type="checkbox" 
                               name="modules[]" 
                               value="garderie" 
                               {{ in_array('garderie', old('modules', $plan->modules ?? [])) ? 'checked' : '' }}
                               class="w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                        <div class="ml-3">
                            <span class="font-medium text-gray-900">Garderie</span>
                            <p class="text-sm text-gray-500">Gestion des présences garderie</p>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition-colors">
                        <input type="checkbox" 
                               name="modules[]" 
                               value="cantine" 
                               {{ in_array('cantine', old('modules', $plan->modules ?? [])) ? 'checked' : '' }}
                               class="w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                        <div class="ml-3">
                            <span class="font-medium text-gray-900">Cantine</span>
                            <p class="text-sm text-gray-500">Gestion des présences cantine</p>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition-colors">
                        <input type="checkbox" 
                               name="modules[]" 
                               value="alsh" 
                               {{ in_array('alsh', old('modules', $plan->modules ?? [])) ? 'checked' : '' }}
                               class="w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                        <div class="ml-3">
                            <span class="font-medium text-gray-900">ALSH</span>
                            <p class="text-sm text-gray-500">Accueil de loisirs</p>
                        </div>
                    </label>

                    <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition-colors">
                        <input type="checkbox" 
                               name="modules[]" 
                               value="facturation" 
                               {{ in_array('facturation', old('modules', $plan->modules ?? [])) ? 'checked' : '' }}
                               class="w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                        <div class="ml-3">
                            <span class="font-medium text-gray-900">Facturation</span>
                            <p class="text-sm text-gray-500">Gestion des factures</p>
                        </div>
                    </label>
                </div>
                @error('modules')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Fonctionnalités -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-star text-yellow-600 mr-2"></i>
                    Fonctionnalités Additionnelles
                </h2>
                
                <div id="features-container" class="space-y-3">
                    @php
                        $features = old('features', $plan->features ?? []);
                    @endphp
                    @if(count($features) > 0)
                        @foreach($features as $feature)
                        <div class="flex gap-2 feature-item">
                            <input type="text" 
                                   name="features[]" 
                                   value="{{ $feature }}"
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   placeholder="Ex: Support prioritaire">
                            <button type="button" onclick="removeFeature(this)" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        @endforeach
                    @else
                    <div class="flex gap-2 feature-item">
                        <input type="text" 
                               name="features[]" 
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="Ex: Support prioritaire">
                        <button type="button" onclick="removeFeature(this)" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    @endif
                </div>
                <button type="button" onclick="addFeature()" class="mt-3 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Ajouter une fonctionnalité
                </button>
            </div>

            <!-- Statut -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-toggle-on text-green-600 mr-2"></i>
                    Statut
                </h2>
                
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="is_active" 
                           value="1" 
                           {{ old('is_active', $plan->is_active) ? 'checked' : '' }}
                           class="w-5 h-5 text-green-600 rounded focus:ring-2 focus:ring-green-500">
                    <span class="ml-3 text-gray-700">Plan actif (visible pour les nouveaux tenants)</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('central.plans.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                    <i class="fas fa-times mr-2"></i>
                    Annuler
                </a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg hover:shadow-xl">
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer les Modifications
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Gestion des fonctionnalités
function addFeature() {
    const container = document.getElementById('features-container');
    const newFeature = document.createElement('div');
    newFeature.className = 'flex gap-2 feature-item';
    newFeature.innerHTML = `
        <input type="text" 
               name="features[]" 
               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
               placeholder="Ex: Support prioritaire">
        <button type="button" onclick="removeFeature(this)" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(newFeature);
}

function removeFeature(button) {
    const container = document.getElementById('features-container');
    if (container.children.length > 1) {
        button.closest('.feature-item').remove();
    }
}
</script>
@endsection
