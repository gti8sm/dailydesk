@extends('layouts.app')

@section('title', 'Invitation expirée')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full bg-white shadow-xl rounded-2xl p-8 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-3">
            <i class="fas fa-clock text-3xl text-red-600"></i>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Invitation expirée</h1>
        <p class="text-gray-600">Ce lien d'invitation n'est plus valide ou a déjà été utilisé.</p>
        <p class="text-sm text-gray-400 mt-4">Contactez la mairie pour obtenir une nouvelle invitation.</p>
    </div>
</div>
@endsection
