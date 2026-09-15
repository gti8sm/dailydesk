@extends('layouts.app')

@section('title', 'Cantine - Menus')

@section('content')
<div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
    <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                <i class="fas fa-utensils text-green-600 mr-2"></i>
                Menus de la cantine
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-gray-600">Planifiez et publiez les menus — visibles par les parents une fois publiés</p>
        </div>
        <a href="{{ route('cantine.menus.create') }}"
           class="px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition-colors flex items-center shadow-md">
            <i class="fas fa-plus mr-2"></i> Nouveau menu
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-300 rounded-lg text-green-700">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    <!-- Navigation mois -->
    <div class="mb-4 flex items-center justify-between bg-white shadow rounded-xl px-4 py-3">
        <a href="?year={{ $prevMonth->year }}&month={{ $prevMonth->month }}" class="text-gray-500 hover:text-gray-700 px-2 py-1">
            <i class="fas fa-chevron-left"></i>
        </a>
        <span class="font-bold text-gray-900 capitalize">{{ $monthName }} {{ $year }}</span>
        <a href="?year={{ $nextMonth->year }}&month={{ $nextMonth->month }}" class="text-gray-500 hover:text-gray-700 px-2 py-1">
            <i class="fas fa-chevron-right"></i>
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Repas</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Menu</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Allergènes</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($menus as $menu)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            {{ $menu->menu_date->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($menu->meal_type === 'lunch')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Déjeuner</span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Goûter</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($menu->title)<div class="font-medium text-gray-900">{{ $menu->title }}</div>@endif
                            <div class="text-gray-700">
                                @if($menu->starter)<span class="text-gray-500">{{ $menu->starter }} · </span>@endif
                                <span class="font-medium">{{ $menu->main_course }}</span>
                                @if($menu->side_dish)<span class="text-gray-500"> · {{ $menu->side_dish }}</span>@endif
                                @if($menu->dessert)<span class="text-gray-500"> · {{ $menu->dessert }}</span>@endif
                            </div>
                            @if($menu->vegetarian)
                                <span class="inline-flex items-center mt-1 px-1.5 py-0.5 rounded text-[10px] bg-green-50 text-green-700"><i class="fas fa-leaf mr-1"></i>Végé</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if(!empty($menu->allergens))
                                @foreach($menu->allergens as $allergen)
                                <span class="inline-block mr-1 mb-1 px-1.5 py-0.5 rounded text-[10px] bg-red-50 text-red-700">{{ $allergen }}</span>
                                @endforeach
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($menu->is_published)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i> Publié
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    <i class="fas fa-pencil-alt mr-1"></i> Brouillon
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right text-sm whitespace-nowrap">
                            <a href="{{ route('cantine.menus.edit', $menu) }}" class="text-blue-600 hover:text-blue-800 font-medium">Modifier</a>
                            <form action="{{ route('cantine.menus.togglePublish', $menu) }}" method="POST" class="inline ml-2">
                                @csrf
                                <button type="submit" class="font-medium {{ $menu->is_published ? 'text-orange-600 hover:text-orange-800' : 'text-green-600 hover:text-green-800' }}">
                                    {{ $menu->is_published ? 'Dépublier' : 'Publier' }}
                                </button>
                            </form>
                            <form action="{{ route('cantine.menus.destroy', $menu) }}" method="POST" class="inline ml-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium"
                                        onclick="return confirm('Supprimer ce menu ?')">
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                            <i class="fas fa-utensils text-4xl text-gray-300 mb-2"></i>
                            <p>Aucun menu pour {{ $monthName }} {{ $year }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
