@extends('layouts.app')

@section('title', 'RGPD — Registre des traitements')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('central.dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Retour au dashboard
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">
            <i class="fas fa-shield-halved text-indigo-600 mr-2"></i>RGPD
        </h1>
        <p class="mt-1 text-sm text-gray-600">Registre des traitements, durées de conservation et outils d'exercice des droits.</p>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
        <p class="text-sm text-green-700"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
        <p class="text-sm text-red-700"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</p>
    </div>
    @endif

    <!-- Registre des traitements -->
    <div class="bg-white shadow-lg rounded-xl p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4"><i class="fas fa-list-check text-indigo-600 mr-2"></i>Registre des traitements</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 uppercase border-b border-gray-200">
                        <th class="py-2 pr-4">Traitement</th>
                        <th class="py-2 pr-4">Finalité</th>
                        <th class="py-2 pr-4">Base légale</th>
                        <th class="py-2 pr-4">Durée de conservation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="py-2 pr-4 font-medium">Familles, parents, enfants</td>
                        <td class="py-2 pr-4">Gestion scolaire, cantine et garderie</td>
                        <td class="py-2 pr-4">Missions d'intérêt public (art. 6-1-e)</td>
                        <td class="py-2 pr-4">Durée de scolarité + 1 an</td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-4 font-medium">Présences cantine / garderie</td>
                        <td class="py-2 pr-4">Suivi des présences et facturation</td>
                        <td class="py-2 pr-4">Missions d'intérêt public</td>
                        <td class="py-2 pr-4">3 ans (purge via l'outil ci-dessous)</td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-4 font-medium">Données santé (allergies)</td>
                        <td class="py-2 pr-4">Sécurité alimentaire des enfants</td>
                        <td class="py-2 pr-4">Intérêt vital (art. 6-1-d) / santé publique</td>
                        <td class="py-2 pr-4">Durée de scolarité + 1 an</td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-4 font-medium">Comptes utilisateurs</td>
                        <td class="py-2 pr-4">Authentification des agents et parents</td>
                        <td class="py-2 pr-4">Missions d'intérêt public</td>
                        <td class="py-2 pr-4">Durée du contrat + 1 an</td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-4 font-medium">Journal d'activité</td>
                        <td class="py-2 pr-4">Traçabilité des opérations</td>
                        <td class="py-2 pr-4">Intérêt légitime (sécurité)</td>
                        <td class="py-2 pr-4">12 mois</td>
                    </tr>
                    <tr>
                        <td class="py-2 pr-4 font-medium">Messages du formulaire de contact (site public)</td>
                        <td class="py-2 pr-4">Traitement des demandes</td>
                        <td class="py-2 pr-4">Intérêt légitime</td>
                        <td class="py-2 pr-4">12 mois</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="text-xs text-gray-400 mt-3">
            Ce registre est un modèle à adapter et compléter par chaque commune (responsable de traitement). {{ $tenants }} commune(s) hébergée(s) sur cette instance.
        </p>
    </div>

    <!-- Exercice des droits -->
    <div class="bg-white shadow-lg rounded-xl p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4"><i class="fas fa-user-shield text-indigo-600 mr-2"></i>Exercice des droits (inclus dans le produit)</h2>
        <ul class="space-y-2 text-sm text-gray-700">
            <li class="flex gap-2"><i class="fas fa-check text-green-500 mt-1"></i>
                <span><strong>Droit d'accès / portabilité (art. 15-20)</strong> — bouton « Export RGPD » sur chaque fiche famille : export JSON complet (famille, parents, enfants, présences), réalisable par l'admin mairie en autonomie.</span>
            </li>
            <li class="flex gap-2"><i class="fas fa-check text-green-500 mt-1"></i>
                <span><strong>Droit à l'effacement (art. 17)</strong> — purge définitive d'une famille (parents, enfants, présences, comptes portail) avec confirmation renforcée, depuis la fiche famille.</span>
            </li>
            <li class="flex gap-2"><i class="fas fa-check text-green-500 mt-1"></i>
                <span><strong>Droit de rectification (art. 16)</strong> — toutes les fiches sont modifiables par l'admin mairie.</span>
            </li>
        </ul>
    </div>

    <!-- Purge durée de conservation -->
    <div class="bg-white shadow-lg rounded-xl p-6 mb-6 border border-orange-100">
        <h2 class="text-lg font-bold text-gray-900 mb-2"><i class="fas fa-clock-rotate-left text-orange-500 mr-2"></i>Durée de conservation — purge des anciennes présences</h2>
        <p class="text-sm text-gray-500 mb-4">
            Supprime définitivement les présences cantine et garderie au-delà de la durée de conservation retenue, pour toutes les communes.
            Action recommandée une fois par an, à inscrire au calendrier de la personne en charge du RGPD.
        </p>
        <form action="{{ route('central.rgpd.purge-presences') }}" method="POST" class="flex flex-wrap items-end gap-3">
            @csrf
            <div>
                <label class="block text-xs text-gray-600 mb-1">Conservation (années)</label>
                <input type="number" name="years" value="3" min="1" max="10" required
                       class="w-24 text-sm border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Confirmation</label>
                <input type="text" name="confirm" required autocomplete="off" spellcheck="false" placeholder="PURGER"
                       class="w-32 text-sm border-orange-300 rounded-lg font-mono uppercase">
            </div>
            <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium text-sm"
                    onclick="return confirm('Purger définitivement les présences au-delà de la durée de conservation ? Cette action est irréversible.')">
                <i class="fas fa-eraser mr-1"></i>Purger les anciennes présences
            </button>
        </form>
    </div>

    <!-- Annexe sous-traitant -->
    <div class="bg-white shadow-lg rounded-xl p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4"><i class="fas fa-file-contract text-indigo-600 mr-2"></i>Modèle d'annexe — protection des données</h2>
        <p class="text-sm text-gray-600 mb-3">
            À joindre aux devis/contrats des communes. Points à y faire figurer :
        </p>
        <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700">
            <li><strong>Qualité</strong> : la commune est responsable de traitement ; l'éditeur agit en qualité de sous-traitant (art. 28 RGPD) pour l'hébergement et la maintenance de l'outil.</li>
            <li><strong>Finalités</strong> : uniquement la gestion scolaire, périscolaire et la communication municipale, conformément aux instructions documentées de la commune.</li>
            <li><strong>Hébergement</strong> : serveurs localisés en France (Union européenne), aucune donnée transférée hors UE.</li>
            <li><strong>Mesures de sécurité</strong> : chiffrement des échanges (HTTPS), authentification par mot de passe, journalisation des accès, sauvegardes quotidiennes chiffrées, gestion des habilitations par rôles.</li>
            <li><strong>Assistance</strong> : l'éditeur assiste la commune dans l'exercice des droits des personnes (accès, rectification, effacement) via les outils intégrés.</li>
            <li><strong>Sous-traitants ultérieurs</strong> : l'hébergeur (o2switch — France). Notification préalable de tout changement.</li>
            <li><strong>Durée et fin</strong> : la prestation prend fin à la résiliation ; les données sont restituées (export) puis supprimées sous 30 jours.</li>
            <li><strong>Audit</strong> : la commune peut demander la politique de sécurité et les registres d'accès.</li>
        </ol>
        <p class="text-xs text-gray-400 mt-3">
            Ce modèle est fourni à titre indicatif — faites le valider par votre conseil habituel avant signature.
        </p>
    </div>
</div>
@endsection
