@extends('layouts.app')

@section('title', 'Stock - Articles')

@section('content')
<div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
    <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                <i class="fas fa-boxes-stacked text-indigo-600 mr-2"></i>Articles en stock
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-gray-600">
                {{ $items->total() }} article(s)
                @if($lowStockCount > 0)
                <span class="ml-2 inline-flex items-center px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                    <i class="fas fa-triangle-exclamation mr-1"></i>{{ $lowStockCount }} en alerte
                </span>
                @endif
            </p>
        </div>
        @can('manage_stock')
        <a href="{{ route('stock.items.create') }}"
           class="px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition-colors flex items-center shadow-md">
            <i class="fas fa-plus mr-2"></i> Nouvel article
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-300 rounded-lg text-green-700">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    <!-- Filtres -->
    <div class="bg-white shadow-lg rounded-xl p-4 mb-6">
        <form method="GET" class="space-y-3">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Rechercher un article, une référence, une catégorie…"
                       class="w-full pl-9 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <select name="location_id" class="rounded-lg border-gray-300 text-sm">
                    <option value="">Tous les lieux</option>
                    @foreach($locations as $location)
                    <option value="{{ $location->id }}" @selected(request('location_id') == $location->id)>{{ $location->name }}</option>
                    @endforeach
                </select>
                <select name="category" class="rounded-lg border-gray-300 text-sm">
                    <option value="">Toutes catégories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category }}" @selected(request('category') == $category)>{{ $category }}</option>
                    @endforeach
                </select>
                <label class="flex items-center gap-2 text-sm text-gray-600 col-span-2 sm:col-span-1">
                    <input type="checkbox" name="low_stock" value="1" @checked(request('low_stock'))
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    Stock faible uniquement
                </label>
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
                        <i class="fas fa-filter mr-1"></i>Filtrer
                    </button>
                    <a href="{{ route('stock.items.index') }}" class="px-3 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 text-sm">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Liste articles (cartes mobile-first) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($items as $item)
        @php
            $levelColors = [
                'ok' => 'bg-green-100 text-green-700 border-green-300',
                'warning' => 'bg-yellow-100 text-yellow-700 border-yellow-300',
                'low' => 'bg-orange-100 text-orange-700 border-orange-300',
                'critical' => 'bg-red-100 text-red-700 border-red-300',
            ];
            $levelLabels = [
                'ok' => 'OK',
                'warning' => 'À surveiller',
                'low' => 'Stock faible',
                'critical' => 'Critique',
            ];
            $levelIcons = [
                'ok' => 'fa-check-circle',
                'warning' => 'fa-eye',
                'low' => 'fa-triangle-exclamation',
                'critical' => 'fa-circle-exclamation',
            ];
        @endphp
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border-l-4 {{ $levelColors[$item->stock_level] }}">
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <h3 class="font-bold text-gray-900">{{ $item->name }}</h3>
                        @if($item->reference)
                        <p class="text-xs text-gray-400 font-mono">Réf: {{ $item->reference }}</p>
                        @endif
                    </div>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $levelColors[$item->stock_level] }}">
                        <i class="fas {{ $levelIcons[$item->stock_level] }} mr-1"></i>{{ $levelLabels[$item->stock_level] }}
                    </span>
                </div>

                <div class="flex items-center gap-2 text-xs text-gray-500 mb-3">
                    @if($item->category)
                    <span class="bg-gray-100 px-2 py-0.5 rounded">{{ $item->category }}</span>
                    @endif
                    <span><i class="fas fa-warehouse mr-1"></i>{{ $item->location?->name ?? '—' }}</span>
                </div>

                <div class="flex items-end justify-between">
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($item->quantity, 2) }} <span class="text-sm font-normal text-gray-500">{{ $item->unit }}</span></div>
                        <div class="text-xs text-gray-400">Min: {{ number_format($item->min_quantity, 2) }} {{ $item->unit }}</div>
                    </div>
                    <div class="flex flex-col gap-1">
                        @can('record_stock_movement')
                        <a href="{{ route('stock.movements.create', ['stock_item_id' => $item->id]) }}" class="px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-lg hover:bg-indigo-700 font-medium text-center">
                            <i class="fas fa-arrow-right-arrow-left mr-1"></i>Mouvement
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="px-4 py-2 bg-gray-50 border-t flex items-center justify-between">
                <a href="{{ route('stock.items.show', $item) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                    <i class="fas fa-history mr-1"></i>Historique
                </a>
                @can('manage_stock')
                <div class="flex gap-2">
                    <a href="{{ route('stock.items.edit', $item) }}" class="text-xs text-blue-600 hover:text-blue-800">Modifier</a>
                    <form action="{{ route('stock.items.destroy', $item) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-600 hover:text-red-800" onclick="return confirm('Archiver cet article ?')">Archiver</button>
                    </form>
                </div>
                @endcan
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white shadow-lg rounded-xl p-12 text-center text-gray-500">
            <i class="fas fa-boxes-stacked text-4xl text-gray-300 mb-3"></i>
            <p>Aucun article</p>
            @if(request()->hasAny(['search','location_id','category','low_stock']))
            <p class="text-sm text-gray-400 mt-1">Essayez de modifier les filtres</p>
            @endif
        </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $items->links() }}</div>
</div>
@endsection
