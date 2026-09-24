@extends('layouts.public-site')

@section('title', 'Actualités — ' . $tenant->name)
@section('meta_description', 'Toutes les actualités de ' . $tenant->name)

@section('content')
<section class="py-12 px-4">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8"><i class="fas fa-newspaper text-primary mr-2"></i>Actualités</h1>

        @if($news->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($news as $item)
            <a href="{{ route('public.site.news.show', ['tenant' => $tenant->slug, 'newsSlug' => $item->slug]) }}" class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow group">
                @if($item->image_path)
                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition-transform">
                @else
                <div class="w-full h-48 bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center">
                    <i class="fas fa-newspaper text-5xl text-primary/40"></i>
                </div>
                @endif
                <div class="p-5">
                    @if($item->published_at)
                    <p class="text-xs text-gray-500 mb-2">{{ $item->published_at->format('d/m/Y') }}</p>
                    @endif
                    <h3 class="font-bold text-gray-900 mb-2">{{ $item->title }}</h3>
                    @if($item->excerpt)
                    <p class="text-sm text-gray-600 line-clamp-3">{{ $item->excerpt }}</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $news->links() }}
        </div>
        @else
        <div class="text-center py-16 text-gray-400">
            <i class="fas fa-newspaper text-6xl mb-4"></i>
            <p class="text-lg">Aucune actualité pour le moment.</p>
        </div>
        @endif
    </div>
</section>
@endsection
