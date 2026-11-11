@extends('layouts.app')

@section('title', $family->family_name)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('families.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Retour à la liste
        </a>
        <div class="flex gap-2">
            <a href="{{ route('families.edit', $family) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                <i class="fas fa-edit mr-1"></i>Modifier
            </a>
            <a href="{{ route('families.export-rgpd', $family) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200" title="Export RGPD — droit d'accès (JSON)">
                <i class="fas fa-shield-halved mr-1"></i>Export RGPD
            </a>
            <form action="{{ route('families.destroy', $family) }}" method="POST" onsubmit="return confirm('Supprimer cette famille ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-trash mr-1"></i>Supprimer
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Informations famille -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-users text-purple-600 mr-2"></i>
                {{ $family->family_name }}
            </h1>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Adresse</p>
                <p class="text-sm font-medium text-gray-900">{{ $family->address ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Téléphone</p>
                <p class="text-sm font-medium text-gray-900">{{ $family->phone ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Email</p>
                <p class="text-sm font-medium text-gray-900">{{ $family->email ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Ville</p>
                <p class="text-sm font-medium text-gray-900">{{ $family->postal_code ?? '-' }} {{ $family->city ?? '-' }}</p>
            </div>
            @if($family->notes)
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500">Notes</p>
                <p class="text-sm font-medium text-gray-900">{{ $family->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Parents / Tuteurs -->
    @if($family->parents->isNotEmpty())
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">
                <i class="fas fa-user-friends text-purple-600 mr-2"></i>
                Parents / Tuteurs ({{ $family->parents->count() }})
            </h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($family->parents as $parent)
            <div class="p-4 border rounded-lg {{ $parent->is_primary_contact ? 'border-purple-300 bg-purple-50' : 'border-gray-200 bg-gray-50' }}">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-gray-900">{{ $parent->full_name }}</span>
                        <span class="px-2 py-0.5 text-xs rounded-full
                            {{ $parent->relationship === 'mother' ? 'bg-pink-100 text-pink-700' : '' }}
                            {{ $parent->relationship === 'father' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $parent->relationship === 'guardian' ? 'bg-orange-100 text-orange-700' : '' }}
                            {{ $parent->relationship === 'other' ? 'bg-gray-100 text-gray-700' : '' }}">
                            {{ $parent->relationship_label }}
                        </span>
                    </div>
                    @if($parent->is_primary_contact)
                    <span class="text-xs text-purple-600 font-medium"><i class="fas fa-star mr-1"></i>Contact principal</span>
                    @endif
                </div>
                <div class="space-y-1 text-sm text-gray-600">
                    @if($parent->email)
                    <p><i class="fas fa-envelope mr-2 text-gray-400"></i>{{ $parent->email }}</p>
                    @endif
                    @if($parent->phone)
                    <p><i class="fas fa-phone mr-2 text-gray-400"></i>{{ $parent->phone }}</p>
                    @endif
                    @if($parent->mobile)
                    <p><i class="fas fa-mobile-alt mr-2 text-gray-400"></i>{{ $parent->mobile }}</p>
                    @endif
                    @if($parent->full_address)
                    <p><i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>{{ $parent->full_address }}</p>
                    @endif
                </div>
                <div class="mt-2 flex gap-3">
                    @if($parent->can_pickup)
                    <span class="text-xs text-green-600"><i class="fas fa-check-circle mr-1"></i>Autorisé à récupérer</span>
                    @endif
                    @if($parent->is_legal_guardian)
                    <span class="text-xs text-orange-600"><i class="fas fa-shield-alt mr-1"></i>Tuteur légal</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Enfants -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-900">
                <i class="fas fa-child text-blue-600 mr-2"></i>
                Enfants ({{ $family->children->count() }})
            </h2>
            <a href="{{ route('families.children.create', $family) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-1"></i>Ajouter un enfant
            </a>
        </div>
        <div class="p-6">
            @forelse($family->children as $child)
            <div class="mb-4 p-4 border rounded-lg hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="font-bold text-lg text-gray-900">{{ $child->full_name }}</h3>
                        <p class="text-sm text-gray-600">
                            Né(e) le {{ \Carbon\Carbon::parse($child->birth_date)->format('d/m/Y') }}
                            ({{ $child->age }} ans)
                        </p>
                        @if($child->class)
                        <p class="text-sm text-gray-600">Classe : {{ $child->class }}</p>
                        @endif
                        @if($child->allergies)
                        <p class="text-sm text-red-600 mt-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Allergies : {{ $child->allergies }}
                        </p>
                        @endif
                        @if($child->dietary_restrictions)
                        <p class="text-sm text-yellow-700 mt-1">
                            <i class="fas fa-info-circle mr-1"></i> Restrictions : {{ $child->dietary_restrictions }}
                        </p>
                        @endif
                    </div>
                    <div class="flex gap-2 ml-4">
                        <a href="{{ route('children.edit', $child) }}" class="text-purple-600 hover:text-purple-900" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('children.destroy', $child) }}" method="POST" onsubmit="return confirm('Supprimer cet enfant ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8">
                <i class="fas fa-child text-4xl text-gray-300 mb-2"></i>
                <p class="text-gray-500">Aucun enfant enregistré</p>
                <a href="{{ route('families.children.create', $family) }}" class="mt-2 inline-block text-blue-600 hover:text-blue-700">
                    Ajouter le premier enfant
                </a>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Zone RGPD — suppression définitive -->
    <div class="bg-white shadow rounded-lg p-6 border border-red-100" x-data="{ purging: false }">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">
            <i class="fas fa-shield-halved text-gray-400 mr-2"></i>RGPD — effacement définitif
        </h3>
        <p class="text-sm text-gray-500 mb-3">
            Réponse à un droit à l'effacement (RGPD art. 17) : supprime <strong>définitivement</strong> la famille,
            ses parents, ses enfants, leurs présences (cantine et garderie), les invitations et les comptes portail parent associés.
            Contrairement à la suppression classique (corbeille), cette action est irrécupérable.
        </p>
        <button type="button" @click="purging = !purging" class="text-red-600 hover:text-red-800 text-sm font-medium">
            <i class="fas fa-triangle-exclamation mr-1"></i>Purger définitivement cette famille...
        </button>
        <div x-show="purging" x-cloak class="mt-3 border-t border-gray-200 pt-3">
            <form action="{{ route('families.purge-rgpd', $family) }}" method="POST">
                @csrf
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                    <p class="text-xs text-red-800 leading-relaxed">
                        <strong>Action irréversible</strong> — toutes les données de cette famille seront effacées
                        sans possibilité de récupération. Un export RGPD préalable est recommandé si la mairie doit
                        conserver une trace.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <label class="text-xs text-gray-600">
                        Pour confirmer, recopiez le nom de la famille <span class="font-semibold">{{ $family->family_name }}</span> :
                    </label>
                    <input type="text" name="confirm" required autocomplete="off" spellcheck="false"
                           class="text-sm border-red-300 rounded-lg w-44" placeholder="{{ $family->family_name }}">
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium text-sm">
                        <i class="fas fa-eraser mr-1"></i>Effacer définitivement
                    </button>
                    <button type="button" @click="purging = false" class="text-sm text-gray-500 hover:text-gray-700 underline">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
