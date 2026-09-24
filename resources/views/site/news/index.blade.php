@extends('layouts.app')

@section('title', 'Actualités du site public')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-newspaper text-emerald-600 mr-2"></i>Actualités du site public
            </h1>
            <p class="mt-1 text-sm text-gray-600">Gérez les actualités affichées sur votre site web public.</p>
        </div>
        <a href="{{ route('site.news.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-plus mr-2"></i>Nouvelle actualité
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($news as $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($item->image_path)
                            <img src="{{ asset('storage/' . $item->image_path) }}" class="w-10 h-10 rounded-lg object-cover">
                            @endif
                            <span class="text-sm font-medium text-gray-900">{{ $item->title }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $item->published_at?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-6 py-4">
                        @if($item->is_published)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Publié</span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Brouillon</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('site.news.edit', $item) }}" class="text-blue-600 hover:text-blue-800 mr-3" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('site.news.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette actualité ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                        <i class="fas fa-newspaper text-4xl mb-3"></i>
                        <p>Aucune actualité. Créez votre première actualité !</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $news->links() }}
    </div>
</div>
@endsection
