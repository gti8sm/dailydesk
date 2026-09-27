@php
    $showForecast = $config['show_forecast'] ?? true;
    $tenant = tenant();

    // Récupère les coords depuis le code postal via geo.api.gouv.fr (cache permanent)
    $coords = \Illuminate\Support\Facades\Cache::rememberForever("tenant_coords_{$tenant->id}", function () use ($tenant) {
        if (!$tenant->postal_code) return null;
        try {
            $response = \Illuminate\Support\Facades\Http::get("https://geo.api.gouv.fr/communes", [
                'codePostal' => $tenant->postal_code,
                'fields' => 'code,nom,centre',
                'format' => 'json',
            ])->json();
            if (!empty($response[0]['centre'])) {
                return [
                    'lat' => $response[0]['centre']['coordinates'][1],
                    'lon' => $response[0]['centre']['coordinates'][0],
                ];
            }
        } catch (\Exception $e) {}
        return null;
    });

    // Météo via Open-Meteo (cache 1h)
    $weather = null;
    if ($coords) {
        $weather = \Illuminate\Support\Facades\Cache::remember("weather_{$tenant->id}", 3600, function () use ($coords, $showForecast) {
            try {
                $params = [
                    'latitude' => $coords['lat'],
                    'longitude' => $coords['lon'],
                    'current' => 'temperature_2m,weather_code,wind_speed_10m',
                    'timezone' => 'auto',
                ];
                if ($showForecast) {
                    $params['daily'] = 'temperature_2m_max,temperature_2m_min,weather_code';
                    $params['forecast_days'] = 3;
                }
                return \Illuminate\Support\Facades\Http::get('https://api.open-meteo.com/v1/forecast', $params)->json();
            } catch (\Exception $e) {
                return null;
            }
        });
    }

    // Mapping codes météo → icône + description
    $weatherMap = [
        0 => ['☀️', 'Ciel dégagé'],
        1 => ['🌤️', 'Peu nuageux'],
        2 => ['⛅', 'Partiellement nuageux'],
        3 => ['☁️', 'Couvert'],
        45 => ['🌫️', 'Brouillard'],
        48 => ['🌫️', 'Brouillard givrant'],
        51 => ['🌦️', 'Bruine légère'],
        53 => ['🌦️', 'Bruine'],
        55 => ['🌧️', 'Bruine dense'],
        61 => ['🌧️', 'Pluie légère'],
        63 => ['🌧️', 'Pluie'],
        65 => ['🌧️', 'Pluie forte'],
        71 => ['🌨️', 'Neige légère'],
        73 => ['🌨️', 'Neige'],
        75 => ['❄️', 'Neige forte'],
        80 => ['🌧️', 'Averses'],
        81 => ['🌧️', 'Averses'],
        82 => ['⛈️', 'Averses violentes'],
        95 => ['⛈️', 'Orage'],
        96 => ['⛈️', 'Orage + grêle'],
        99 => ['⛈️', 'Orage violent'],
    ];
@endphp

<div class="bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-xl shadow-lg p-6 mb-6">
    @if($weather && isset($weather['current']))
    @php
        $current = $weather['current'];
        $code = $current['weather_code'] ?? 0;
        $info = $weatherMap[$code] ?? ['🌡️', '—'];
    @endphp
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-white/80">Météo actuelle</p>
            <p class="text-5xl font-bold">{{ round($current['temperature_2m']) }}°C</p>
            <p class="text-sm mt-1">{{ $info[1] }}</p>
            @if(isset($current['wind_speed_10m']))
            <p class="text-xs text-white/70 mt-1"><i class="fas fa-wind mr-1"></i>{{ round($current['wind_speed_10m']) }} km/h</p>
            @endif
        </div>
        <div class="text-7xl">{{ $info[0] }}</div>
    </div>

    @if($showForecast && isset($weather['daily']))
    <div class="mt-6 pt-4 border-t border-white/20 grid grid-cols-3 gap-2 text-center">
        @foreach($weather['daily']['time'] as $i => $date)
        @php
            $dCode = $weather['daily']['weather_code'][$i] ?? 0;
            $dInfo = $weatherMap[$dCode] ?? ['🌡️', '—'];
        @endphp
        <div>
            <p class="text-xs text-white/80 capitalize">{{ \Carbon\Carbon::parse($date)->locale('fr')->isoFormat('ddd') }}</p>
            <p class="text-2xl my-1">{{ $dInfo[0] }}</p>
            <p class="text-xs">
                <span class="font-semibold">{{ round($weather['daily']['temperature_2m_max'][$i]) }}°</span>
                <span class="text-white/60">{{ round($weather['daily']['temperature_2m_min'][$i]) }}°</span>
            </p>
        </div>
        @endforeach
    </div>
    @endif
    @else
    <div class="text-center py-4">
        <i class="fas fa-cloud-sun text-4xl mb-2 text-white/70"></i>
        <p class="text-sm text-white/70">Météo indisponible</p>
        @if(!$tenant->postal_code)
        <p class="text-xs text-white/50 mt-1">Code postal requis</p>
        @endif
    </div>
    @endif
</div>
