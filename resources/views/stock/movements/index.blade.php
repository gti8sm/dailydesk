@extends('layouts.app')

@section('title', 'Stock - Mouvements')

@section('content')
<div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
    <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                <i class="fas fa-arrow-right-arrow-left text-indigo-600 mr-2"></i>Mouvements de stock
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-gray-600">{{ $movements->total() }} mouvement(s)</p>
        </div>
        @can('record_stock_movement')
        <a href="{{ route('stock.movements.create') }}"
           class="px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition-colors flex items-center shadow-md">
            <i class="fas fa-plus mr-2"></i> Nouveau mouvement
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
        <form method="GET" class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <select name="location_id" class="rounded-lg border-gray-300 text-sm">
                <option value="">Tous les lieux</option>
                @foreach($locations as $location)
                <option value="{{ $location->id }}" @selected(request('location_id') == $location->id)>{{ $location->name }}</option>
                @endforeach
            </select>
            <select name="type" class="rounded-lg border-gray-300 text-sm">
                <option value="">Tous types</option>
                @foreach($types as $key => $label)
                <option value="{{ $key }}" @selected(request('type') == $key)>{{ $label }}</option>
                @endforeach
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border-gray-300 text-sm">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border-gray-300 text-sm">
            <div class="flex gap-2">
                <button type="submit" class="px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium flex-1">
                    <i class="fas fa-filter mr-1"></i>Filtrer
                </button>
                <a href="{{ route('stock.movements.index') }}" class="px-3 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 text-sm">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Liste mouvements -->
    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Article</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lieu</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qté</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock après</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motif</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Par</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($movements as $movement)
                    @php
                        $typeColors = ['in' => 'bg-green-100 text-green-700', 'out' => 'bg-red-100 text-red-700', 'adjust' => 'bg-blue-100 text-blue-700'];
                        $typeIcons = ['in' => 'fa-arrow-down', 'out' => 'fa-arrow-up', 'adjust' => 'fa-equals'];
                        $typeSigns = ['in' => '+', 'out' => '-', 'adjust' => '='];
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $movement->stockItem?->name ?? '—' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $movement->stockItem?->location?->name ?? '—' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $typeColors[$movement->type] }}">
                                <i class="fas {{ $typeIcons[$movement->type] }} mr-1"></i>{{ $movement->type_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">{{ $typeSigns[$movement->type] }} {{ number_format($movement->quantity, 2) }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ number_format($movement->new_quantity, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $movement->reason ?? '—' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $movement->createdBy?->name ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                            <i class="fas fa-arrow-right-arrow-left text-3xl text-gray-300 mb-2"></i>
                            <p>Aucun mouvement</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $movements->links() }}</div>
    </div>
</div>
@endsection
