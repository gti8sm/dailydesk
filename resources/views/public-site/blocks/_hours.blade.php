@php
    $days = [
        'monday' => 'Lundi', 'tuesday' => 'Mardi', 'wednesday' => 'Mercredi',
        'thursday' => 'Jeudi', 'friday' => 'Vendredi',
        'saturday' => 'Samedi', 'sunday' => 'Dimanche',
    ];
    $now = now();
    $todayKey = strtolower($now->englishDayOfWeek);
    $todayConfig = $config[$todayKey] ?? null;
    $isOpenNow = false;

    if ($todayConfig) {
        $nowTime = $now->format('H:i');
        if (!empty($todayConfig['open']) && !empty($todayConfig['close']) && $nowTime >= $todayConfig['open'] && $nowTime <= $todayConfig['close']) {
            $isOpenNow = true;
        }
        if (!empty($todayConfig['open2']) && !empty($todayConfig['close2']) && $nowTime >= $todayConfig['open2'] && $nowTime <= $todayConfig['close2']) {
            $isOpenNow = true;
        }
    }
@endphp

<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-gray-900">
            <i class="fas fa-clock mr-2" style="color: {{ tenant()->primary_color ?? '#3B82F6' }}"></i>
            Horaires d'ouverture
        </h2>
        @if($isOpenNow)
        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>Ouvert
        </span>
        @else
        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
            <span class="w-2 h-2 bg-red-500 rounded-full"></span>Fermé
        </span>
        @endif
    </div>

    <div class="space-y-2">
        @foreach($days as $dayKey => $dayLabel)
        @php
            $dayConfig = $config[$dayKey] ?? null;
            $isToday = $dayKey === $todayKey;
        @endphp
        <div class="flex items-center justify-between py-2 @if($isToday) bg-blue-50 -mx-2 px-2 rounded-lg @endif">
            <span class="text-sm font-medium @if($isToday) text-blue-700 @else text-gray-700 @endif">
                {{ $dayLabel }}
                @if($isToday)<span class="text-xs ml-1">(aujourd'hui)</span>@endif
            </span>
            <div class="text-right">
                @if($dayConfig && (!empty($dayConfig['open']) || !empty($dayConfig['open2'])))
                    <span class="text-sm text-gray-600">
                        @if(!empty($dayConfig['open'])){{ $dayConfig['open'] }} - {{ $dayConfig['close'] }}@endif
                        @if(!empty($dayConfig['open2'])) · {{ $dayConfig['open2'] }} - {{ $dayConfig['close2'] }}@endif
                    </span>
                @else
                    <span class="text-sm text-gray-400 italic">Fermé</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
