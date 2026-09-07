@extends('layouts.app')

@section('title', 'Modifier la Classe')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-edit text-indigo-600 mr-2"></i>
            Modifier la Classe
        </h1>
    </div>

    <div class="bg-white shadow-lg rounded-xl p-6">
        <form action="{{ route('classes.update', $class) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nom de la classe
                </label>
                <input type="text" name="name" id="name" required
                       value="{{ old('name', $class->name) }}"
                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="mb-4">
                <label for="teacher_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Professeur(e)
                </label>
                <input type="text" name="teacher_name" id="teacher_name"
                       value="{{ old('teacher_name', $class->teacher_name) }}"
                       placeholder="Ex: Mme Dupont"
                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="mb-4">
                <label for="school_year" class="block text-sm font-medium text-gray-700 mb-2">
                    Année scolaire
                </label>
                <input type="text" name="school_year" id="school_year" required
                       value="{{ old('school_year', $class->school_year) }}"
                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1"
                           {{ $class->is_active ? 'checked' : '' }}
                           class="h-5 w-5 text-indigo-600 rounded border-gray-300 mr-2">
                    <span class="text-sm font-medium text-gray-700">Classe active</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                    <i class="fas fa-check mr-2"></i>Enregistrer
                </button>
                <a href="{{ route('classes.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
