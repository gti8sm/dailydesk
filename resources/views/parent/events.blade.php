@extends('layouts.app')

@section('title', 'Signalements')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('parent.dashboard') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Retour
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-3">
            <i class="fas fa-exclamation-triangle text-orange-600 mr-2"></i>
            Signalements
        </h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Garderie -->
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <div class="px-5 py-3 bg-gradient-to-r from-blue-600 to-blue-500 text-white">
                <h3 class="font-bold"><i class="fas fa-child mr-2"></i>Garderie</h3>
            </div>
            <div class="divide-y divide-gray-100 max-h-[60vh] overflow-y-auto">
                @forelse($garderieEvents as $event)
                <div class="p-4">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-medium text-gray-900">{{ $event->child->full_name }}</span>
                        <span class="text-xs text-gray-400">{{ $event->event_date->format('d/m/Y') }}</span>
                    </div>
                    <p class="text-sm font-medium text-gray-700">{{ $event->title }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $event->description }}</p>
                    <div class="mt-2 flex items-center gap-2">
                        @if($event->severity ?? null)
                            <span class="text-xs px-2 py-0.5 rounded-full
                                {{ $event->severity === 'high' ? 'bg-red-100 text-red-700' : ($event->severity === 'medium' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600') }}">
                                {{ $event->severity }}
                            </span>
                        @endif
                        @if(!$event->parents_notified)
                            <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">Non notifié</span>
                        @else
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full"><i class="fas fa-check mr-1"></i>Notifié</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-check text-3xl text-green-300 mb-2"></i>
                    <p>Aucun signalement en garderie</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Cantine -->
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <div class="px-5 py-3 bg-gradient-to-r from-green-600 to-green-500 text-white">
                <h3 class="font-bold"><i class="fas fa-utensils mr-2"></i>Cantine</h3>
            </div>
            <div class="divide-y divide-gray-100 max-h-[60vh] overflow-y-auto">
                @forelse($cantineEvents as $event)
                <div class="p-4">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-medium text-gray-900">{{ $event->child->full_name }}</span>
                        <span class="text-xs text-gray-400">{{ $event->event_date->format('d/m/Y') }}</span>
                    </div>
                    <p class="text-sm font-medium text-gray-700">{{ $event->title }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $event->description }}</p>
                    <div class="mt-2 flex items-center gap-2">
                        @if(!$event->parents_notified)
                            <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">Non notifié</span>
                        @else
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full"><i class="fas fa-check mr-1"></i>Notifié</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-check text-3xl text-green-300 mb-2"></i>
                    <p>Aucun signalement en cantine</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
