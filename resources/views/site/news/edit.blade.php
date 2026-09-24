@extends('layouts.app')

@section('title', 'Modifier l\'actualité')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('site.news.index') }}" class="text-emerald-600 hover:text-emerald-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Retour aux actualités
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-emerald-500 to-emerald-600">
            <h1 class="text-2xl font-bold text-white"><i class="fas fa-edit mr-2"></i>Modifier l'actualité</h1>
        </div>

        <form action="{{ route('site.news.update', $news) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Titre <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $news->title) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Slug (URL)</label>
                    <input type="text" name="slug" value="{{ old('slug', $news->slug) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Extrait (résumé court)</label>
                <textarea name="excerpt" rows="2"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">{{ old('excerpt', $news->excerpt) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                @if($news->image_path)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $news->image_path) }}" class="w-32 h-32 rounded-lg object-cover">
                </div>
                @endif
                <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                <p class="mt-1 text-xs text-gray-500">Remplacer l'image actuelle (max 2 Mo)</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Contenu</label>
                <textarea name="content" id="news-content" rows="15" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">{{ old('content', $news->content) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date de publication</label>
                    <input type="date" name="published_at" value="{{ old('published_at', $news->published_at?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $news->is_published) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-sm font-medium text-gray-700">Publier cette actualité</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-200">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg">
                    <i class="fas fa-save mr-2"></i>Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '#news-content',
    language: 'fr_FR',
    plugins: 'lists link image table autolink',
    toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image table | removeformat',
    menubar: false,
    height: 500,
    content_style: 'body { font-family: Inter, sans-serif; font-size: 14px; }',
});
</script>
@endsection
