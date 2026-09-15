@extends('layouts.app')

@section('title', 'Stock - Lieux de stockage')

@section('content')
<div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
    <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                <i class="fas fa-warehouse text-indigo-600 mr-2"></i>Lieux de stockage
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-gray-600">Gérez les emplacements de stock</p>
        </div>
        <a href="{{ route('stock.locations.create') }}"
           class="px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition-colors flex items-center shadow-md">
            <i class="fas fa-plus mr-2"></i> Nouveau lieu
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-300 rounded-lg text-green-700">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($locations as $location)
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <div class="p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">{{ $location->name }}</h3>
                        @if($location->description)
                        <p class="text-sm text-gray-500 mt-1">{{ $location->description }}</p>
                        @endif
                    </div>
                    @if($location->is_active)
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Actif</span>
                    @else
                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-full">Archivé</span>
                    @endif
                </div>
                <div class="mt-3 flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-boxes-stacked text-indigo-400"></i>
                    <span>{{ $location->items->count() }} article(s)</span>
                </div>
            </div>
            <div class="px-5 py-3 bg-gray-50 border-t flex items-center justify-between">
                <a href="{{ route('stock.items.index', ['location_id' => $location->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    <i class="fas fa-eye mr-1"></i>Voir les articles
                </a>
                <div class="flex gap-2">
                    <a href="{{ route('stock.locations.edit', $location) }}" class="text-sm text-blue-600 hover:text-blue-800">Modifier</a>
                    <form action="{{ route('stock.locations.destroy', $location) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800"
                                onclick="return confirm('Archiver ce lieu ?')">Archiver</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white shadow-lg rounded-xl p-12 text-center text-gray-500">
            <i class="fas fa-warehouse text-4xl text-gray-300 mb-3"></i>
            <p>Aucun lieu de stockage</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
