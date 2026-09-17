@extends('layouts.app')

@section('title', 'Mes tickets de support')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-life-ring text-orange-600 mr-2"></i> Support
        </h1>
        <a href="{{ route('support.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg font-medium">
            <i class="fas fa-plus mr-2"></i> Nouveau ticket
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sujet</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($tickets as $ticket)
                <tr class="hover:bg-gray-50 {{ $ticket->is_unread_by_user ? 'bg-orange-50' : '' }}">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @if($ticket->is_unread_by_user)
                            <span class="w-2 h-2 bg-orange-500 rounded-full mr-2 flex-shrink-0" title="Nouvelle réponse"></span>
                            @endif
                            <div>
                                <div class="text-sm font-medium {{ $ticket->is_unread_by_user ? 'text-orange-700' : 'text-gray-900' }}">{{ $ticket->subject }}</div>
                                <div class="text-xs text-gray-500">{{ ucfirst($ticket->priority) }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ ucfirst($ticket->category) }}
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
                        <a href="{{ route('support.show', $ticket) }}" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-eye"></i> Voir
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                        <p>Aucun ticket pour le moment</p>
                        <a href="{{ route('support.create') }}" class="text-orange-600 hover:text-orange-800 text-sm mt-2 inline-block">Créer un ticket</a>
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
