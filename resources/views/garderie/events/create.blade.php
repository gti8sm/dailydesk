@extends('layouts.app')

@section('title', 'Garderie - Signaler un événement')

@section('content')
<div class="max-w-3xl mx-auto px-4">
    <div class="mb-6">
        <a href="{{ route('garderie.events.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Retour aux événements
        </a>
    </div>

    <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <h1 class="text-2xl font-bold text-white">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Signaler un événement
            </h1>
            <p class="text-blue-100 text-sm mt-1">Garderie - Incident, accident ou comportement</p>
        </div>

        <form action="{{ route('garderie.events.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Enfant concerné <span class="text-red-500">*</span></label>
                <select name="child_id" required class="w-full text-lg px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Sélectionner un enfant</option>
                    @foreach($children as $child)
                        <option value="{{ $child->id }}" {{ (int)($preselectedChild ?? 0) === $child->id ? 'selected' : '' }}>
                            {{ $child->first_name }} {{ $child->last_name }} - {{ $child->class }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Date <span class="text-red-500">*</span></label>
                    <input type="date" name="event_date" value="{{ date('Y-m-d') }}" required
                           class="w-full text-lg px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Heure</label>
                    <input type="time" name="event_time" value="{{ date('H:i') }}"
                           class="w-full text-lg px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Type d'événement <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach(['incident' => 'Incident', 'accident' => 'Accident', 'behavior' => 'Comportement', 'medical' => 'Médical', 'other' => 'Autre'] as $value => $label)
                    <label class="flex items-center gap-2 p-3 rounded-lg border-2 border-gray-200 hover:border-blue-300 cursor-pointer transition-all">
                        <input type="radio" name="event_type" value="{{ $value }}" required class="text-blue-600">
                        <span class="text-sm font-medium">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Gravité <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="flex items-center justify-center gap-2 p-3 rounded-lg border-2 border-gray-200 hover:border-green-300 cursor-pointer transition-all">
                        <input type="radio" name="severity" value="low" required class="text-green-600">
                        <span class="text-sm font-medium text-green-700">Faible</span>
                    </label>
                    <label class="flex items-center justify-center gap-2 p-3 rounded-lg border-2 border-gray-200 hover:border-yellow-300 cursor-pointer transition-all">
                        <input type="radio" name="severity" value="medium" class="text-yellow-600">
                        <span class="text-sm font-medium text-yellow-700">Moyenne</span>
                    </label>
                    <label class="flex items-center justify-center gap-2 p-3 rounded-lg border-2 border-gray-200 hover:border-red-300 cursor-pointer transition-all">
                        <input type="radio" name="severity" value="high" class="text-red-600">
                        <span class="text-sm font-medium text-red-700">Élevée</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Titre <span class="text-red-500">*</span></label>
                <input type="text" name="title" required placeholder="Ex: Chute dans la cour"
                       class="w-full text-lg px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Description <span class="text-red-500">*</span></label>
                <textarea name="description" required rows="5"
                          placeholder="Décrivez l'événement en détail..."
                          class="w-full text-lg px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-blue-500 focus:ring-blue-500"></textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-blue-600 to-blue-500 text-white py-4 rounded-xl font-bold text-lg shadow-lg active:scale-95 transition-all touch-manipulation">
                    <i class="fas fa-save mr-2"></i> Enregistrer
                </button>
                <a href="{{ route('garderie.events.index') }}"
                   class="px-6 py-4 bg-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-300 transition-all">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
