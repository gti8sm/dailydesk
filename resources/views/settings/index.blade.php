@extends('layouts.app')

@section('title', 'Paramètres')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-cog text-indigo-600 mr-2"></i>
            Paramètres de l'Application
        </h1>
        <p class="mt-1 text-sm text-gray-600">
            Configuration des horaires, notifications et services
        </p>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <div class="flex">
            <i class="fas fa-check-circle text-green-400 mr-3 mt-0.5"></i>
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Paramètres Généraux (Super Admin uniquement) -->
        @if(auth()->user()->hasRole('super_admin'))
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border-2 border-purple-200">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white">
                <h2 class="text-xl font-bold flex items-center">
                    <i class="fas fa-crown mr-3 text-2xl"></i>
                    Paramètres Généraux
                </h2>
                <p class="text-sm text-purple-100 mt-1">
                    <i class="fas fa-lock mr-1"></i>
                    Réservé aux Super Administrateurs
                </p>
            </div>
            
            <div class="p-6">
                <div>
                    <label for="app_name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tag text-purple-600 mr-2"></i>
                        Nom de l'Application
                    </label>
                    <input type="text" name="app_name" id="app_name"
                           value="{{ $settings['general']['app_name'] ?? 'DailyDesk' }}"
                           class="w-full text-lg px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                           placeholder="Nom de votre application"
                           maxlength="50">
                    <p class="mt-2 text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Ce nom apparaîtra dans toute l'application (menu, login, emails, etc.)
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Horaires Garderie -->
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-500 text-white">
                <h2 class="text-xl font-bold flex items-center">
                    <i class="fas fa-clock mr-3 text-2xl"></i>
                    Horaires de Garderie
                </h2>
                <p class="text-sm text-blue-100 mt-1">
                    Définir les plages horaires pour l'horodatage automatique
                </p>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Garderie du Matin -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-sun text-yellow-500 mr-2"></i>
                        Garderie du Matin
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="garderie_morning_start" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-play text-green-600 mr-1"></i>
                                Heure de début
                            </label>
                            <input type="time" name="garderie_morning_start" id="garderie_morning_start"
                                   value="{{ $settings['garderie']['garderie_morning_start'] ?? '07:00' }}"
                                   class="w-full text-lg px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">
                                À partir de cette heure, les arrivées seront enregistrées
                            </p>
                        </div>
                        <div>
                            <label for="garderie_morning_end" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-stop text-red-600 mr-1"></i>
                                Heure de fin
                            </label>
                            <input type="time" name="garderie_morning_end" id="garderie_morning_end"
                                   value="{{ $settings['garderie']['garderie_morning_end'] ?? '08:30' }}"
                                   class="w-full text-lg px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">
                                Fin de la garderie du matin (début de l'école)
                            </p>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-moon text-indigo-500 mr-2"></i>
                        Garderie du Soir
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="garderie_evening_start" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-play text-green-600 mr-1"></i>
                                Heure de début
                            </label>
                            <input type="time" name="garderie_evening_start" id="garderie_evening_start"
                                   value="{{ $settings['garderie']['garderie_evening_start'] ?? '16:30' }}"
                                   class="w-full text-lg px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">
                                Début de la garderie du soir (fin de l'école)
                            </p>
                        </div>
                        <div>
                            <label for="garderie_evening_end" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-stop text-red-600 mr-1"></i>
                                Heure de fin
                            </label>
                            <input type="time" name="garderie_evening_end" id="garderie_evening_end"
                                   value="{{ $settings['garderie']['garderie_evening_end'] ?? '18:30' }}"
                                   class="w-full text-lg px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">
                                Heure limite de départ des enfants
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                    <div class="flex">
                        <i class="fas fa-info-circle text-blue-400 mr-3 mt-0.5"></i>
                        <div class="text-sm text-blue-700">
                            <p class="font-medium">Fonctionnement de l'horodatage automatique :</p>
                            <ul class="mt-2 list-disc list-inside space-y-1">
                                <li><strong>Matin :</strong> Un clic sur l'enfant enregistre l'arrivée avec l'heure actuelle</li>
                                <li><strong>Soir :</strong> Un clic sur l'enfant enregistre le départ avec l'heure actuelle</li>
                                <li>La durée est calculée automatiquement entre arrivée et départ</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cantine -->
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-500 text-white">
                <h2 class="text-xl font-bold flex items-center">
                    <i class="fas fa-utensils mr-3 text-2xl"></i>
                    Cantine
                </h2>
                <p class="text-sm text-green-100 mt-1">
                    Configuration des repas et goûter
                </p>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex items-center">
                        <i class="fas fa-cookie-bite text-orange-600 text-xl mr-4"></i>
                        <div>
                            <p class="font-medium text-gray-900">Activer le goûter</p>
                            <p class="text-sm text-gray-500">Permet d'enregistrer les présences au goûter en plus du déjeuner</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="cantine_enable_snack" value="1"
                               {{ ($settings['cantine']['cantine_enable_snack'] ?? false) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-600"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Configuration SMTP -->
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-600 to-purple-500 text-white">
                <h2 class="text-xl font-bold flex items-center">
                    <i class="fas fa-envelope mr-3 text-2xl"></i>
                    Configuration Email (SMTP)
                </h2>
                <p class="text-sm text-purple-100 mt-1">
                    Paramètres pour l'envoi automatique d'emails
                </p>
            </div>
            
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="smtp_host" class="block text-sm font-medium text-gray-700 mb-2">
                            Serveur SMTP
                        </label>
                        <input type="text" name="smtp_host" id="smtp_host"
                               value="{{ $settings['smtp']['smtp_host'] ?? '' }}"
                               placeholder="smtp.example.com"
                               class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <label for="smtp_port" class="block text-sm font-medium text-gray-700 mb-2">
                            Port
                        </label>
                        <input type="number" name="smtp_port" id="smtp_port"
                               value="{{ $settings['smtp']['smtp_port'] ?? '587' }}"
                               placeholder="587"
                               class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="smtp_username" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom d'utilisateur
                        </label>
                        <input type="text" name="smtp_username" id="smtp_username"
                               value="{{ $settings['smtp']['smtp_username'] ?? '' }}"
                               class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <label for="smtp_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Mot de passe
                        </label>
                        <input type="password" name="smtp_password" id="smtp_password"
                               value="{{ $settings['smtp']['smtp_password'] ?? '' }}"
                               class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="smtp_encryption" class="block text-sm font-medium text-gray-700 mb-2">
                            Chiffrement
                        </label>
                        <select name="smtp_encryption" id="smtp_encryption"
                                class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            <option value="tls" {{ ($settings['smtp']['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ ($settings['smtp']['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                        </select>
                    </div>
                </div>

                <div class="border-t pt-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Adresse d'expédition</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="smtp_from_address" class="block text-sm font-medium text-gray-700 mb-2">
                                Email
                            </label>
                            <input type="email" name="smtp_from_address" id="smtp_from_address"
                                   value="{{ $settings['smtp']['smtp_from_address'] ?? '' }}"
                                   placeholder="noreply@example.com"
                                   class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        </div>
                        <div>
                            <label for="smtp_from_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nom
                            </label>
                            <input type="text" name="smtp_from_name" id="smtp_from_name"
                                   value="{{ $settings['smtp']['smtp_from_name'] ?? 'DailyDesk' }}"
                                   placeholder="DailyDesk"
                                   class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-500 text-white">
                <h2 class="text-xl font-bold flex items-center">
                    <i class="fas fa-bell mr-3 text-2xl"></i>
                    Notifications Automatiques
                </h2>
                <p class="text-sm text-green-100 mt-1">
                    Choisir les événements qui déclenchent un email
                </p>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex items-center">
                        <i class="fas fa-sign-in-alt text-green-600 text-xl mr-4"></i>
                        <div>
                            <p class="font-medium text-gray-900">Arrivée enregistrée</p>
                            <p class="text-sm text-gray-500">Notifier les parents quand l'enfant arrive</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="notify_arrival" value="1"
                               {{ ($settings['notifications']['notify_arrival'] ?? false) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex items-center">
                        <i class="fas fa-sign-out-alt text-orange-600 text-xl mr-4"></i>
                        <div>
                            <p class="font-medium text-gray-900">Départ enregistré</p>
                            <p class="text-sm text-gray-500">Notifier les parents quand l'enfant part</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="notify_departure" value="1"
                               {{ ($settings['notifications']['notify_departure'] ?? false) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex items-center">
                        <i class="fas fa-user-slash text-red-600 text-xl mr-4"></i>
                        <div>
                            <p class="font-medium text-gray-900">Absence détectée</p>
                            <p class="text-sm text-gray-500">Notifier si l'enfant n'est pas arrivé à l'heure prévue</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="notify_absence" value="1"
                               {{ ($settings['notifications']['notify_absence'] ?? false) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mr-4"></i>
                        <div>
                            <p class="font-medium text-gray-900">Événement signalé</p>
                            <p class="text-sm text-gray-500">Notifier immédiatement en cas d'incident ou allergie</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="notify_event" value="1"
                               {{ ($settings['notifications']['notify_event'] ?? true) ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-600"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="flex justify-end gap-4 pt-4">
            <a href="{{ route('dashboard') }}" 
               class="px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">
                <i class="fas fa-times mr-2"></i>
                Annuler
            </a>
            <button type="submit" 
                    class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white rounded-lg hover:from-indigo-700 hover:to-indigo-600 font-bold shadow-lg transition">
                <i class="fas fa-save mr-2"></i>
                Enregistrer les Paramètres
            </button>
        </div>
    </form>
</div>
@endsection
