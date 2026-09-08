@extends('layouts.app')

@section('title', 'Mon espace')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-home text-blue-600 mr-2"></i>
            Bonjour {{ $parent->first_name }}
        </h1>
        <p class="mt-1 text-sm text-gray-600">{{ $family->family_name }}</p>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    @if($unreadEvents->isNotEmpty())
    <!-- Signalements non lus (au-dessus du planning) -->
    <div class="mb-6 bg-white shadow-lg rounded-xl overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200 bg-gradient-to-r from-orange-500 to-orange-400 text-white flex items-center justify-between">
            <h3 class="font-bold">
                <i class="fas fa-exclamation-triangle mr-2"></i>Signalements non lus ({{ $unreadEvents->count() }})
            </h3>
            <form action="{{ route('parent.events.markViewed') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs bg-white text-orange-600 font-bold px-3 py-1 rounded-full hover:bg-orange-50 transition">
                    <i class="fas fa-check mr-1"></i> Marquer comme lu
                </button>
            </form>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($unreadEvents->take(3) as $event)
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
                </div>
            </div>
            @endforeach
        </div>
        <div class="p-2 border-t border-gray-100">
            <a href="{{ route('parent.events') }}" class="block text-center text-sm text-blue-600 hover:text-blue-800 py-1">
                Voir tout
            </a>
        </div>
    </div>
    @endif

    <!-- Stats cards -->
    <div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Heures garderie -->
        <div class="bg-white shadow-lg rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Garderie</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['garderie_hours'] }}h</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $monthName }} {{ $year }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-clock text-blue-600 text-xl"></i>
                </div>
            </div>
            @if($stats['garderie_trend'] !== null)
            <div class="mt-2 flex items-center gap-1 text-xs">
                <i class="fas fa-arrow-{{ $stats['garderie_trend'] >= 0 ? 'up text-green-500' : 'down text-red-500' }}"></i>
                <span class="{{ $stats['garderie_trend'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ abs($stats['garderie_trend']) }}% vs mois précédent
                </span>
            </div>
            @else
            <div class="mt-2 text-xs text-gray-400">Pas de données M-1</div>
            @endif
        </div>

        <!-- Repas cantine -->
        <div class="bg-white shadow-lg rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Cantine</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['cantine_meals'] }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">repas · {{ $monthName }} {{ $year }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-utensils text-green-600 text-xl"></i>
                </div>
            </div>
            @if($stats['cantine_trend'] !== null)
            <div class="mt-2 flex items-center gap-1 text-xs">
                <i class="fas fa-arrow-{{ $stats['cantine_trend'] >= 0 ? 'up text-green-500' : 'down text-red-500' }}"></i>
                <span class="{{ $stats['cantine_trend'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ abs($stats['cantine_trend']) }}% vs mois précédent
                </span>
            </div>
            @else
            <div class="mt-2 text-xs text-gray-400">Pas de données M-1</div>
            @endif
        </div>

        <!-- Moyenne repas/enfant -->
        <div class="bg-white shadow-lg rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Moyenne</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['avg_meals'] }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">repas/enfant · {{ $monthName }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-chart-bar text-purple-600 text-xl"></i>
                </div>
            </div>
            @if($stats['avg_trend'] !== null)
            <div class="mt-2 flex items-center gap-1 text-xs">
                <i class="fas fa-arrow-{{ $stats['avg_trend'] >= 0 ? 'up text-green-500' : 'down text-red-500' }}"></i>
                <span class="{{ $stats['avg_trend'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ abs($stats['avg_trend']) }}% vs mois précédent
                </span>
            </div>
            @else
            <div class="mt-2 text-xs text-gray-400">Pas de données M-1</div>
            @endif
        </div>
    </div>

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
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-green-500"></span>Repas</span>
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
                    $garderiePresence = $cell['garderie']->first();
                    $isToday = $cell['date'] === now()->format('Y-m-d');

                    $morningEnd = \Carbon\Carbon::parse(\App\Models\Setting::get('garderie_morning_end', '08:30'));
                    $eveningStart = \Carbon\Carbon::parse(\App\Models\Setting::get('garderie_evening_start', '16:30'));

                    $morningHours = null;
                    $eveningHours = null;
                    if ($garderiePresence && $garderiePresence->arrival_time && $garderiePresence->departure_time) {
                        $arrival = \Carbon\Carbon::parse($garderiePresence->arrival_time);
                        $departure = \Carbon\Carbon::parse($garderiePresence->departure_time);
                        if ($arrival->lt($morningEnd)) {
                            $morningEndActual = $departure->lt($morningEnd) ? $departure : $morningEnd;
                            $morningMinutes = $arrival->diffInMinutes($morningEndActual);
                            $morningHours = round($morningMinutes / 60, 1);
                        }
                        if ($departure->gt($eveningStart)) {
                            $eveningStartActual = $arrival->gt($eveningStart) ? $arrival : $eveningStart;
                            $eveningMinutes = $eveningStartActual->diffInMinutes($departure);
                            $eveningHours = round($eveningMinutes / 60, 1);
                        }
                    }
                @endphp
                <div class="border rounded-lg p-1 min-h-[60px] {{ $isToday ? 'border-indigo-400 bg-indigo-50' : 'border-gray-200' }} {{ ($hasGarderie || $hasCantine) ? '' : 'bg-gray-50' }}">
                    <div class="text-xs font-medium {{ $isToday ? 'text-indigo-700' : 'text-gray-600' }}">{{ $cell['day'] }}</div>
                    @if($hasGarderie || $hasCantine)
                    <div class="mt-1 space-y-0.5">
                        @if($hasGarderie)
                        <div class="flex items-center gap-0.5">
                            <span class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0"></span>
                            <span class="text-[10px] text-gray-600">
                                @if($morningHours && $eveningHours)
                                    M:{{ $morningHours }}h S:{{ $eveningHours }}h
                                @elseif($morningHours)
                                    M:{{ $morningHours }}h
                                @elseif($eveningHours)
                                    S:{{ $eveningHours }}h
                                @else
                                    Garderie
                                @endif
                            </span>
                        </div>
                        @endif
                        @if($cantineLunch)
                        <div class="flex items-center gap-0.5">
                            <span class="w-2 h-2 rounded-full bg-green-500 flex-shrink-0"></span>
                            <span class="text-[10px] text-gray-600">Repas</span>
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

    <div class="grid grid-cols-1 gap-6">
        <!-- Mes enfants -->
        <div>
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

    </div>
</div>
@endsection
