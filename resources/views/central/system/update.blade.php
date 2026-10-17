@extends('layouts.app')

@section('title', 'Système — Mise à jour')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('central.dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Retour au dashboard
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">
            <i class="fas fa-rocket text-blue-600 mr-2"></i>Mise à jour de la plateforme
        </h1>
        <p class="mt-1 text-sm text-gray-600">Mettez DailyDesk à jour depuis la dernière version publiée sur GitHub, sans terminal.</p>
    </div>

    @if(session('error'))
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
        <p class="text-sm text-red-700"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</p>
    </div>
    @endif

    <div class="bg-white shadow-lg rounded-xl p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-3"><i class="fas fa-code-branch text-blue-600 mr-2"></i>Version installée</h2>
        @if($current)
        <p class="font-mono text-sm bg-gray-100 rounded-lg px-4 py-3">
            <i class="fas fa-check-circle text-green-500 mr-2"></i>{{ $current }}
        </p>
        @else
        <p class="text-sm text-gray-500">Version inconnue (dépôt Git non accessible depuis le web).</p>
        @endif

        <div class="mt-4 flex flex-wrap gap-3">
            <form action="{{ route('central.system.check') }}" method="POST">
                @csrf
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i>Vérifier les mises à jour
                </button>
            </form>
        </div>

        @if($updateAvailable !== null && session('remote_info'))
        @php $remote = session('remote_info'); @endphp
        <div class="mt-6 {{ $updateAvailable ? 'bg-orange-50 border-l-4 border-orange-500' : 'bg-green-50 border-l-4 border-green-500' }} p-4 rounded">
            @if($updateAvailable)
            <p class="font-semibold text-orange-800">
                <i class="fas fa-arrow-circle-up mr-2"></i>Mise à jour disponible !
            </p>
            <p class="text-sm text-orange-700 mt-1">
                Local : <span class="font-mono">{{ $remote['local'] }}</span> →
                Distant : <span class="font-mono">{{ $remote['remote'] }}</span>
            </p>
            <p class="text-sm text-orange-600 mt-1 italic">« {{ $remote['commit'] }} »</p>

            <form action="{{ route('central.system.update') }}" method="POST" class="mt-4"
                  onsubmit="return confirm('⚠️ Lancer la mise à jour ?\n\nLe site passera brièvement en mode maintenance (quelques secondes à 1 minute selon les migrations).\nLes utilisateurs verront une page « en maintenance » pendant l\'opération.')">
                @csrf
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white rounded-xl font-bold transition-all shadow-lg">
                    <i class="fas fa-rocket mr-2"></i>Mettre à jour maintenant
                </button>
            </form>
            @else
            <p class="font-semibold text-green-800">
                <i class="fas fa-check-circle mr-2"></i>Vous êtes à jour ({{ $remote['local'] }})
            </p>
            @endif
        </div>
        @endif

        @if(session('dry_run'))
        @php $dry = session('dry_run'); @endphp
        <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>Simulation — local <span class="font-mono">{{ $dry['local'] }}</span>,
                distant <span class="font-mono">{{ $dry['remote'] }}</span>. Étapes qui seraient exécutées :
            </p>
            <ul class="mt-2 list-disc list-inside text-sm text-blue-700">
                @foreach($dry['steps'] as $step)
                <li>{{ $step }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    @if(session('update_results'))
    <div class="bg-white shadow-lg rounded-xl p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">
            <i class="fas fa-terminal text-gray-600 mr-2"></i>Résultat de la mise à jour
            @if(session('update_failed'))
            <span class="text-red-600">— échec</span>
            @else
            <span class="text-green-600">— succès</span>
            @endif
        </h2>

        @if(session('new_version'))
        <p class="mb-4 font-mono text-sm bg-green-50 rounded-lg px-4 py-3 border border-green-200">
            <i class="fas fa-check-circle text-green-500 mr-2"></i>Nouvelle version : {{ session('new_version') }}
        </p>
        @endif

        <div class="space-y-3">
            @foreach(session('update_results') as $result)
            <div class="rounded-lg border {{ $result['success'] ? 'border-green-200' : 'border-red-200' }} overflow-hidden">
                <div class="{{ $result['success'] ? 'bg-green-50' : 'bg-red-50' }} px-4 py-2 flex items-center justify-between">
                    <p class="text-sm font-semibold {{ $result['success'] ? 'text-green-800' : 'text-red-800' }}">
                        <i class="fas {{ $result['success'] ? 'fa-check-circle' : 'fa-times-circle' }} mr-2"></i>{{ $result['label'] }}
                    </p>
                </div>
                @if($result['output'])
                <pre class="text-xs text-gray-600 bg-gray-900 text-gray-100 p-3 overflow-x-auto">{{ $result['output'] }}</pre>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded text-sm text-yellow-800">
        <p class="font-semibold mb-2"><i class="fas fa-exclamation-triangle mr-2"></i>Prérequis sur cet hébergement</p>
        <ul class="list-disc list-inside space-y-1">
            <li>Le dépôt Git doit être cloné avec une clé SSH accessible à l'utilisateur web (o2switch : oui, même compte)</li>
            <li>Composer optionnel : s'il est absent du PATH web, l'étape est signalée sans bloquer</li>
            <li>Faites une <strong>sauvegarde</strong> avant la mise à jour (cp -r du dossier + dump SQL)</li>
        </ul>
    </div>
</div>
@endsection
