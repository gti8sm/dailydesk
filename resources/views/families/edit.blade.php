@extends('layouts.app')

@section('title', 'Modifier la Famille')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('families.show', $family) }}" class="text-blue-600 hover:text-blue-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour à la famille
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-500 to-purple-600">
            <h1 class="text-2xl font-bold text-white">
                <i class="fas fa-edit mr-2"></i>
                Modifier la Famille
            </h1>
            <p class="text-purple-100 mt-1">{{ $family->family_name }}</p>
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

        <form action="{{ route('families.update', $family) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="family_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom de la Famille <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="family_name" 
                           id="family_name" 
                           value="{{ old('family_name', $family->family_name) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('family_name') border-red-500 @enderror">
                    @error('family_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Téléphone <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" 
                           name="phone" 
                           id="phone" 
                           value="{{ old('phone', $family->phone) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                    @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           value="{{ old('email', $family->email) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('email') border-red-500 @enderror">
                    @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div x-data="familyAddressAutocomplete()" class="relative">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        Adresse <span class="text-red-500">*</span> <span class="text-xs text-gray-400">(autocomplétion)</span>
                    </label>
                    <input type="text" 
                           name="address" 
                           id="address" 
                           value="{{ old('address', $family->address) }}"
                           @input.debounce.300ms="search()"
                           x-model="query"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('address') border-red-500 @enderror">
                    <div x-show="loading" x-cloak class="absolute right-3 top-10">
                        <i class="fas fa-spinner fa-spin text-purple-500"></i>
                    </div>
                    <div x-show="results.length > 0" x-cloak
                         class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                        <template x-for="result in results" :key="result.id">
                            <button type="button"
                                    @click="selectAddress(result)"
                                    class="w-full text-left px-4 py-2 hover:bg-purple-50 border-b border-gray-100 last:border-0">
                                <span class="text-sm font-medium text-gray-900" x-text="result.label"></span>
                                <span class="block text-xs text-gray-500" x-text="result.context"></span>
                            </button>
                        </template>
                    </div>
                    @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                        Code Postal <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="postal_code" 
                           id="postal_code" 
                           value="{{ old('postal_code', $family->postal_code) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('postal_code') border-red-500 @enderror">
                    @error('postal_code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                        Ville <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="city" 
                           id="city" 
                           value="{{ old('city', $family->city) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('city') border-red-500 @enderror">
                    @error('city')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notes
                    </label>
                    <textarea name="notes" 
                              id="notes" 
                              rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('notes') border-red-500 @enderror">{{ old('notes', $family->notes) }}</textarea>
                    @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Parents / Tuteurs -->
            <div x-data="{
                parents: {{ json_encode($family->parents->map(fn($p) => [
                    'id' => $p->id,
                    'first_name' => $p->first_name,
                    'last_name' => $p->last_name,
                    'email' => $p->email ?? '',
                    'phone' => $p->phone ?? '',
                    'mobile' => $p->mobile ?? '',
                    'address' => $p->address ?? '',
                    'postal_code' => $p->postal_code ?? '',
                    'city' => $p->city ?? '',
                    'relationship' => $p->relationship,
                    'is_primary_contact' => (bool) $p->is_primary_contact,
                    'can_pickup' => (bool) $p->can_pickup,
                    'is_legal_guardian' => (bool) $p->is_legal_guardian,
                ])) }},
                addParent() { this.parents.push({ first_name: '', last_name: '', email: '', phone: '', mobile: '', address: '', postal_code: '', city: '', relationship: 'mother', is_primary_contact: false, can_pickup: true, is_legal_guardian: false }); },
                removeParent(index) { if (this.parents.length > 1) { this.parents.splice(index, 1); } }
            }">
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
                        <input type="hidden" x-show="parent.id" :name="'parents[' + index + '][id]'" :value="parent.id">
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

            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('families.show', $family) }}" class="text-gray-600 hover:text-gray-800 font-medium">
                    <i class="fas fa-times mr-2"></i>
                    Annuler
                </a>
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg hover:shadow-xl">
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer les Modifications
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function familyAddressAutocomplete() {
    return {
        query: document.getElementById('address')?.value || '',
        results: [],
        loading: false,

        async search() {
            if (this.query.length < 3) {
                this.results = [];
                return;
            }
            this.loading = true;
            try {
                const res = await fetch(`https://api.adresse.data.gouv.fr/search/?q=${encodeURIComponent(this.query)}&limit=5`);
                const data = await res.json();
                this.results = (data.features || []).map(f => ({
                    id: f.properties.id,
                    label: f.properties.label,
                    name: f.properties.name,
                    postcode: f.properties.postcode,
                    city: f.properties.city,
                    context: f.properties.context,
                }));
            } catch (e) {
                console.error('Erreur autocomplétion adresse:', e);
                this.results = [];
            } finally {
                this.loading = false;
            }
        },

        selectAddress(result) {
            this.query = result.label;
            this.results = [];

            document.getElementById('address').value = result.name;
            document.getElementById('postal_code').value = result.postcode;
            document.getElementById('city').value = result.city;
        }
    };
}
</script>
@endsection
