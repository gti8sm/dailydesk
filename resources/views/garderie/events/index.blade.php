@extends('layouts.app')

@section('title', 'Garderie - Événements')

@section('content')
<div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
    <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                <i class="fas fa-exclamation-triangle text-blue-600 mr-2"></i>
                Événements - Garderie
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-gray-600">Incidents, accidents et comportements signalés</p>
        </div>
        <a href="{{ route('garderie.events.create') }}"
           class="px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors flex items-center shadow-md">
            <i class="fas fa-plus mr-2"></i> Signaler un événement
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-300 rounded-lg text-green-700">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enfant</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gravité</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($events as $event)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            {{ $event->event_date->format('d/m/Y') }}
                            @if($event->event_time)<br><span class="text-xs text-gray-400">{{ $event->event_time }}</span>@endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="font-medium text-gray-900">{{ $event->child->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $event->child->class }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @php
                                $typeLabels = ['incident' => ['Incident', 'red'], 'accident' => ['Accident', 'red'], 'behavior' => ['Comportement', 'yellow'], 'medical' => ['Médical', 'blue'], 'other' => ['Autre', 'gray']];
                                [$label, $color] = $typeLabels[$event->event_type] ?? ['Autre', 'gray'];
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800">{{ $label }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $event->title }}</td>
                        <td class="px-4 py-3 text-sm">
                            @php
                                $sevLabels = ['low' => ['Faible', 'green'], 'medium' => ['Moyenne', 'yellow'], 'high' => ['Élevée', 'red']];
                                [$sevLabel, $sevColor] = $sevLabels[$event->severity] ?? ['Faible', 'green'];
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-{{ $sevColor }}-100 text-{{ $sevColor }}-800">{{ $sevLabel }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($event->parents_notified)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i> Notifiés
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    <i class="fas fa-clock mr-1"></i> En attente
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right text-sm whitespace-nowrap">
                            <a href="{{ route('garderie.events.show', $event) }}" class="text-blue-600 hover:text-blue-800 font-medium">Voir</a>
                            @if(!$event->parents_notified)
                            <form action="{{ route('garderie.events.notify', $event) }}" method="POST" class="inline ml-2">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-800 font-medium"
                                        onclick="return confirm('Notifier les parents par email ?')">
                                    Notifier
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                            <p>Aucun événement signalé</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($events->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $events->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
