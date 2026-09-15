@extends('layouts.app')

@section('title', $item->name . ' - Historique')

@section('content')
<div class="max-w-4xl mx-auto px-2 sm:px-4 lg:px-8">
    <div class="mb-4">
        <a href="{{ route('stock.items.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-1"></i>Retour aux articles
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-xl p-6 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $item->name }}</h1>
                @if($item->reference)
                <p class="text-sm text-gray-400 font-mono mt-1">Réf: {{ $item->reference }}</p>
                @endif
                <div class="flex items-center gap-2 mt-2 text-sm text-gray-600">
                    @if($item->category)
                    <span class="bg-gray-100 px-2 py-0.5 rounded">{{ $item->category }}</span>
                    @endif
                    <span><i class="fas fa-warehouse mr-1"></i>{{ $item->location?->name ?? '—' }}</span>
                </div>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold text-gray-900">{{ number_format($item->quantity, 2) }}</div>
                <div class="text-sm text-gray-500">{{ $item->unit }}</div>
                <div class="text-xs text-gray-400 mt-1">Min: {{ number_format($item->min_quantity, 2) }}</div>
            </div>
        </div>
        @if($item->description)
        <p class="text-sm text-gray-600 mt-3 pt-3 border-t">{{ $item->description }}</p>
        @endif
    </div>

    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <div class="px-5 py-3 border-b bg-gray-50">
            <h2 class="font-bold text-gray-900"><i class="fas fa-history mr-2 text-indigo-600"></i>Historique des mouvements</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock après</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motif</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Par</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($movements as $movement)
                    @php
                        $typeColors = ['in' => 'text-green-600', 'out' => 'text-red-600', 'adjust' => 'text-blue-600'];
                        $typeSigns = ['in' => '+', 'out' => '-', 'adjust' => '='];
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium {{ $typeColors[$movement->type] }}">{{ $movement->type_label }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm {{ $typeColors[$movement->type] }}">{{ $typeSigns[$movement->type] }} {{ number_format($movement->quantity, 2) }} {{ $item->unit }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-medium">{{ number_format($movement->new_quantity, 2) }} {{ $item->unit }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $movement->reason ?? '—' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $movement->createdBy?->name ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                            <i class="fas fa-history text-3xl text-gray-300 mb-2"></i>
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
