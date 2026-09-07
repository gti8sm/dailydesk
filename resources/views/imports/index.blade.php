@extends('layouts.app')

@section('title', 'Import CSV')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-file-upload text-purple-600 mr-3"></i>
            Import CSV
        </h1>
        <p class="mt-2 text-gray-600">Importez en masse vos familles et enfants depuis un fichier CSV</p>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
        <div class="flex">
            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
            <p class="text-green-800">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
        <div class="flex">
            <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-3"></i>
            <p class="text-red-800">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    @if(session('errors') && count(session('errors')) > 0)
    <div class="mb-6 bg-orange-50 border-l-4 border-orange-500 p-4 rounded">
        <div class="flex">
            <i class="fas fa-exclamation-triangle text-orange-500 mt-0.5 mr-3"></i>
            <div class="flex-1">
                <p class="text-orange-900 font-medium mb-2">Erreurs détectées :</p>
                <ul class="list-disc list-inside text-sm text-orange-800 space-y-1">
                    @foreach(session('errors') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Import Familles -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600 border-b border-blue-700">
                <h2 class="text-xl font-semibold text-white flex items-center">
                    <i class="fas fa-users mr-2"></i>
                    Import Familles
                </h2>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <a href="{{ route('imports.template.families') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg font-medium transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        Télécharger le modèle CSV
                    </a>
                </div>

                <form action="{{ route('imports.families') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="families_file" class="block text-sm font-medium text-gray-700 mb-2">
                            Fichier CSV
                        </label>
                        <input type="file" 
                               id="families_file" 
                               name="file" 
                               accept=".csv,.txt"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('file') border-red-500 @enderror"
                               required>
                        @error('file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded mb-4">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Format attendu :</strong> Nom Famille, Email, Téléphone, Adresse, Code Postal, Ville
                        </p>
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium transition-colors flex items-center justify-center">
                        <i class="fas fa-upload mr-2"></i>
                        Importer les Familles
                    </button>
                </form>
            </div>
        </div>

        <!-- Import Enfants -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-green-500 to-green-600 border-b border-green-700">
                <h2 class="text-xl font-semibold text-white flex items-center">
                    <i class="fas fa-child mr-2"></i>
                    Import Enfants
                </h2>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <a href="{{ route('imports.template.children') }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg font-medium transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        Télécharger le modèle CSV
                    </a>
                </div>

                <form action="{{ route('imports.children') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="children_file" class="block text-sm font-medium text-gray-700 mb-2">
                            Fichier CSV
                        </label>
                        <input type="file" 
                               id="children_file" 
                               name="file" 
                               accept=".csv,.txt"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('file') border-red-500 @enderror"
                               required>
                        @error('file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded mb-4">
                        <p class="text-sm text-green-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Format attendu :</strong> Prénom, Nom, Date Naissance (YYYY-MM-DD), Genre (M/F), Email Famille
                        </p>
                        <p class="text-sm text-green-800 mt-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Les familles doivent être importées en premier !
                        </p>
                    </div>

                    <button type="submit" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-medium transition-colors flex items-center justify-center">
                        <i class="fas fa-upload mr-2"></i>
                        Importer les Enfants
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Instructions -->
    <div class="mt-8 bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-question-circle text-purple-600 mr-2"></i>
            Comment ça marche ?
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg p-4 shadow">
                <div class="flex items-center mb-3">
                    <div class="bg-blue-100 rounded-full p-3 mr-3">
                        <i class="fas fa-download text-blue-600 text-xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900">1. Téléchargez</h4>
                </div>
                <p class="text-sm text-gray-600">
                    Téléchargez le modèle CSV correspondant (familles ou enfants)
                </p>
            </div>

            <div class="bg-white rounded-lg p-4 shadow">
                <div class="flex items-center mb-3">
                    <div class="bg-green-100 rounded-full p-3 mr-3">
                        <i class="fas fa-edit text-green-600 text-xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900">2. Remplissez</h4>
                </div>
                <p class="text-sm text-gray-600">
                    Ouvrez le fichier dans Excel et remplissez vos données
                </p>
            </div>

            <div class="bg-white rounded-lg p-4 shadow">
                <div class="flex items-center mb-3">
                    <div class="bg-purple-100 rounded-full p-3 mr-3">
                        <i class="fas fa-upload text-purple-600 text-xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900">3. Importez</h4>
                </div>
                <p class="text-sm text-gray-600">
                    Enregistrez en CSV et importez le fichier ici
                </p>
            </div>
        </div>
    </div>

    <!-- Conseils -->
    <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-lightbulb text-yellow-600 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-900">
                    Conseils pour un import réussi
                </h3>
                <div class="mt-2 text-sm text-yellow-800">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Utilisez le séparateur <strong>point-virgule (;)</strong></li>
                        <li>Respectez l'ordre des colonnes du modèle</li>
                        <li>Les emails des familles doivent être uniques</li>
                        <li>Le format de date doit être <strong>YYYY-MM-DD</strong> (ex: 2018-05-15)</li>
                        <li>Le genre doit être <strong>M</strong> ou <strong>F</strong></li>
                        <li>Importez d'abord les familles, puis les enfants</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
