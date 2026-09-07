@extends('layouts.app')

@section('title', 'Invitations Familles')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-envelope-open-text text-indigo-600 mr-2"></i>
                Invitations Familles
            </h1>
            <p class="mt-1 text-sm text-gray-600">Envoyer des invitations aux familles pour créer leur compte parent</p>
        </div>
        <a href="{{ route('invitations.qrcodes') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
            <i class="fas fa-qrcode mr-2"></i>QR Codes
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Formulaire d'envoi en lot -->
    <div class="bg-white shadow-lg rounded-xl p-6 mb-6">
        <form action="{{ route('invitations.send') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Sélectionnez les familles à inviter</label>
                <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-3 space-y-2">
                    @foreach($families as $family)
                    <label class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded">
                        <input type="checkbox" name="family_ids[]" value="{{ $family->id }}"
                               class="h-5 w-5 text-indigo-600 rounded border-gray-300">
                        <div>
                            <span class="font-medium text-gray-900">{{ $family->family_name }}</span>
                            <span class="text-sm text-gray-500 ml-2">{{ $family->email ?: '(pas d\'email)' }}</span>
                            <span class="text-xs text-gray-400 ml-2">{{ $family->children->count() }} enfant(s)</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="mb-4">
                <label for="expiry_days" class="block text-sm font-medium text-gray-700 mb-2">Durée de validité (jours)</label>
                <input type="number" name="expiry_days" id="expiry_days" value="30" min="1" max="90"
                       class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                <i class="fas fa-paper-plane mr-2"></i>Envoyer les invitations
            </button>
        </form>
    </div>

    <!-- Historique -->
    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Historique des invitations</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Famille</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expire le</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($invitations as $inv)
                <tr>
                    <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $inv->family->family_name }}</td>
                    <td class="px-6 py-3 text-sm text-gray-500">{{ $inv->email }}</td>
                    <td class="px-6 py-3">
                        @if($inv->status === 'accepted')
                            <span class="px-2 text-xs font-semibold rounded-full bg-green-100 text-green-800">Acceptée</span>
                        @elseif($inv->isExpired())
                            <span class="px-2 text-xs font-semibold rounded-full bg-red-100 text-red-800">Expirée</span>
                        @else
                            <span class="px-2 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">En attente</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-500">{{ $inv->expires_at?->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Aucune invitation envoyée</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
