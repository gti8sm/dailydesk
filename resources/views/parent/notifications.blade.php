@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('parent.dashboard') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Retour
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-3">
            <i class="fas fa-bell text-blue-600 mr-2"></i>
            Préférences de notification
        </h1>
        <p class="mt-1 text-sm text-gray-600">Choisissez les notifications que vous souhaitez recevoir par email</p>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <form action="{{ route('parent.notifications') }}" method="POST" class="bg-white shadow-lg rounded-xl p-6 space-y-4">
        @csrf
        @method('PUT')

        <!-- Signalements (verrouillé) -->
        <div class="bg-gray-100 rounded-lg p-4 flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-red-500"></i>
                    <span class="font-medium text-gray-900">Signalements (incidents, accidents)</span>
                    <i class="fas fa-lock text-gray-400 text-xs" title="Verrouillé"></i>
                </div>
                <p class="text-xs text-gray-500 mt-1">Notification obligatoire pour la traçabilité</p>
            </div>
            <div class="flex items-center">
                <input type="checkbox" checked disabled class="h-5 w-5 text-gray-400 rounded border-gray-300">
            </div>
        </div>

        <!-- Résumé des présences -->
        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
            <div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-clipboard-check text-blue-500"></i>
                    <span class="font-medium text-gray-900">Résumé des présences</span>
                </div>
                <p class="text-xs text-gray-500 mt-1">Recevoir un récapitulatif périodique des présences</p>
            </div>
            <input type="checkbox" name="email_presence_summary" value="1"
                   {{ ($prefs['email_presence_summary'] ?? false) ? 'checked' : '' }}
                   class="h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
        </div>

        <!-- Rappel d'absence -->
        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
            <div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-calendar-times text-orange-500"></i>
                    <span class="font-medium text-gray-900">Rappel d'absence</span>
                </div>
                <p class="text-xs text-gray-500 mt-1">Recevoir un email de rappel pour signaler une absence</p>
            </div>
            <input type="checkbox" name="email_absence_reminder" value="1"
                   {{ ($prefs['email_absence_reminder'] ?? false) ? 'checked' : '' }}
                   class="h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                <i class="fas fa-save mr-2"></i>Enregistrer
            </button>
        </div>
    </form>
</div>
@endsection
