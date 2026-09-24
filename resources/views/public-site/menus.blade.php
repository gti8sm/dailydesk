@extends('layouts.public-site')

@section('title', 'Menus cantine — ' . $tenant->name)
@section('meta_description', 'Menus de la cantine scolaire de ' . $tenant->name . ' pour ' . $monthName . ' ' . $year)

@section('content')
<section class="py-12 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-8 flex-wrap gap-4">
            <h1 class="text-3xl font-bold text-gray-900"><i class="fas fa-utensils text-primary mr-2"></i>Menus de la cantine</h1>

            <!-- Sélecteur de mois -->
            <div class="flex items-center gap-2">
                @php
                    $prevMonth = $month == 1 ? 12 : $month - 1;
                    $prevYear = $month == 1 ? $year - 1 : $year;
                    $nextMonth = $month == 12 ? 1 : $month + 1;
                    $nextYear = $month == 12 ? $year + 1 : $year;
                @endphp
                <a href="{{ route('public.site.menus', ['tenant' => $tenant->slug, 'year' => $prevYear, 'month' => $prevMonth]) }}" class="px-3 py-2 bg-white rounded-lg shadow-sm hover:shadow border border-gray-200 text-gray-600">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <span class="px-4 py-2 bg-white rounded-lg shadow-sm border border-gray-200 font-medium text-gray-900">{{ $monthName }} {{ $year }}</span>
                <a href="{{ route('public.site.menus', ['tenant' => $tenant->slug, 'year' => $nextYear, 'month' => $nextMonth]) }}" class="px-3 py-2 bg-white rounded-lg shadow-sm hover:shadow border border-gray-200 text-gray-600">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>

        @if($menus->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($menus as $date => $dayMenus)
            <div class="bg-white rounded-lg shadow-md p-5">
                <p class="font-bold text-primary mb-3 capitalize">{{ \Carbon\Carbon::parse($date)->locale('fr')->isoFormat('dddd D MMMM') }}</p>
                @foreach($dayMenus as $menu)
                <div class="border-t border-gray-100 pt-3 mt-3 first:border-0 first:pt-0 first:mt-0">
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-1">{{ $menu->meal_type === 'midi' ? 'Déjeuner' : 'Goûter' }}</p>
                    @if($menu->title)<p class="font-semibold text-gray-800 text-sm mb-1">{{ $menu->title }}</p>@endif
                    @if($menu->starter)<p class="text-xs text-gray-600"><i class="fas fa-circle text-gray-300 mr-1 text-[6px]"></i>{{ $menu->starter }}</p>@endif
                    @if($menu->main_course)<p class="text-xs text-gray-600"><i class="fas fa-circle text-gray-300 mr-1 text-[6px]"></i>{{ $menu->main_course }}</p>@endif
                    @if($menu->side_dish)<p class="text-xs text-gray-600"><i class="fas fa-circle text-gray-300 mr-1 text-[6px]"></i>{{ $menu->side_dish }}</p>@endif
                    @if($menu->dessert)<p class="text-xs text-gray-600"><i class="fas fa-circle text-gray-300 mr-1 text-[6px]"></i>{{ $menu->dessert }}</p>@endif
                    @if($menu->vegetarian)<span class="inline-block mt-1 text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Végétarien</span>@endif
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-16 text-gray-400">
            <i class="fas fa-utensils text-6xl mb-4"></i>
            <p class="text-lg">Aucun menu publié pour {{ $monthName }} {{ $year }}.</p>
        </div>
        @endif
    </div>
</section>
@endsection
