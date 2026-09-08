@extends('layouts.app')

@section('title', 'Cantine - Détail événement')

@section('content')
<div class="max-w-3xl mx-auto px-4">
    <div class="mb-6">
        <a href="{{ route('cantine.events.index') }}" class="text-green-600 hover:text-green-800 text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Retour aux événements
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-300 rounded-lg text-green-700">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
            <h1 class="text-2xl font-bold text-white">{{ $event->title }}</h1>
            <p class="text-green-100 text-sm mt-1">
                {{ $event->event_date->format('d/m/Y') }} @if($event->event_time) à {{ $event->event_time }} @endif
            </p>
        </div>

        <div class="p-6 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 uppercase font-bold mb-1">Enfant</p>
                    <p class="text-lg font-bold text-gray-900">{{ $event->child->full_name }}</p>
                    <p class="text-sm text-gray-600">{{ $event->child->class }}</p>
                    <p class="text-sm text-gray-500">{{ $event->child->family->family_name }}</p>
                    @if($event->child->allergies)
                    <div class="mt-2 p-2 bg-red-50 rounded text-xs text-red-700">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Allergies: {{ $event->child->allergies }}
                    </div>
                    @endif
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 uppercase font-bold mb-1">Type</p>
                    @php
                        $typeLabels = ['allergy' => 'Allergie', 'refusal' => 'Refus', 'incident' => 'Incident', 'other' => 'Autre'];
                    @endphp
                    <p class="text-lg font-bold text-gray-900">{{ $typeLabels[$event->event_type] ?? 'Autre' }}</p>
                </div>
            </div>

            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-500 uppercase font-bold mb-2">Description</p>
                <p class="text-gray-700 whitespace-pre-line">{{ $event->description }}</p>
            </div>

            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-500 uppercase font-bold mb-1">Signalé par</p>
                <p class="text-sm text-gray-700">{{ $event->createdBy->name }}</p>
                <p class="text-xs text-gray-400">{{ $event->created_at->format('d/m/Y à H:i') }}</p>
            </div>

            <div class="flex items-center justify-between p-4 rounded-lg {{ $event->parents_notified ? 'bg-green-50' : 'bg-orange-50' }}">
                <div>
                    <p class="font-bold text-sm {{ $event->parents_notified ? 'text-green-700' : 'text-orange-700' }}">
                        @if($event->parents_notified)
                            <i class="fas fa-check-circle mr-2"></i> Parents notifiés
                            <span class="text-xs font-normal text-gray-500 ml-2">{{ $event->notified_at?->format('d/m/Y à H:i') }}</span>
                        @else
                            <i class="fas fa-clock mr-2"></i> Parents non notifiés
                        @endif
                    </p>
                </div>
                @if(!$event->parents_notified && auth()->user()->can('notify_event_parents'))
                <form action="{{ route('cantine.events.notify', $event) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium text-sm transition-colors"
                            onclick="return confirm('Notifier les parents par email ?')">
                        <i class="fas fa-envelope mr-1"></i> Notifier les parents
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
