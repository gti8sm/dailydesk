@extends('layouts.app')

@section('title', 'Message de ' . $message->name)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-envelope-open-text text-emerald-600 mr-2"></i>Message de contact
            </h1>
            <p class="mt-1 text-sm text-gray-600">Reçu le {{ $message->created_at->format('d/m/Y à H:i') }}</p>
        </div>
        <a href="{{ route('site.messages.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium text-sm">
            <i class="fas fa-arrow-left mr-2"></i>Retour
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-xl p-6 space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-6 border-b border-gray-100">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Expéditeur</p>
                <p class="text-sm font-semibold text-gray-900 mt-1">{{ $message->name }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Email</p>
                <p class="text-sm text-gray-700 mt-1">
                    <a href="mailto:{{ $message->email }}" class="text-emerald-600 hover:underline">{{ $message->email }}</a>
                </p>
            </div>
            @if($message->phone)
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Téléphone</p>
                <p class="text-sm text-gray-700 mt-1">{{ $message->phone }}</p>
            </div>
            @endif
            @if($message->subject)
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Sujet</p>
                <p class="text-sm text-gray-700 mt-1">{{ $message->subject }}</p>
            </div>
            @endif
        </div>

        <div>
            <p class="text-xs font-medium text-gray-500 uppercase mb-2">Message</p>
            <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $message->message }}</div>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <a href="mailto:{{ $message->email }}?subject=Re: {{ rawurlencode($message->subject ?: 'Votre message') }}"
               class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium text-sm">
                <i class="fas fa-reply mr-2"></i>Répondre par email
            </a>
            <form action="{{ route('site.messages.destroy', $message) }}" method="POST" onsubmit="return confirm('Supprimer ce message ?')">
                @csrf @method('DELETE')
                <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg font-medium text-sm">
                    <i class="fas fa-trash mr-2"></i>Supprimer
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
