@extends('layouts.app')

@section('title', 'Messages de contact')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-envelope text-emerald-600 mr-2"></i>Messages de contact
            </h1>
            <p class="mt-1 text-sm text-gray-600">Messages envoyés via le formulaire de contact du site public.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('site.messages.index') }}"
               class="px-4 py-2 rounded-lg font-medium text-sm transition-colors {{ request('filter') !== 'unread' ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Tous
            </a>
            <a href="{{ route('site.messages.index', ['filter' => 'unread']) }}"
               class="px-4 py-2 rounded-lg font-medium text-sm transition-colors {{ request('filter') === 'unread' ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Non lus
                @if($unreadCount > 0)
                <span class="ml-1 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-500 text-white">{{ $unreadCount }}</span>
                @endif
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expéditeur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sujet</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reçu le</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($messages as $message)
                <tr class="hover:bg-gray-50 {{ $message->is_read ? '' : 'bg-blue-50/50' }}">
                    <td class="px-6 py-4">
                        @if($message->is_read)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Lu</span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-circle text-[6px] mr-1.5"></i>Non lu
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-medium text-gray-900">{{ $message->name }}</p>
                        <p class="text-xs text-gray-500">{{ $message->email }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700 max-w-xs">
                        <p class="truncate">{{ $message->subject ?: '—' }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                        {{ $message->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('site.messages.show', $message) }}" class="text-emerald-600 hover:text-emerald-800 mr-3" title="Lire">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="mailto:{{ $message->email }}?subject=Re: {{ rawurlencode($message->subject ?: 'Votre message') }}" class="text-blue-600 hover:text-blue-800 mr-3" title="Répondre par email">
                            <i class="fas fa-reply"></i>
                        </a>
                        <form action="{{ route('site.messages.destroy', $message) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce message ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                        <i class="fas fa-envelope text-4xl mb-3"></i>
                        <p>Aucun message {{ request('filter') === 'unread' ? 'non lu' : '' }} pour le moment.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $messages->links() }}
    </div>
</div>
@endsection
