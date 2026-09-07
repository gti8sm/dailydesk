@extends('layouts.app')

@section('title', 'QR Codes - Invitations')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-qrcode text-indigo-600 mr-2"></i>
                QR Codes d'invitation
            </h1>
            <p class="mt-1 text-sm text-gray-600">Imprimez cette page et collez les QR codes dans les cahiers de liaison</p>
        </div>
        <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
            <i class="fas fa-print mr-2"></i>Imprimer
        </button>
    </div>

    @if($invitations->isEmpty())
    <div class="bg-white shadow rounded-lg p-12 text-center text-gray-500">
        <i class="fas fa-qrcode text-4xl text-gray-300 mb-2"></i>
        <p>Aucune invitation en attente. Envoyez d'abord des invitations.</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 print:grid-cols-3">
        @foreach($invitations as $invitation)
        <div class="bg-white border-2 border-gray-200 rounded-xl p-6 text-center print:break-inside-avoid">
            <h3 class="font-bold text-lg text-gray-900 mb-1">Famille {{ $invitation->family->family_name }}</h3>
            <p class="text-xs text-gray-500 mb-4">Expire le {{ $invitation->expires_at->format('d/m/Y') }}</p>
            <div id="qr-{{ $invitation->id }}" class="flex justify-center mb-3" data-url="{{ url('/invitation/accept/' . $invitation->token) }}"></div>
            <p class="text-xs text-gray-400">Scannez pour créer votre compte parent</p>
        </div>
        @endforeach
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[id^="qr-"]').forEach(function(el) {
        var url = el.dataset.url;
        new QRCode(el, {
            text: url,
            width: 160,
            height: 160,
            colorDark: '#000000',
            colorLight: '#ffffff',
        });
    });
});
</script>
@endpush
@endsection
