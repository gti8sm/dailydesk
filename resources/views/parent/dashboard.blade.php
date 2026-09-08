@extends('layouts.app')

@section('title', 'Mon espace')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-home text-blue-600 mr-2"></i>
            Bonjour {{ $parent->first_name }}
        </h1>
        <p class="mt-1 text-sm text-gray-600">Famille {{ $family->family_name }}</p>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Planning mensuel -->
    <div class="mb-6 bg-white shadow-lg rounded-xl overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200 bg-gradient-to-r from-indigo-600 to-purple-600 text-white flex items-center justify-between">
            <h3 class="font-bold">
                <i class="fas fa-calendar-alt mr-2"></i>Planning de {{ $monthName }} {{ $year }}
            </h3>
            <div class="flex items-center gap-2">
                <a href="?year={{ $prevMonth->year }}&month={{ $prevMonth->month }}" class="text-white hover:bg-white hover:bg-opacity-20 rounded px-2 py-1 transition">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <span class="text-sm">{{ $monthName }}</span>
                <a href="?year={{ $nextMonth->year }}&month={{ $nextMonth->month }}" class="text-white hover:bg-white hover:bg-opacity-20 rounded px-2 py-1 transition">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>
        <div class="p-4">
            <!-- Légende -->
            <div class="flex gap-4 mb-3 text-xs">
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-blue-500"></span>Garderie</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-green-500"></span>Cantine</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-orange-400"></span>Goûter</span>
            </div>
            <!-- Grille calendrier -->
            <div class="grid grid-cols-7 gap-1 text-center text-xs">
                @foreach(['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'] as $dayName)
                <div class="font-semibold text-gray-500 py-1">{{ $dayName }}</div>
                @endforeach
                @foreach($calendar as $cell)
                @if($cell === null)
                <div></div>
                @else
                @php
                    $hasGarderie = $cell['garderie']->isNotEmpty();
                    $hasCantine = $cell['cantine']->isNotEmpty();
                    $cantineLunch = $cell['cantine']->where('meal_type', 'lunch')->where('is_present', true)->isNotEmpty();
                    $cantineSnack = $cell['cantine']->where('meal_type', 'snack')->where('is_present', true)->isNotEmpty();
                    $garderieArrival = $cell['garderie']->whereNotNull('arrival_time')->first();
                    $garderieDeparture = $cell['garderie']->whereNotNull('departure_time')->first();
                    $isToday = $cell['date'] === now()->format('Y-m-d');
                @endphp
                <div class="border rounded-lg p-1 min-h-[60px] {{ $isToday ? 'border-indigo-400 bg-indigo-50' : 'border-gray-200' }} {{ ($hasGarderie || $hasCantine) ? '' : 'bg-gray-50' }}">
                    <div class="text-xs font-medium {{ $isToday ? 'text-indigo-700' : 'text-gray-600' }}">{{ $cell['day'] }}</div>
                    @if($hasGarderie || $hasCantine)
                    <div class="mt-1 space-y-0.5">
                        @if($hasGarderie)
                        <div class="flex items-center gap-0.5">
                            <span class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0"></span>
                            <span class="text-[10px] text-gray-600">
                                @if($garderieArrival && $garderieDeparture)
                                    {{ \Carbon\Carbon::parse($garderieArrival->arrival_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($garderieDeparture->departure_time)->format('H:i') }}
                                @elseif($garderieArrival)
                                    {{ \Carbon\Carbon::parse($garderieArrival->arrival_time)->format('H:i') }} →
                                @elseif($garderieDeparture)
                                    → {{ \Carbon\Carbon::parse($garderieDeparture->departure_time)->format('H:i') }}
                                @endif
                            </span>
                        </div>
                        @endif
                        @if($cantineLunch)
                        <div class="flex items-center gap-0.5">
                            <span class="w-2 h-2 rounded-full bg-green-500 flex-shrink-0"></span>
                            <span class="text-[10px] text-gray-600">Déj.</span>
                        </div>
                        @endif
                        @if($cantineSnack)
                        <div class="flex items-center gap-0.5">
                            <span class="w-2 h-2 rounded-full bg-orange-400 flex-shrink-0"></span>
                            <span class="text-[10px] text-gray-600">Goûter</span>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Mes enfants -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-lg rounded-xl overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-child text-blue-500 mr-2"></i>Mes enfants
                    </h3>
                    <a href="{{ route('parent.profile') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        <i class="fas fa-edit mr-1"></i>Modifier mes infos
                    </a>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($children as $child)
                    <div class="p-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-100 text-blue-700 rounded-full w-10 h-10 flex items-center justify-center font-bold">
                                {{ strtoupper(substr($child->first_name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $child->full_name }}</div>
                                <div class="text-xs text-gray-500">{{ $child->schoolClass?->name ?? 'Sans classe' }}</div>
                                <div class="mt-1 flex gap-1">
                                    @if($child->garderie_subscribed)
                                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Garderie</span>
                                    @endif
                                    @if($child->cantine_subscribed)
                                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Cantine</span>
                                    @endif
                                    @if($child->allergies)
                                        <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full"><i class="fas fa-exclamation-triangle"></i> Allergies</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('parent.children.edit', $child) }}" class="text-sm text-blue-600 hover:text-blue-800">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                    @endforeach
                    @if($children->isEmpty())
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-child text-4xl text-gray-300 mb-2"></i>
                        <p>Aucun enfant enregistré</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Signalements récents -->
        <div class="space-y-4">
            <div class="bg-white shadow-lg rounded-xl overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-200 bg-gradient-to-r from-orange-500 to-orange-400 text-white">
                    <h3 class="font-bold flex items-center justify-between">
                        <span><i class="fas fa-exclamation-triangle mr-2"></i>Signalements</span>
                        @if($unnotifiedCount > 0)
                        <span class="bg-white text-orange-600 text-xs font-bold px-2 py-0.5 rounded-full">
                            {{ $unnotifiedCount }} nouveau(x)
                        </span>
                        @endif
                    </h3>
                </div>
                <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                    @foreach($garderieEvents->concat($cantineEvents)->sortByDesc('event_date')->take(8) as $event)
                    <div class="p-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-gray-900">{{ $event->child->full_name }}</span>
                            <span class="text-xs text-gray-400">{{ $event->event_date->format('d/m/Y') }}</span>
                        </div>
                        <p class="text-sm text-gray-700 mt-1">{{ $event->title }}</p>
                        <div class="mt-1 flex items-center gap-1">
                            @if(str_contains(get_class($event), 'Garderie'))
                                <span class="text-xs bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded">Garderie</span>
                            @else
                                <span class="text-xs bg-green-100 text-green-700 px-1.5 py-0.5 rounded">Cantine</span>
                            @endif
                            @if(!$event->parents_notified)
                                <span class="text-xs bg-yellow-100 text-yellow-700 px-1.5 py-0.5 rounded">Non notifié</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    @if($garderieEvents->isEmpty() && $cantineEvents->isEmpty())
                    <div class="p-6 text-center text-gray-500 text-sm">
                        <i class="fas fa-check text-2xl text-green-300 mb-1"></i>
                        <p>Aucun signalement</p>
                    </div>
                    @endif
                </div>
                <div class="p-2 border-t border-gray-100">
                    <a href="{{ route('parent.events') }}" class="block text-center text-sm text-blue-600 hover:text-blue-800 py-1">
                        Voir tout
                    </a>
                </div>
            </div>

            <div class="bg-white shadow-lg rounded-xl p-4">
                <a href="{{ route('parent.notifications') }}" class="flex items-center justify-between text-gray-700 hover:text-blue-600">
                    <span class="text-sm font-medium"><i class="fas fa-bell mr-2"></i>Notifications</span>
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
