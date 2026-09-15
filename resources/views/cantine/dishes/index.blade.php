@extends('layouts.app')

@section('title', 'Cantine - Plats enregistrés')

@section('content')
<div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
    <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                <i class="fas fa-utensils text-green-600 mr-2"></i>
                Plats enregistrés
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-gray-600">Catalogue de plats réutilisables pour la saisie rapide des menus</p>
        </div>
        <a href="{{ route('cantine.dishes.create') }}"
           class="px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition-colors flex items-center shadow-md">
            <i class="fas fa-plus mr-2"></i> Nouveau plat
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-300 rounded-lg text-green-700">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    @php
        $categoryLabels = [
            'starter' => 'Entrées',
            'main_course' => 'Plats principaux',
            'side_dish' => 'Accompagnements',
            'dessert' => 'Desserts',
        ];
        $categoryIcons = [
            'starter' => 'fa-seedling',
            'main_course' => 'fa-drumstick-bite',
            'side_dish' => 'fa-carrot',
            'dessert' => 'fa-ice-cream',
        ];
        $categoryColors = [
            'starter' => 'green',
            'main_course' => 'green',
            'side_dish' => 'orange',
            'dessert' => 'pink',
        ];
    @endphp

    @foreach(['starter', 'main_course', 'side_dish', 'dessert'] as $cat)
    @if(isset($dishes[$cat]))
    <div class="mb-6 bg-white shadow-lg rounded-xl overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-gray-900">
                <i class="fas {{ $categoryIcons[$cat] }} text-{{ $categoryColors[$cat] }}-500 mr-2"></i>
                {{ $categoryLabels[$cat] }}
                <span class="ml-2 text-xs font-normal text-gray-500">({{ $dishes[$cat]->count() }})</span>
            </h3>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($dishes[$cat] as $dish)
            <div class="p-4 flex items-center justify-between hover:bg-gray-50">
                <div class="flex items-center gap-3">
                    <div>
                        <div class="font-medium text-gray-900">{{ $dish->name }}</div>
                        <div class="mt-1 flex flex-wrap gap-1">
                            @if($dish->vegetarian)
                                <span class="text-xs bg-green-50 text-green-700 px-1.5 py-0.5 rounded"><i class="fas fa-leaf mr-1"></i>Végé</span>
                            @endif
                            @if(!empty($dish->allergens))
                                @foreach($dish->allergens as $allergen)
                                <span class="text-xs bg-red-50 text-red-700 px-1.5 py-0.5 rounded">{{ $allergen }}</span>
                                @endforeach
                            @endif
                            @if($dish->notes)
                                <span class="text-xs text-gray-400 italic">{{ \Illuminate\Support\Str::limit($dish->notes, 60) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-sm whitespace-nowrap">
                    <a href="{{ route('cantine.dishes.edit', $dish) }}" class="text-blue-600 hover:text-blue-800 font-medium">Modifier</a>
                    <form action="{{ route('cantine.dishes.destroy', $dish) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium"
                                onclick="return confirm('Supprimer ce plat ?')">
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    @endforeach

    @if($dishes->isEmpty())
    <div class="bg-white shadow-lg rounded-xl p-12 text-center text-gray-500">
        <i class="fas fa-utensils text-4xl text-gray-300 mb-2"></i>
        <p>Aucun plat enregistré pour le moment</p>
        <p class="text-sm text-gray-400 mt-1">Les plats saisis dans les menus seront automatiquement sauvegardés ici.</p>
    </div>
    @endif
</div>
@endsection
