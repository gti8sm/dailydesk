@extends('layouts.app')

@section('title', 'Exports Super Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour au dashboard
        </a>
    </div>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-file-download text-purple-600 mr-3"></i>
            Exports Super Admin
        </h1>
        <p class="mt-2 text-gray-600">Exportez les données de n'importe quel tenant</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Export Garderie -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600 border-b border-blue-700">
                <h2 class="text-xl font-semibold text-white flex items-center">
                    <i class="fas fa-child mr-2"></i>
                    Export Garderie
                </h2>
            </div>
            <div class="p-6">
                <form action="{{ route('central.exports.garderie') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="garderie_tenant_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-building text-purple-600 mr-1"></i>
                            Sélectionner le tenant
                        </label>
                        <select id="garderie_tenant_id" 
                                name="tenant_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="">-- Choisir un tenant --</option>
                            @foreach($tenants as $tenant)
                            <option value="{{ $tenant->id }}">{{ $tenant->name }} ({{ $tenant->slug }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="garderie_start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Date de début
                        </label>
                        <input type="date" 
                               id="garderie_start_date" 
                               name="start_date" 
                               value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>

                    <div class="mb-4">
                        <label for="garderie_end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Date de fin
                        </label>
                        <input type="date" 
                               id="garderie_end_date" 
                               name="end_date" 
                               value="{{ now()->format('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Format d'export
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" 
                                       name="format" 
                                       value="csv" 
                                       checked
                                       class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-gray-700">
                                    <i class="fas fa-file-csv text-green-600 mr-1"></i>
                                    CSV (Excel compatible)
                                </span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" 
                                       name="format" 
                                       value="excel"
                                       class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-gray-700">
                                    <i class="fas fa-file-excel text-green-700 mr-1"></i>
                                    Excel (.xlsx)
                                </span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium transition-colors flex items-center justify-center">
                        <i class="fas fa-download mr-2"></i>
                        Télécharger Export Garderie
                    </button>
                </form>
            </div>
        </div>

        <!-- Export Cantine -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-green-500 to-green-600 border-b border-green-700">
                <h2 class="text-xl font-semibold text-white flex items-center">
                    <i class="fas fa-utensils mr-2"></i>
                    Export Cantine
                </h2>
            </div>
            <div class="p-6">
                <form action="{{ route('central.exports.cantine') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="cantine_tenant_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-building text-purple-600 mr-1"></i>
                            Sélectionner le tenant
                        </label>
                        <select id="cantine_tenant_id" 
                                name="tenant_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                required>
                            <option value="">-- Choisir un tenant --</option>
                            @foreach($tenants as $tenant)
                            <option value="{{ $tenant->id }}">{{ $tenant->name }} ({{ $tenant->slug }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="cantine_start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Date de début
                        </label>
                        <input type="date" 
                               id="cantine_start_date" 
                               name="start_date" 
                               value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               required>
                    </div>

                    <div class="mb-4">
                        <label for="cantine_end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Date de fin
                        </label>
                        <input type="date" 
                               id="cantine_end_date" 
                               name="end_date" 
                               value="{{ now()->format('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Format d'export
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" 
                                       name="format" 
                                       value="csv" 
                                       checked
                                       class="text-green-600 focus:ring-green-500">
                                <span class="ml-2 text-gray-700">
                                    <i class="fas fa-file-csv text-green-600 mr-1"></i>
                                    CSV (Excel compatible)
                                </span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" 
                                       name="format" 
                                       value="excel"
                                       class="text-green-600 focus:ring-green-500">
                                <span class="ml-2 text-gray-700">
                                    <i class="fas fa-file-excel text-green-700 mr-1"></i>
                                    Excel (.xlsx)
                                </span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-medium transition-colors flex items-center justify-center">
                        <i class="fas fa-download mr-2"></i>
                        Télécharger Export Cantine
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Raccourcis rapides -->
    <div class="mt-8 bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-bolt text-yellow-500 mr-2"></i>
            Raccourcis Rapides
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button onclick="setDatesThisMonth()" 
                    class="bg-white hover:bg-gray-50 border border-gray-300 px-4 py-3 rounded-lg text-gray-700 font-medium transition-colors">
                <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                Ce mois
            </button>
            <button onclick="setDatesLastMonth()" 
                    class="bg-white hover:bg-gray-50 border border-gray-300 px-4 py-3 rounded-lg text-gray-700 font-medium transition-colors">
                <i class="fas fa-calendar-minus text-purple-600 mr-2"></i>
                Mois dernier
            </button>
            <button onclick="setDatesThisYear()" 
                    class="bg-white hover:bg-gray-50 border border-gray-300 px-4 py-3 rounded-lg text-gray-700 font-medium transition-colors">
                <i class="fas fa-calendar text-green-600 mr-2"></i>
                Cette année
            </button>
        </div>
    </div>

    <!-- Info -->
    <div class="mt-6 bg-purple-50 border-l-4 border-purple-500 p-4 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-purple-600 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-purple-900">
                    Export Super Admin
                </h3>
                <div class="mt-2 text-sm text-purple-800">
                    <p>En tant que super admin, vous pouvez exporter les données de présences de n'importe quel tenant. Le fichier CSV inclura le nom du tenant dans la première colonne.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setDatesThisMonth() {
    const now = new Date();
    const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
    const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    
    setAllDates(firstDay, lastDay);
}

function setDatesLastMonth() {
    const now = new Date();
    const firstDay = new Date(now.getFullYear(), now.getMonth() - 1, 1);
    const lastDay = new Date(now.getFullYear(), now.getMonth(), 0);
    
    setAllDates(firstDay, lastDay);
}

function setDatesThisYear() {
    const now = new Date();
    const firstDay = new Date(now.getFullYear(), 0, 1);
    const lastDay = new Date(now.getFullYear(), 11, 31);
    
    setAllDates(firstDay, lastDay);
}

function setAllDates(start, end) {
    const startStr = start.toISOString().split('T')[0];
    const endStr = end.toISOString().split('T')[0];
    
    document.getElementById('garderie_start_date').value = startStr;
    document.getElementById('garderie_end_date').value = endStr;
    document.getElementById('cantine_start_date').value = startStr;
    document.getElementById('cantine_end_date').value = endStr;
}
</script>
@endsection
