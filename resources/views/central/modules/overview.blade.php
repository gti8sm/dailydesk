@extends('layouts.app')

@section('title', 'Gestion des Modules')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-puzzle-piece mr-2 text-blue-600"></i>Gestion des Modules
            </h1>
            <p class="mt-1 text-sm text-gray-600">Vue d'ensemble des modules par tenant</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('central.modules.settings') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <i class="fas fa-cog mr-2"></i>Paramètres globaux
            </a>
            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded">
        <p class="text-sm text-red-700">{{ session('error') }}</p>
    </div>
    @endif

    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                    @foreach($modules as $key => $module)
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <i class="{{ $module['icon'] }} mr-1"></i>{{ $module['label'] }}
                    </th>
                    @endforeach
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($tenants as $tenant)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-bold text-sm">{{ strtoupper(substr($tenant->name, 0, 1)) }}</span>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">{{ $tenant->name }}</div>
                                <div class="text-xs text-gray-500">{{ $tenant->domains->first()?->domain ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    @foreach($modules as $key => $module)
                    @php $enabled = in_array($key, $tenant->modules_enabled ?? []); @endphp
                    <td class="px-6 py-4 text-center">
                        <form action="{{ route('central.modules.toggle', ['tenant' => $tenant->id, 'module' => $key]) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                class="px-3 py-1 rounded-full text-xs font-semibold transition-colors {{
                                    $enabled
                                    ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                    : 'bg-gray-100 text-gray-500 hover:bg-gray-200'
                                }}">
                                @if($enabled)
                                <i class="fas fa-check-circle mr-1"></i>Activé
                                @else
                                <i class="fas fa-times-circle mr-1"></i>Désactivé
                                @endif
                            </button>
                        </form>
                    </td>
                    @endforeach
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                            {{ count($tenant->modules_enabled ?? []) }}/{{ count($modules) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ count($modules) + 2 }}" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-puzzle-piece text-4xl text-gray-300 mb-3"></i>
                        <p>Aucun tenant trouvé</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-{{ count($modules) }} gap-4">
        @foreach($modules as $key => $module)
        <div class="bg-white shadow rounded-lg p-5">
            <div class="flex items-center mb-2">
                <div class="bg-{{ $module['color'] }}-100 rounded-full p-2">
                    <i class="{{ $module['icon'] }} text-{{ $module['color'] }}-600 text-lg"></i>
                </div>
                <h3 class="ml-3 font-semibold text-gray-900">{{ $module['label'] }}</h3>
            </div>
            <p class="text-sm text-gray-600 mb-3">{{ $module['description'] }}</p>
            <p class="text-xs text-gray-500">
                Activé pour <strong>{{ $tenants->filter(fn($t) => in_array($key, $t->modules_enabled ?? []))->count() }}</strong> tenant(s) sur {{ $tenants->count() }}
            </p>
        </div>
        @endforeach
    </div>
</div>
@endsection
