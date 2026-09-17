@extends('layouts.app')

@section('title', 'Support - Tickets')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('central.dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Retour au dashboard
        </a>
    </div>

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-life-ring text-orange-600 mr-2"></i> Tickets de Support
        </h1>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <div class="mb-6 grid grid-cols-2 sm:grid-cols-5 gap-4">
        <div class="bg-white shadow rounded-lg p-4">
            <p class="text-xs text-gray-500 uppercase">Total</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
            <p class="text-xs text-gray-500 uppercase">Ouverts</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['open'] }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
            <p class="text-xs text-gray-500 uppercase">En cours</p>
            <p class="text-2xl font-bold text-orange-600">{{ $stats['in_progress'] }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4">
            <p class="text-xs text-gray-500 uppercase">Résolus</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['resolved'] }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4 {{ $stats['unread'] > 0 ? 'ring-2 ring-red-400' : '' }}">
            <p class="text-xs text-gray-500 uppercase">Non lus</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['unread'] }}</p>
        </div>
    </div>

    <div class="mb-4 flex flex-wrap gap-2">
        <a href="{{ route('central.support.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ !$archivedFilter && $statusFilter === 'all' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border' }}">Tous</a>
        <a href="{{ route('central.support.index', ['status' => 'open']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ !$archivedFilter && $statusFilter === 'open' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border' }}">Ouverts</a>
        <a href="{{ route('central.support.index', ['status' => 'in_progress']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ !$archivedFilter && $statusFilter === 'in_progress' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border' }}">En cours</a>
        <a href="{{ route('central.support.index', ['status' => 'resolved']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ !$archivedFilter && $statusFilter === 'resolved' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border' }}">Résolus</a>
        <a href="{{ route('central.support.index', ['archived' => 1]) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ $archivedFilter ? 'bg-gray-700 text-white' : 'bg-white text-gray-700 border' }}">
            <i class="fas fa-archive mr-1"></i> Archivés ({{ $stats['archived'] }})
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sujet</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Demandeur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priorité</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($tickets as $ticket)
                <tr class="hover:bg-gray-50 {{ $ticket->is_unread_by_staff ? 'bg-red-50' : '' }}">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @if($ticket->is_unread_by_staff && !$archivedFilter)
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2 flex-shrink-0" title="Non lu"></span>
                            @endif
                            <div>
                                <div class="text-sm font-medium {{ $ticket->is_unread_by_staff && !$archivedFilter ? 'text-red-700' : 'text-gray-900' }}">{{ $ticket->subject }}</div>
                                <div class="text-xs text-gray-500">{{ ucfirst($ticket->category) }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $ticket->user_name }}</div>
                        @if($ticket->tenant_id)
                        <div class="text-xs text-gray-500">
                            @php $tenant = \App\Models\Tenant::find($ticket->tenant_id); @endphp
                            {{ $tenant?->name ?? 'N/A' }}
                            @if($tenant && $tenant->status === 'suspended')
                            <span class="text-orange-600 font-medium">(suspendu)</span>
                            @endif
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $priorityColors = [
                                'low' => 'bg-gray-100 text-gray-600',
                                'normal' => 'bg-blue-100 text-blue-700',
                                'high' => 'bg-orange-100 text-orange-700',
                                'urgent' => 'bg-red-100 text-red-700',
                            ];
                        @endphp
                        <span class="px-2 py-1 text-xs rounded-full {{ $priorityColors[$ticket->priority] ?? '' }}">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusColors = [
                                'open' => 'bg-blue-100 text-blue-700',
                                'in_progress' => 'bg-orange-100 text-orange-700',
                                'resolved' => 'bg-green-100 text-green-700',
                                'closed' => 'bg-gray-100 text-gray-500',
                            ];
                            $statusLabels = [
                                'open' => 'Ouvert',
                                'in_progress' => 'En cours',
                                'resolved' => 'Résolu',
                                'closed' => 'Fermé',
                            ];
                        @endphp
                        <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$ticket->status] ?? '' }}">
                            {{ $statusLabels[$ticket->status] ?? ucfirst($ticket->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $ticket->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('central.support.show', $ticket) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-eye"></i> Voir
                        </a>
                        @if($ticket->is_archived)
                        <form action="{{ route('central.support.unarchive', $ticket) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-900" title="Désarchiver">
                                <i class="fas fa-box-open"></i>
                            </button>
                        </form>
                        @else
                        <form action="{{ route('central.support.archive', $ticket) }}" method="POST" class="inline" onsubmit="return confirm('Archiver ce ticket ?')">
                            @csrf
                            <button type="submit" class="text-gray-500 hover:text-gray-700" title="Archiver">
                                <i class="fas fa-archive"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                        <p>{{ $archivedFilter ? 'Aucun ticket archivé' : 'Aucun ticket pour le moment' }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($tickets->hasPages())
        <div class="px-6 py-3 border-t border-gray-200 bg-gray-50">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
