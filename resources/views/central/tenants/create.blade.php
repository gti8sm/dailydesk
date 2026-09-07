@extends('layouts.app')

@section('title', 'Créer un Tenant')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour au dashboard
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-500 to-blue-600">
            <h1 class="text-2xl font-bold text-white">
                <i class="fas fa-plus-circle mr-2"></i>
                Créer un Nouveau Tenant
            </h1>
            <p class="text-blue-100 mt-1">Ajoutez une nouvelle mairie à la plateforme</p>
        </div>

        @if(session('error'))
        <div class="mx-6 mt-6 bg-red-50 border-l-4 border-red-400 p-4 rounded">
            <div class="flex">
                <i class="fas fa-exclamation-circle text-red-400 mt-1"></i>
                <p class="ml-3 text-sm text-red-700">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        <form action="{{ route('central.tenants.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Informations du Tenant -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-building text-blue-600 mr-2"></i>
                    Informations du Tenant
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom de la Mairie <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                               placeholder="Ex: Mairie de Paris">
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                            Slug (URL) <span class="text-red-500">*</span>
                        </label>
                        <div class="flex">
                            <input type="text" 
                                   name="slug" 
                                   id="slug" 
                                   value="{{ old('slug') }}"
                                   required
                                   pattern="[a-z0-9-]+"
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('slug') border-red-500 @enderror"
                                   placeholder="paris">
                            <span class="inline-flex items-center px-4 py-2 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm rounded-r-lg">
                                .localhost
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Uniquement lettres minuscules, chiffres et tirets</p>
                        @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email de Contact <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email') }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                               placeholder="contact@mairie.fr">
                        @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Téléphone
                        </label>
                        <input type="tel" 
                               name="phone" 
                               id="phone" 
                               value="{{ old('phone') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                               placeholder="01 23 45 67 89">
                        @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Adresse
                        </label>
                        <input type="text" 
                               name="address" 
                               id="address" 
                               value="{{ old('address') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror"
                               placeholder="1 Place de la Mairie">
                        @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Code Postal
                        </label>
                        <input type="text" 
                               name="postal_code" 
                               id="postal_code" 
                               value="{{ old('postal_code') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('postal_code') border-red-500 @enderror"
                               placeholder="75001">
                        @error('postal_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                            Ville
                        </label>
                        <input type="text" 
                               name="city" 
                               id="city" 
                               value="{{ old('city') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('city') border-red-500 @enderror"
                               placeholder="Paris">
                        @error('city')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Abonnement -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-tags text-purple-600 mr-2"></i>
                    Abonnement
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($plans as $plan)
                    <label class="relative cursor-pointer">
                        <input type="radio" 
                               name="subscription_plan" 
                               value="{{ $plan->slug }}" 
                               {{ old('subscription_plan') === $plan->slug ? 'checked' : '' }}
                               {{ $loop->first && !old('subscription_plan') ? 'checked' : '' }}
                               class="peer sr-only">
                        <div class="border-2 border-gray-300 rounded-lg p-4 peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-300 transition-all">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-bold text-lg">{{ $plan->name }}</h3>
                                <i class="fas fa-check-circle text-blue-500 hidden peer-checked:block"></i>
                            </div>
                            <p class="text-2xl font-bold text-gray-900 mb-2">
                                {{ number_format($plan->price, 0, ',', ' ') }}€
                                <span class="text-sm text-gray-500 font-normal">/mois</span>
                            </p>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li><i class="fas fa-check text-green-500 mr-1"></i> {{ $plan->max_children }} enfants max</li>
                                @foreach($plan->modules as $module)
                                <li><i class="fas fa-check text-green-500 mr-1"></i> {{ ucfirst($module) }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('subscription_plan')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="mt-4">
                    <label for="trial_days" class="block text-sm font-medium text-gray-700 mb-2">
                        Période d'Essai (jours)
                    </label>
                    <input type="number" 
                           name="trial_days" 
                           id="trial_days" 
                           value="{{ old('trial_days', 30) }}"
                           min="0"
                           max="90"
                           class="w-full md:w-48 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('trial_days') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">0 = pas d'essai, max 90 jours</p>
                    @error('trial_days')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Administrateur -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-user-shield text-green-600 mr-2"></i>
                    Compte Administrateur
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="admin_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom de l'Admin <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="admin_name" 
                               id="admin_name" 
                               value="{{ old('admin_name') }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('admin_name') border-red-500 @enderror"
                               placeholder="Jean Dupont">
                        @error('admin_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="admin_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email de l'Admin <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               name="admin_email" 
                               id="admin_email" 
                               value="{{ old('admin_email') }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('admin_email') border-red-500 @enderror"
                               placeholder="admin@mairie.fr">
                        @error('admin_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="admin_login" class="block text-sm font-medium text-gray-700 mb-2">
                            Identifiant de Connexion
                        </label>
                        <input type="text" 
                               name="admin_login" 
                               id="admin_login" 
                               value="{{ old('admin_login') }}"
                               pattern="[a-zA-Z0-9_-]+"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('admin_login') border-red-500 @enderror"
                               placeholder="jdupont">
                        <p class="mt-1 text-xs text-gray-500">Optionnel. Permet de se connecter avec un identifiant court</p>
                        @error('admin_login')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="admin_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Mot de Passe <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="admin_password" 
                               id="admin_password" 
                               required
                               minlength="8"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('admin_password') border-red-500 @enderror"
                               placeholder="••••••••">
                        <p class="mt-1 text-xs text-gray-500">Minimum 8 caractères</p>
                        @error('admin_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="admin_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirmer le Mot de Passe <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="admin_password_confirmation" 
                               id="admin_password_confirmation" 
                               required
                               minlength="8"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="••••••••">
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                    <i class="fas fa-times mr-2"></i>
                    Annuler
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg hover:shadow-xl">
                    <i class="fas fa-check mr-2"></i>
                    Créer le Tenant
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-génération du slug à partir du nom
document.getElementById('name').addEventListener('input', function(e) {
    const slug = e.target.value
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
    document.getElementById('slug').value = slug;
});
</script>
@endsection
