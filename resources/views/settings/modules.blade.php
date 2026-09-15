@extends('layouts.app')

@section('title', 'Gestion des Modules')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('settings.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour aux paramètres
        </a>
    </div>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-puzzle-piece mr-2 text-blue-600"></i>Gestion des Modules
        </h1>
        <p class="mt-2 text-gray-600">Activez ou désactivez les modules disponibles pour votre mairie.</p>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
        <p class="text-sm text-red-700">{{ session('error') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($modules as $key => $module)
        @php $enabled = in_array($key, $tenant->modules_enabled ?? []); @endphp
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border {{ $enabled ? 'border-green-300' : 'border-gray-200' }}">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-center">
                        <div class="bg-{{ $module['color'] }}-100 rounded-2xl w-14 h-14 flex items-center justify-center">
                            <i class="{{ $module['icon'] }} text-{{ $module['color'] }}-600 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-bold text-lg text-gray-900">{{ $module['label'] }}</h3>
                            <span class="text-xs {{ $enabled ? 'text-green-600' : 'text-gray-400' }} font-medium">
                                {{ $enabled ? 'Activé' : 'Désactivé' }}
                            </span>
                        </div>
                    </div>
                    <form action="{{ route('tenant.modules.toggle', $key) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                class="relative inline-flex h-7 w-12 items-center rounded-full transition-colors {{ $enabled ? 'bg-green-500' : 'bg-gray-300' }}">
                            <span class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform {{ $enabled ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </form>
                </div>
                <p class="text-sm text-gray-600 mt-4">{{ $module['description'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
