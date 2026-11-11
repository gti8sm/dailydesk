@extends('layouts.guest')

@section('title', 'Vérification en deux étapes')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div>
        <a href="{{ route('login') }}">
            <h1 class="text-3xl font-bold text-blue-600">{{ config('app.name', 'DailyDesk') }}</h1>
        </a>
    </div>

    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="mb-4 text-center">
            <i class="fas fa-shield-halved text-3xl text-blue-600 mb-2"></i>
            <h2 class="text-xl font-semibold text-gray-900">Vérification en deux étapes</h2>
            <p class="text-sm text-gray-500 mt-1">
                Un code à 6 chiffres vient de vous être envoyé par email.
                Il est valable 10 minutes.
            </p>
        </div>

        @if ($errors->any())
        <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-3 rounded">
            <ul class="text-sm text-red-700 list-disc list-inside">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('login.twofactor.verify') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Code de vérification</label>
                <input type="text" id="code" name="code" required inputmode="numeric" pattern="[0-9]{6}"
                       maxlength="6" autocomplete="one-time-code" autofocus
                       placeholder="••••••"
                       class="block w-full text-center text-2xl tracking-[0.5em] font-mono rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <button type="submit"
                    class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-unlock mr-2"></i>Vérifier et me connecter
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left mr-1"></i>Revenir à la connexion
            </a>
        </div>
    </div>
</div>
@endsection
