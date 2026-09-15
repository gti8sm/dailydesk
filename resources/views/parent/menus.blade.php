@extends('layouts.app')

@section('title', 'Menus de la cantine')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-utensils text-green-600 mr-2"></i>
            Menus de la cantine
        </h1>
        <p class="mt-1 text-sm text-gray-600">Menus publiés par la cantine pour vos enfants</p>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Navigation mois -->
    <div class="mb-6 flex items-center justify-between bg-white shadow-lg rounded-xl px-5 py-3">
        <a href="?year={{ $prevMonth->year }}&month={{ $prevMonth->month }}" class="text-gray-500 hover:text-gray-700 px-2 py-1">
            <i class="fas fa-chevron-left"></i>
        </a>
        <span class="font-bold text-gray-900 capitalize">{{ $monthName }} {{ $year }}</span>
        <a href="?year={{ $nextMonth->year }}&month={{ $nextMonth->month }}" class="text-gray-500 hover:text-gray-700 px-2 py-1">
            <i class="fas fa-chevron-right"></i>
        </a>
    </div>

    @if($menus->isNotEmpty())
    <div class="space-y-4">
        @foreach($menus as $menu)
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-200 bg-gradient-to-r from-green-600 to-green-500 text-white flex items-center justify-between">
                <h3 class="font-bold">
                    <i class="fas fa-calendar-day mr-2"></i>
                    {{ $menu->menu_date->locale('fr')->isoFormat('dddd d MMMM') }}
                </h3>
                <div class="flex items-center gap-2">
                    @if($menu->meal_type === 'lunch')
                        <span class="text-xs bg-white bg-opacity-20 px-2 py-1 rounded-full">Déjeuner</span>
                    @else
                        <span class="text-xs bg-white bg-opacity-20 px-2 py-1 rounded-full">Goûter</span>
                    @endif
                    @if($menu->vegetarian)
                        <span class="text-xs bg-green-700 px-2 py-1 rounded-full"><i class="fas fa-leaf mr-1"></i>Végé</span>
                    @endif
                </div>
            </div>
            <div class="p-5">
                @if($menu->title)
                <p class="font-medium text-gray-900 mb-3">{{ $menu->title }}</p>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @if($menu->starter)
                    <div class="flex items-start gap-2">
                        <i class="fas fa-seedling text-green-400 mt-1"></i>
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Entrée</p>
                            <p class="text-sm text-gray-800">{{ $menu->starter }}</p>
                        </div>
                    </div>
                    @endif
                    <div class="flex items-start gap-2">
                        <i class="fas fa-drumstick-bite text-green-600 mt-1"></i>
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Plat principal</p>
                            <p class="text-sm font-medium text-gray-900">{{ $menu->main_course }}</p>
                        </div>
                    </div>
                    @if($menu->side_dish)
                    <div class="flex items-start gap-2">
                        <i class="fas fa-carrot text-orange-400 mt-1"></i>
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Accompagnement</p>
                            <p class="text-sm text-gray-800">{{ $menu->side_dish }}</p>
                        </div>
                    </div>
                    @endif
                    @if($menu->dessert)
                    <div class="flex items-start gap-2">
                        <i class="fas fa-ice-cream text-pink-400 mt-1"></i>
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Dessert</p>
                            <p class="text-sm text-gray-800">{{ $menu->dessert }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                @if(!empty($menu->allergens))
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs font-medium text-red-600 mb-1.5"><i class="fas fa-exclamation-triangle mr-1"></i>Allergènes</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($menu->allergens as $allergen)
                        <span class="inline-block px-2 py-1 rounded-full text-xs bg-red-50 text-red-700 border border-red-100">{{ $allergen }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($menu->notes)
                <div class="mt-3 text-sm text-gray-600 bg-gray-50 rounded-lg p-3">
                    <i class="fas fa-info-circle mr-1 text-gray-400"></i>{{ $menu->notes }}
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white shadow-lg rounded-xl p-12 text-center text-gray-500">
        <i class="fas fa-utensils text-5xl text-gray-300 mb-3"></i>
        <p class="font-medium">Aucun menu publié pour {{ $monthName }} {{ $year }}</p>
        <p class="text-sm text-gray-400 mt-1">Les menus apparaîtront ici dès leur publication par la cantine.</p>
    </div>
    @endif
</div>
@endsection
