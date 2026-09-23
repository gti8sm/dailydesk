@extends('layouts.app')

@section('title', 'Nouvelle intercommunalité')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('central.intercommunalities.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Retour aux intercommunalités
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-500 to-indigo-600">
            <h1 class="text-2xl font-bold text-white">
                <i class="fas fa-plus-circle mr-2"></i> Nouvelle intercommunalité
            </h1>
        </div>

        <form action="{{ route('central.intercommunalities.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="Ex: CC Vallée du Rhône">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="communaute_communes" @selected(old('type') === 'communaute_communes')>Communauté de communes</option>
                        <option value="communaute_agglomeration" @selected(old('type') === 'communaute_agglomeration')>Communauté d'agglomération</option>
                        <option value="syndicat" @selected(old('type') === 'syndicat')>Syndicat intercommunal</option>
                        <option value="epci" @selected(old('type') === 'epci')>EPCI</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SIREN</label>
                    <input type="text" name="siren" value="{{ old('siren') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="123456789">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                    <input type="text" name="address" value="{{ old('address') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CP</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                        <input type="text" name="city" value="{{ old('city') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Communes membres <span class="text-gray-400 font-normal">(sélectionnez les tenants à rattacher)</span>
                </label>
                <div class="max-h-60 overflow-y-auto border border-gray-300 rounded-lg p-3 space-y-2">
                    @foreach($tenants as $tenant)
                    <label class="flex items-center gap-2 hover:bg-indigo-50 px-2 py-1 rounded">
                        <input type="checkbox" name="tenant_ids[]" value="{{ $tenant->id }}"
                               {{ in_array($tenant->id, old('tenant_ids', [])) ? 'checked' : '' }}
                               class="w-4 h-4 text-indigo-600 rounded focus:ring-2 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">{{ $tenant->name }}</span>
                        @if($tenant->city)<span class="text-xs text-gray-400">— {{ $tenant->city }}</span>@endif
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <a href="{{ route('central.intercommunalities.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                    <i class="fas fa-times mr-2"></i> Annuler
                </a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg">
                    <i class="fas fa-check mr-2"></i> Créer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
