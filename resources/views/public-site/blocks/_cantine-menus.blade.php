@php
    $monthOffset = $config['month_offset'] ?? 0;
    $limit = $config['limit'] ?? 10;
    $date = now()->startOfMonth()->addMonths($monthOffset);
    $year = $date->year;
    $month = $date->month;

    $menus = \App\Modules\Cantine\Models\CantineMenu::published()
        ->withoutGlobalScope('tenant')
        ->whereYear('menu_date', $year)
        ->whereMonth('menu_date', $month)
        ->orderBy('menu_date')
        ->get()
        ->groupBy(fn($m) => $m->menu_date->format('Y-m-d'))
        ->take($limit);
@endphp

<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-4">
        <i class="fas fa-utensils mr-2" style="color: {{ tenant()->primary_color ?? '#3B82F6' }}"></i>
        Menus de la cantine — {{ ucfirst($date->locale('fr')->monthName) }} {{ $year }}
    </h2>

    @if($menus->count() > 0)
    <div class="space-y-3">
        @foreach($menus as $date => $dayMenus)
        <div class="border-l-4 pl-4" style="border-color: {{ tenant()->primary_color ?? '#3B82F6' }}">
            <p class="font-bold text-gray-800 capitalize">{{ \Carbon\Carbon::parse($date)->locale('fr')->isoFormat('dddd D MMMM') }}</p>
            @foreach($dayMenus as $menu)
            <div class="text-sm text-gray-600 mt-1">
                <span class="font-medium">{{ $menu->meal_type === 'lunch' ? 'Déjeuner' : 'Goûter' }}</span>
                @if($menu->title) — {{ $menu->title }} @endif
                @if($menu->main_course) · {{ $menu->main_course }} @endif
                @if($menu->dessert) · {{ $menu->dessert }} @endif
            </div>
            @endforeach
        </div>
        @endforeach
    </div>
    @else
    <p class="text-gray-400 text-center py-8">Aucun menu publié pour ce mois.</p>
    @endif
</div>
