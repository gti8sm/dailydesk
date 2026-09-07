@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">
        <i class="fas fa-user-circle text-blue-600 mr-2"></i>
        Mon Profil
    </h1>

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

            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                <div class="space-y-3">
                    <button class="w-full text-left px-4 py-3 border border-gray-300 rounded-md hover:bg-gray-50">
                        <i class="fas fa-key text-gray-600 mr-2"></i>
                        Modifier le mot de passe
                    </button>
                    <button class="w-full text-left px-4 py-3 border border-gray-300 rounded-md hover:bg-gray-50">
                        <i class="fas fa-lock text-gray-600 mr-2"></i>
                        Configurer le code PIN
                    </button>
                    <button class="w-full text-left px-4 py-3 border border-gray-300 rounded-md hover:bg-gray-50">
                        <i class="fas fa-shield-alt text-gray-600 mr-2"></i>
                        Gérer la whitelist IP
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
