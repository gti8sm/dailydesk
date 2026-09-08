@extends('layouts.app')

@section('title', 'Journal d\'activité')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-history mr-2 text-indigo-600"></i>Journal d'activité
            </h1>
            <p class="mt-1 text-sm text-gray-600">Traçabilité des actions sur la plateforme</p>
        </div>
        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
            <i class="fas fa-arrow-left mr-2"></i>Retour
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-xl p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Tenant</label>
                <select name="tenant_id" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                    <option value="">Tous</option>
                    @foreach($tenants as $tenant)
                    <option value="{{ $tenant->id }}" {{ request('tenant_id') == $tenant->id ? 'selected' : '' }}>{{ $tenant->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Action</label>
                <select name="action" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
                    <option value="">Toutes</option>
                    @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Du</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Au</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border-gray-300 shadow-sm text-sm">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
                    <i class="fas fa-filter mr-1"></i>Filtrer
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tenant</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                        {{ $log->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                        {{ $log->user_name ?? 'Système' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @php
                            $colors = [
                                'created' => 'bg-green-100 text-green-700',
                                'updated' => 'bg-blue-100 text-blue-700',
                                'deleted' => 'bg-red-100 text-red-700',
                                'login' => 'bg-purple-100 text-purple-700',
                                'logout' => 'bg-gray-100 text-gray-700',
                            ];
                            $icons = [
                                'created' => 'fa-plus',
                                'updated' => 'fa-edit',
                                'deleted' => 'fa-trash',
                                'login' => 'fa-sign-in-alt',
                                'logout' => 'fa-sign-out-alt',
                            ];
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $colors[$log->action] ?? 'bg-gray-100 text-gray-700' }}">
                            <i class="fas {{ $icons[$log->action] ?? 'fa-circle' }} mr-1"></i>{{ $log->action }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                        <a href="{{ route('central.logs.show', $log) }}" class="hover:text-indigo-600 hover:underline">
                            {{ $log->description }}
                        </a>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                        {{ $log->tenant?->name ?? '—' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 font-mono">
                        {{ $log->ip_address ?? '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                        <i class="fas fa-history text-4xl text-gray-300 mb-3"></i>
                        <p>Aucune activité enregistrée</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $logs->links() }}
    </div>
</div>
@endsection
