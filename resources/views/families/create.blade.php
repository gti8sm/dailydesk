@extends('layouts.app')

@section('title', 'Nouvelle Famille')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('families.index') }}" class="text-purple-600 hover:text-purple-700">
            <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">
            <i class="fas fa-users text-purple-600 mr-2"></i>
            Nouvelle Famille
        </h1>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('families.store') }}">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informations de la Famille</h3>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="family_name" class="block text-sm font-medium text-gray-700">
                                Nom de famille *
                            </label>
                            <input type="text" name="family_name" id="family_name" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        </div>

                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">
                                Adresse *
                            </label>
                            <input type="text" name="address" id="address" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-gray-700">
                                    Code postal *
                                </label>
                                <input type="text" name="postal_code" id="postal_code" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700">
                                    Ville *
                                </label>
                                <input type="text" name="city" id="city" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">
                                    Téléphone *
                                </label>
                                <input type="tel" name="phone" id="phone" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">
                                    Email
                                </label>
                                <input type="email" name="email" id="email"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">
                                Notes
                            </label>
                            <textarea name="notes" id="notes" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Parents / Tuteurs -->
                <div x-data="{ parents: [{ first_name: '', last_name: '', email: '', phone: '', mobile: '', address: '', postal_code: '', city: '', relationship: 'mother', is_primary_contact: true, can_pickup: true, is_legal_guardian: false }], addParent() { this.parents.push({ first_name: '', last_name: '', email: '', phone: '', mobile: '', address: '', postal_code: '', city: '', relationship: 'father', is_primary_contact: false, can_pickup: true, is_legal_guardian: false }); }, removeParent(index) { if (this.parents.length > 1) { this.parents.splice(index, 1); } } }">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-user-friends text-purple-600 mr-2"></i>
                            Parents / Tuteurs
                        </h3>
                        <button type="button" @click="addParent()" class="px-3 py-1.5 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 text-sm font-medium">
                            <i class="fas fa-plus mr-1"></i>Ajouter
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 mb-4">Renseignez chaque parent ou tuteur. En cas de parents séparés, indiquez l'adresse de chacun.</p>

                    <template x-for="(parent, index) in parents" :key="index">
                        <div class="mb-6 p-4 border border-gray-200 rounded-lg bg-gray-50">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-semibold text-gray-700" x-text="'Parent / Tuteur ' + (index + 1)"></span>
                                <button type="button" x-show="parents.length > 1" @click="removeParent(index)" class="text-red-500 hover:text-red-700 text-sm">
                                    <i class="fas fa-trash mr-1"></i>Retirer
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Prénom *</label>
                                    <input type="text" :name="'parents[' + index + '][first_name]'" x-model="parent.first_name" required
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                                    <input type="text" :name="'parents[' + index + '][last_name]'" x-model="parent.last_name" required
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Lien</label>
                                    <select :name="'parents[' + index + '][relationship]'" x-model="parent.relationship"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                        <option value="mother">Mère</option>
                                        <option value="father">Père</option>
                                        <option value="guardian">Tuteur légal</option>
                                        <option value="other">Autre</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" :name="'parents[' + index + '][email]'" x-model="parent.email"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                                    <input type="tel" :name="'parents[' + index + '][phone]'" x-model="parent.phone"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Mobile</label>
                                    <input type="tel" :name="'parents[' + index + '][mobile]'" x-model="parent.mobile"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </div>
                            </div>
                            <div class="mt-3 p-3 bg-white rounded-md border border-gray-100">
                                <p class="text-xs text-gray-500 mb-2">Adresse (si différente de l'adresse de la famille)</p>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div class="md:col-span-3">
                                        <input type="text" :name="'parents[' + index + '][address]'" x-model="parent.address" placeholder="Adresse"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm">
                                    </div>
                                    <div>
                                        <input type="text" :name="'parents[' + index + '][postal_code]'" x-model="parent.postal_code" placeholder="Code postal"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm">
                                    </div>
                                    <div class="md:col-span-2">
                                        <input type="text" :name="'parents[' + index + '][city]'" x-model="parent.city" placeholder="Ville"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-4">
                                <label class="flex items-center text-sm text-gray-700">
                                    <input type="checkbox" :name="'parents[' + index + '][is_primary_contact]'" x-model="parent.is_primary_contact" @change="parents.forEach((p, i) => { if (i !== index) p.is_primary_contact = false; })" class="mr-2 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                    Contact principal
                                </label>
                                <label class="flex items-center text-sm text-gray-700">
                                    <input type="checkbox" :name="'parents[' + index + '][can_pickup]'" x-model="parent.can_pickup" class="mr-2 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                    Autorisé à récupérer l'enfant
                                </label>
                                <label class="flex items-center text-sm text-gray-700">
                                    <input type="checkbox" :name="'parents[' + index + '][is_legal_guardian]'" x-model="parent.is_legal_guardian" class="mr-2 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                    Tuteur légal
                                </label>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('families.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                        <i class="fas fa-save mr-2"></i>
                        Enregistrer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
