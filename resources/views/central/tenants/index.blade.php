@extends('layouts.app')

@section('title', 'Gestion des tenants')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-building mr-2"></i>Tenants (Mairies)
        </h1>
        <a href="{{ route('central.tenants.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Nouveau tenant
        </a>
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

    <div class="mb-4 flex flex-wrap gap-2">
        @php
            $tabs = [
                'all' => 'Tous',
                'active' => 'Actifs',
                'prospect' => 'Prospects',
                'suspended' => 'Suspendus',
            ];
        @endphp
        @foreach($tabs as $key => $label)
        <a href="{{ route('central.tenants.index', ['status' => $key]) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
           {{ $statusFilter === $key ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
            {{ $label }}
            <span class="ml-1 px-1.5 py-0.5 rounded-full text-xs {{ $statusFilter === $key ? 'bg-blue-500' : 'bg-gray-200' }}">{{ $statusCounts[$key] ?? 0 }}</span>
        </a>
        @endforeach
    </div>

    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($tenants as $tenant)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $tenant->name }}</div>
                        <div class="text-xs text-gray-500">{{ $tenant->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <a href="{{ url('/' . $tenant->slug) }}" target="_blank" class="text-blue-600 hover:text-blue-800">/{{ $tenant->slug }}</a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $tenant->subscription_plan === 'premium' ? 'bg-purple-100 text-purple-700' : ($tenant->subscription_plan === 'pro' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ ucfirst($tenant->subscription_plan) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusColors = [
                                'active' => 'bg-green-100 text-green-700',
                                'suspended' => 'bg-red-100 text-red-700',
                                'prospect' => 'bg-yellow-100 text-yellow-700',
                                'cancelled' => 'bg-gray-100 text-gray-500',
                            ];
                            $statusLabels = [
                                'active' => 'Actif',
                                'suspended' => 'Suspendu',
                                'prospect' => 'Prospect',
                                'cancelled' => 'Annulé',
                            ];
                            $status = $tenant->status;
                        @endphp
                        <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $statusLabels[$status] ?? ucfirst($status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('central.tenants.show', $tenant) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('central.tenants.edit', $tenant) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('central.tenants.toggle-status', $tenant) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                <i class="fas fa-power-off"></i>
                            </button>
                        </form>
                        <form action="{{ route('central.tenants.destroy', $tenant) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce tenant et toutes ses données ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-building text-4xl text-gray-300 mb-3"></i>
                        <p>Aucun tenant créé pour le moment</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tenants->hasPages())
    <div class="mt-4">
        {{ $tenants->links() }}
    </div>
    @endif
</div>
@endsection
