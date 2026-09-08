@extends('layouts.app')

@section('title', 'Détail du log')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-history mr-2 text-indigo-600"></i>Détail du log
        </h1>
        <a href="{{ route('central.logs.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
            <i class="fas fa-arrow-left mr-2"></i>Retour
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-xl p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Date</label>
                <p class="text-sm text-gray-900">{{ $log->created_at?->format('d/m/Y à H:i:s') ?? '—' }}</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Action</label>
                <p class="text-sm">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">{{ $log->action }}</span>
                </p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Utilisateur</label>
                <p class="text-sm text-gray-900">{{ $log->user_name ?? 'Système' }}</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Tenant</label>
                <p class="text-sm text-gray-900">{{ $log->tenant?->name ?? 'Global / N/A' }}</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Modèle</label>
                <p class="text-sm text-gray-900">{{ $log->model_type ? class_basename($log->model_type) : '—' }} (#{{ $log->model_id }})</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Description</label>
                <p class="text-sm text-gray-900">{{ $log->description }}</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Adresse IP</label>
                <p class="text-sm text-gray-900 font-mono">{{ $log->ip_address ?? '—' }}</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">User Agent</label>
                <p class="text-sm text-gray-900 break-all">{{ $log->user_agent ?? '—' }}</p>
            </div>
        </div>

        @if($log->properties)
        <div class="mt-6">
            <label class="block text-xs font-medium text-gray-500 uppercase mb-2">Changements</label>
            <div class="bg-gray-50 rounded-lg p-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2 pr-4 text-gray-500">Champ</th>
                            <th class="text-left py-2 pr-4 text-gray-500">Ancienne valeur</th>
                            <th class="text-left py-2 text-gray-500">Nouvelle valeur</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($log->properties as $field => $change)
                        <tr class="border-b last:border-0">
                            <td class="py-2 pr-4 font-medium text-gray-700">{{ $field }}</td>
                            <td class="py-2 pr-4 text-red-600">{{ is_array($change['old']) ? json_encode($change['old']) : $change['old'] }}</td>
                            <td class="py-2 text-green-600">{{ is_array($change['new']) ? json_encode($change['new']) : $change['new'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
