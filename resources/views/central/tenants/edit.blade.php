@extends('layouts.app')

@section('title', 'Modifier le Tenant')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('central.tenants.show', $tenant) }}" class="text-blue-600 hover:text-blue-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour aux détails
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-500 to-green-600">
            <h1 class="text-2xl font-bold text-white">
                <i class="fas fa-edit mr-2"></i>
                Modifier le Tenant
            </h1>
            <p class="text-green-100 mt-1">{{ $tenant->name }}</p>
        </div>

        <form action="{{ route('central.tenants.update', $tenant) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

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
                               value="{{ old('name', $tenant->name) }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                            Slug (URL)
                        </label>
                        <input type="text" 
                               name="slug" 
                               id="slug" 
                               value="{{ $tenant->slug }}"
                               disabled
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed">
                        <p class="mt-1 text-xs text-gray-500">Le slug ne peut pas être modifié</p>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email de Contact <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email', $tenant->email) }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
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
                               value="{{ old('phone', $tenant->phone) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror">
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
                               value="{{ old('address', $tenant->address) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror">
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
                               value="{{ old('postal_code', $tenant->postal_code) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('postal_code') border-red-500 @enderror">
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
                               value="{{ old('city', $tenant->city) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('city') border-red-500 @enderror">
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
                               {{ old('subscription_plan', $tenant->subscription_plan) === $plan->slug ? 'checked' : '' }}
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
            </div>

            <!-- Administrateur -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-user-shield text-green-600 mr-2"></i>
                    Administrateur Principal
                </h2>
                
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded mb-4">
                    <div class="flex">
                        <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-1">Informations de l'administrateur</p>
                            <p><strong>Nom :</strong> {{ $admin->name ?? 'N/A' }}</p>
                            <p><strong>Email :</strong> {{ $admin->email ?? 'N/A' }}</p>
                            @if($admin->login ?? null)
                            <p><strong>Identifiant actuel :</strong> {{ $admin->login }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="max-w-md">
                    <label for="admin_login" class="block text-sm font-medium text-gray-700 mb-2">
                        Identifiant de Connexion
                    </label>
                    <input type="text" 
                           name="admin_login" 
                           id="admin_login" 
                           value="{{ old('admin_login', $admin->login ?? '') }}"
                           pattern="[a-zA-Z0-9_-]+"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('admin_login') border-red-500 @enderror"
                           placeholder="jdupont">
                    <p class="mt-1 text-xs text-gray-500">Permet à l'admin de se connecter avec un identifiant court au lieu de l'email</p>
                    @error('admin_login')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Statut -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-toggle-on text-green-600 mr-2"></i>
                    Statut
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="relative cursor-pointer">
                        <input type="radio" 
                               name="status" 
                               value="active" 
                               {{ old('status', $tenant->status) === 'active' ? 'checked' : '' }}
                               class="peer sr-only">
                        <div class="border-2 border-gray-300 rounded-lg p-4 peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-green-300 transition-all">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-bold text-lg text-green-600">Actif</h3>
                                    <p class="text-sm text-gray-600">Tenant opérationnel</p>
                                </div>
                                <i class="fas fa-check-circle text-green-500 text-2xl hidden peer-checked:block"></i>
                            </div>
                        </div>
                    </label>

                    <label class="relative cursor-pointer">
                        <input type="radio" 
                               name="status" 
                               value="suspended" 
                               {{ old('status', $tenant->status) === 'suspended' ? 'checked' : '' }}
                               class="peer sr-only">
                        <div class="border-2 border-gray-300 rounded-lg p-4 peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:border-orange-300 transition-all">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-bold text-lg text-orange-600">Suspendu</h3>
                                    <p class="text-sm text-gray-600">Accès bloqué</p>
                                </div>
                                <i class="fas fa-pause-circle text-orange-500 text-2xl hidden peer-checked:block"></i>
                            </div>
                        </div>
                    </label>

                    <label class="relative cursor-pointer">
                        <input type="radio" 
                               name="status" 
                               value="cancelled" 
                               {{ old('status', $tenant->status) === 'cancelled' ? 'checked' : '' }}
                               class="peer sr-only">
                        <div class="border-2 border-gray-300 rounded-lg p-4 peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-red-300 transition-all">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-bold text-lg text-red-600">Annulé</h3>
                                    <p class="text-sm text-gray-600">Abonnement terminé</p>
                                </div>
                                <i class="fas fa-times-circle text-red-500 text-2xl hidden peer-checked:block"></i>
                            </div>
                        </div>
                    </label>
                </div>
                @error('status')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('central.tenants.show', $tenant) }}" class="text-gray-600 hover:text-gray-800 font-medium">
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
@endsection
