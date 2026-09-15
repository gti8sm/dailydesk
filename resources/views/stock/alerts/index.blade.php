@extends('layouts.app')

@section('title', 'Stock - Alertes')

@section('content')
<div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
    <div class="mb-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
            <i class="fas fa-triangle-exclamation text-red-600 mr-2"></i>Alertes de stock
        </h1>
        <p class="mt-1 text-xs sm:text-sm text-gray-600">Articles sous le seuil minimum — à commander</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white shadow-lg rounded-xl p-4 border-l-4 border-red-500">
            <div class="text-3xl font-bold text-red-600">{{ $criticalCount }}</div>
            <div class="text-sm text-gray-600">Stock épuisé</div>
        </div>
        <div class="bg-white shadow-lg rounded-xl p-4 border-l-4 border-orange-500">
            <div class="text-3xl font-bold text-orange-600">{{ $lowCount }}</div>
            <div class="text-sm text-gray-600">Stock faible</div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-300 rounded-lg text-green-700">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    <!-- Liste articles en alerte -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($items as $item)
        @php
            $isCritical = $item->quantity <= 0;
            $colorClass = $isCritical ? 'border-red-500 bg-red-50' : 'border-orange-500 bg-orange-50';
            $badgeClass = $isCritical ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700';
            $icon = $isCritical ? 'fa-circle-exclamation' : 'fa-triangle-exclamation';
            $label = $isCritical ? 'Épuisé' : 'Faible';
        @endphp
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border-l-4 {{ $isCritical ? 'border-red-500' : 'border-orange-500' }}">
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <h3 class="font-bold text-gray-900">{{ $item->name }}</h3>
                        <p class="text-xs text-gray-500"><i class="fas fa-warehouse mr-1"></i>{{ $item->location?->name ?? '—' }}</p>
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $badgeClass }}">
                        <i class="fas {{ $icon }} mr-1"></i>{{ $label }}
                    </span>
                </div>

                <div class="flex items-end justify-between mt-3">
                    <div>
                        <div class="text-2xl font-bold {{ $isCritical ? 'text-red-600' : 'text-orange-600' }}">
                            {{ number_format($item->quantity, 2) }} <span class="text-sm font-normal text-gray-500">{{ $item->unit }}</span>
                        </div>
                        <div class="text-xs text-gray-400">Seuil min: {{ number_format($item->min_quantity, 2) }} {{ $item->unit }}</div>
                    </div>
                    @can('record_stock_movement')
                    <a href="{{ route('stock.movements.create', ['stock_item_id' => $item->id]) }}" class="px-3 py-1.5 bg-green-600 text-white text-xs rounded-lg hover:bg-green-700 font-medium">
                        <i class="fas fa-plus mr-1"></i>Réappro.
                    </a>
                    @endcan
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white shadow-lg rounded-xl p-12 text-center text-gray-500">
            <i class="fas fa-check-circle text-4xl text-green-400 mb-3"></i>
            <p class="text-lg font-medium text-green-600">Aucune alerte</p>
            <p class="text-sm text-gray-400 mt-1">Tous les articles sont au-dessus du seuil minimum</p>
        </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $items->links() }}</div>
</div>
@endsection
