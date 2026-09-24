@extends('layouts.public-site')

@section('title', 'Écoles — ' . $tenant->name)
@section('meta_description', 'Liste des écoles de ' . $tenant->name)

@section('content')
<section class="py-12 px-4">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8"><i class="fas fa-school text-primary mr-2"></i>Nos écoles</h1>

        @if($schools->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($schools as $school)
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-primary/10 rounded-lg w-14 h-14 flex items-center justify-center">
                        <i class="fas fa-school text-primary text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">{{ $school->name }}</h3>
                        <span class="text-sm text-gray-500">{{ $school->type_label }}</span>
                    </div>
                </div>
                @if($school->address)
                <p class="text-sm text-gray-600 mb-2"><i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>{{ $school->address }}</p>
                @endif
                @if($school->classes()->count() > 0)
                <p class="text-sm text-gray-600 mb-2"><i class="fas fa-users mr-2 text-gray-400"></i>{{ $school->classes()->count() }} classe(s)</p>
                @endif
                @if($school->is_shared)
                <span class="inline-block mt-2 text-xs bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full"><i class="fas fa-handshake mr-1"></i>École partagée en intercommunalité</span>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-16 text-gray-400">
            <i class="fas fa-school text-6xl mb-4"></i>
            <p class="text-lg">Aucune école enregistrée.</p>
        </div>
        @endif
    </div>
</section>
@endsection
