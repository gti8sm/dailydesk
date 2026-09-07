@extends('layouts.app')

@section('title', 'Paramètres globaux des modules')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-cog mr-2 text-gray-600"></i>Paramètres globaux des modules
            </h1>
            <p class="mt-1 text-sm text-gray-600">Valeurs par défaut appliquées à tous les tenants</p>
        </div>
        <a href="{{ route('central.modules.overview') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
            <i class="fas fa-arrow-left mr-2"></i>Retour
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <form action="{{ route('central.modules.settings.update') }}" method="POST">
        @csrf

        @foreach($settingsGroups as $group => $settings)
        <div class="bg-white shadow-lg rounded-xl p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 capitalize">
                @if($group === 'garderie')
                <i class="fas fa-child mr-2 text-blue-600"></i>Garderie
                @elseif($group === 'cantine')
                <i class="fas fa-utensils mr-2 text-orange-600"></i>Cantine
                @elseif($group === 'notifications')
                <i class="fas fa-bell mr-2 text-purple-600"></i>Notifications
                @else
                <i class="fas fa-cog mr-2 text-gray-600"></i>{{ ucfirst($group) }}
                @endif
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($settings as $key => $config)
                <div class="space-y-1">
                    <label for="{{ $key }}" class="block text-sm font-medium text-gray-700">{{ $config['label'] }}</label>

                    @if($config['type'] === 'time')
                    <input type="time" name="{{ $key }}" id="{{ $key }}"
                           value="{{ $currentValues[$key] ?? $config['default'] }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500">

                    @elseif($config['type'] === 'select')
                    <select name="{{ $key }}" id="{{ $key }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500">
                        @foreach($config['options'] ?? [] as $optValue => $optLabel)
                        <option value="{{ $optValue }}" {{ ($currentValues[$key] ?? $config['default']) === $optValue ? 'selected' : '' }}>{{ $optLabel }}</option>
                        @endforeach
                    </select>

                    @elseif($config['type'] === 'boolean')
                    <label class="flex items-center mt-2">
                        <input type="checkbox" name="{{ $key }}" id="{{ $key }}"
                               {{ ($currentValues[$key] ?? $config['default']) === '1' ? 'checked' : '' }}
                               class="h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Activer</span>
                    </label>

                    @else
                    <input type="text" name="{{ $key }}" id="{{ $key }}"
                           value="{{ $currentValues[$key] ?? $config['default'] }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500">
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                <i class="fas fa-save mr-2"></i>Enregistrer les paramètres
            </button>
        </div>
    </form>
</div>
@endsection
