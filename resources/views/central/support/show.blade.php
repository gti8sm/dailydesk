@extends('layouts.app')

@section('title', 'Ticket - ' . $ticket->subject)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('central.support.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Retour aux tickets
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white shadow-lg rounded-xl overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold text-gray-900">{{ $ticket->subject }}</h1>
                <div class="flex gap-2">
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
                    <span class="px-3 py-1 text-xs rounded-full {{ $statusColors[$ticket->status] ?? '' }}">{{ $statusLabels[$ticket->status] ?? ucfirst($ticket->status) }}</span>
                    @if($ticket->is_archived)
                    <span class="px-3 py-1 text-xs rounded-full bg-gray-200 text-gray-600"><i class="fas fa-archive mr-1"></i>Archivé</span>
                    @endif
                </div>
            </div>
            <div class="mt-2 flex flex-wrap gap-3 text-sm text-gray-500">
                <span><i class="fas fa-user mr-1"></i> {{ $ticket->user_name }}</span>
                @if($ticket->tenant)
                <span><i class="fas fa-building mr-1"></i> {{ $ticket->tenant->name }}
                    @if($ticket->tenant->status === 'suspended')
                    <span class="text-orange-600 font-medium">(suspendu)</span>
                    @endif
                </span>
                @endif
                <span><i class="fas fa-tag mr-1"></i> {{ ucfirst($ticket->category) }}</span>
                <span><i class="fas fa-flag mr-1"></i> {{ ucfirst($ticket->priority) }}</span>
                <span><i class="fas fa-clock mr-1"></i> {{ $ticket->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>
        <div class="px-6 py-3 border-t border-gray-200 bg-gray-50 flex gap-2">
            @if($ticket->is_archived)
            <form action="{{ route('central.support.unarchive', $ticket) }}" method="POST">
                @csrf
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-box-open mr-1"></i> Désarchiver et rouvrir
                </button>
            </form>
            @else
            <form action="{{ route('central.support.archive', $ticket) }}" method="POST" onsubmit="return confirm('Archiver ce ticket ? Il sera marqué comme fermé.')">
                @csrf
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-archive mr-1"></i> Archiver
                </button>
            </form>
            @endif
        </div>
        <div class="p-6">
            <p class="text-gray-700 whitespace-pre-line">{{ $ticket->description }}</p>
            @if(!empty($ticket->attachments))
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach($ticket->attachments as $attachment)
                <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 px-3 py-2 rounded-lg text-sm text-gray-700">
                    @if(str_starts_with(mime_content_type(storage_path('app/public/' . $attachment['path'])), 'image/'))
                    <img src="{{ asset('storage/' . $attachment['path']) }}" alt="{{ $attachment['name'] }}" class="w-12 h-12 object-cover rounded">
                    @else
                    <i class="fas fa-file text-gray-400 text-xl"></i>
                    @endif
                    <span class="truncate max-w-[150px]">{{ $attachment['name'] }}</span>
                </a>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <div class="space-y-4 mb-6">
        <h3 class="text-lg font-semibold text-gray-900">
            <i class="fas fa-comments mr-2"></i> Discussion ({{ $ticket->comments->count() }})
        </h3>
        @foreach($ticket->comments as $comment)
        <div class="bg-white shadow rounded-lg p-4 {{ $comment->is_staff ? 'border-l-4 border-blue-500' : 'border-l-4 border-gray-300' }}">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <div class="{{ $comment->is_staff ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }} rounded-full w-8 h-8 flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr($comment->user_name, 0, 1)) }}
                    </div>
                    <span class="font-medium text-sm text-gray-900">{{ $comment->user_name }}</span>
                    @if($comment->is_staff)
                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Staff</span>
                    @endif
                </div>
                <span class="text-xs text-gray-400">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $comment->content }}</p>
            @if(!empty($comment->attachments))
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($comment->attachments as $attachment)
                <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 px-3 py-2 rounded-lg text-sm text-gray-700">
                    @if(str_starts_with(mime_content_type(storage_path('app/public/' . $attachment['path'])), 'image/'))
                    <img src="{{ asset('storage/' . $attachment['path']) }}" alt="{{ $attachment['name'] }}" class="w-12 h-12 object-cover rounded">
                    @else
                    <i class="fas fa-file text-gray-400 text-xl"></i>
                    @endif
                    <span class="truncate max-w-[150px]">{{ $attachment['name'] }}</span>
                </a>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>

    @if(in_array($ticket->status, ['open', 'in_progress']))
    <div class="bg-white shadow-lg rounded-xl p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Répondre</h3>
        <form action="{{ route('central.support.comment', $ticket) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                <textarea name="content" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Votre réponse..."></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pièces jointes</label>
                <input type="file" name="attachments[]" multiple accept="image/*,application/pdf,.doc,.docx" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                <p class="mt-1 text-xs text-gray-500">JPG, PNG, GIF, WebP, PDF, DOC — 5 Mo max par fichier</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Changer le statut</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Ne pas changer</option>
                    <option value="in_progress" @if($ticket->status === 'in_progress') selected @endif>En cours</option>
                    <option value="resolved">Résolu</option>
                    <option value="closed">Fermé</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                <i class="fas fa-paper-plane mr-2"></i> Envoyer
            </button>
        </form>
    </div>
    @else
    <div class="bg-gray-50 rounded-lg p-4 text-center text-gray-500">
        <i class="fas fa-check-circle text-green-400 text-2xl mb-2"></i>
        <p>Ce ticket est {{ $statusLabels[$ticket->status] ?? 'fermé' }}.</p>
        <a href="{{ route('central.support.comment', $ticket) }}" class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">
            Rouvrir le ticket
        </a>
    </div>
    @endif
</div>
@endsection
