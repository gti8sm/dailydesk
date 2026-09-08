@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">
        <i class="fas fa-user-circle text-blue-600 mr-2"></i>
        Mon Profil
    </h1>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded">
        <p class="text-sm text-red-700">{{ session('error') }}</p>
    </div>
    @endif

    <div class="bg-white shadow rounded-lg p-6">
        <div class="space-y-6">
            <div class="flex items-center space-x-4">
                <div class="h-20 w-20 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-3xl text-blue-600"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ Auth::user()->name }}</h2>
                    <p class="text-gray-600">{{ Auth::user()->email }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        Rôle: <span class="font-medium">{{ Auth::user()->roles->pluck('name')->join(', ') }}</span>
                    </p>
                </div>
            </div>

            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informations du compte</h3>
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dernière connexion</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ Auth::user()->last_login_at ? Auth::user()->last_login_at->format('d/m/Y H:i') : 'Jamais' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Code PIN configuré</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ Auth::user()->confidential_code ? 'Oui' : 'Non' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Whitelist IP activée</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ Auth::user()->ip_whitelist_enabled ? 'Oui' : 'Non' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Compte actif</dt>
                        <dd class="mt-1">
                            @if(Auth::user()->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i> Actif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times mr-1"></i> Inactif
                                </span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Changement de mot de passe -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-key text-gray-600 mr-2"></i>Modifier le mot de passe
                </h3>
                <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe actuel</label>
                        <input type="password" name="current_password" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        @error('current_password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe</label>
                            <input type="password" name="password" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer</label>
                            <input type="password" name="password_confirmation" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                            <i class="fas fa-save mr-1"></i> Modifier
                        </button>
                    </div>
                </form>
            </div>

            <!-- Code PIN -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-lock text-gray-600 mr-2"></i>Configurer le code PIN
                </h3>
                <p class="text-sm text-gray-500 mb-3">Le code PIN (4 à 6 chiffres) permet une connexion rapide sans saisir votre mot de passe complet.</p>
                <form action="{{ route('profile.pin') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    @if(Auth::user()->confidential_code)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code PIN actuel</label>
                        <input type="password" name="current_pin" inputmode="numeric" pattern="[0-9]*"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Votre code PIN actuel">
                        @error('current_pin')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nouveau code PIN</label>
                        <input type="password" name="pin" required inputmode="numeric" pattern="[0-9]{4,6}" maxlength="6"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                               placeholder="4 à 6 chiffres">
                        @error('pin')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                            <i class="fas fa-save mr-1"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>

            <!-- Whitelist IP -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-shield-alt text-gray-600 mr-2"></i>Gérer la whitelist IP
                </h3>
                <p class="text-sm text-gray-500 mb-3">Restreignez les connexions à votre compte à certaines adresses IP. Laissez désactivé pour autoriser toutes les IP.</p>
                <form action="{{ route('profile.whitelist') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="flex items-center">
                        <input type="checkbox" name="ip_whitelist_enabled" id="ip_whitelist_enabled"
                               {{ Auth::user()->ip_whitelist_enabled ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="ip_whitelist_enabled" class="ml-2 text-sm text-gray-700">Activer la whitelist IP</label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresses IP autorisées (une par ligne)</label>
                        <textarea name="whitelisted_ips" rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                                  placeholder="192.168.1.1&#10;10.0.0.5">{{ old('whitelisted_ips', Auth::user()->ip_whitelist_enabled ? implode("\n", Auth::user()->whitelisted_ips ?? []) : '') }}</textarea>
                        @error('whitelisted_ips')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                            <i class="fas fa-save mr-1"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
