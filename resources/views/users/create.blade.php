@extends('layouts.app')

@section('title', 'Nouvel Utilisateur')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('users.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour à la liste
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600 border-b border-blue-700">
            <h2 class="text-xl font-semibold text-white flex items-center">
                <i class="fas fa-user-plus mr-2"></i>
                Nouvel Utilisateur
            </h2>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="p-6">
            @csrf

            <div class="space-y-6">
                <!-- Nom -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom complet <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                           required>
                    @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Login -->
                <div>
                    <label for="login" class="block text-sm font-medium text-gray-700 mb-2">
                        Identifiant de connexion
                    </label>
                    <input type="text" 
                           id="login" 
                           name="login" 
                           value="{{ old('login') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('login') border-red-500 @enderror"
                           placeholder="ex: jdupont">
                    <p class="mt-1 text-xs text-gray-500">Optionnel. Permet de se connecter avec un identifiant au lieu de l'email. Lettres, chiffres, tirets et underscores uniquement.</p>
                    @error('login')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mot de passe -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Mot de passe <span class="text-red-500">*</span>
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                           required>
                    <p class="mt-1 text-xs text-gray-500">Minimum 8 caractères</p>
                    @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirmation mot de passe -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirmer le mot de passe <span class="text-red-500">*</span>
                    </label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                </div>

                <!-- Rôles -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Rôles <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-3">
                        @foreach($roles as $role)
                        <label class="flex items-start p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                            <input type="checkbox" 
                                   name="roles[]" 
                                   value="{{ $role->name }}"
                                   {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}
                                   class="mt-1 text-blue-600 focus:ring-blue-500 rounded">
                            <div class="ml-3">
                                <span class="font-medium text-gray-900">{{ ucfirst($role->name) }}</span>
                                <p class="text-sm text-gray-500">
                                    @if($role->name === 'admin')
                                    Accès complet à toutes les fonctionnalités
                                    @elseif($role->name === 'alsh')
                                    Gestion de la garderie (ALSH)
                                    @elseif($role->name === 'cantine')
                                    Gestion de la cantine
                                    @endif
                                </p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('roles')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Statut actif -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="is_active" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="text-blue-600 focus:ring-blue-500 rounded">
                    <label for="is_active" class="ml-2 text-sm text-gray-700">
                        Compte actif
                    </label>
                </div>
            </div>

            <div class="flex gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('users.index') }}" 
                   class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg font-medium transition-colors text-center">
                    Annuler
                </a>
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Créer l'utilisateur
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
