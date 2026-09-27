@php
    $limit = $config['limit'] ?? 4;
    $layout = $config['layout'] ?? 'grid';
    $showImage = $config['show_image'] ?? true;

    $news = \App\Models\PublicSiteNews::published()->latestFirst()->take($limit)->get();
@endphp

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-4">
        <i class="fas fa-newspaper mr-2" style="color: {{ tenant()->primary_color ?? '#3B82F6' }}"></i>
        Actualités
    </h2>

    @if($news->count() > 0)
    <div class="@if($layout === 'grid') grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 @endif">
        @foreach($news as $item)
        <a href="{{ route('public.site.news.show', ['tenant' => tenant()->slug, 'newsSlug' => $item->slug]) }}"
           class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow group @if($layout !== 'grid') block mb-3 @endif">
            <div class="@if($layout !== 'grid') flex @endif">
                @if($showImage && $item->image_path)
                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}"
                     class="@if($layout === 'grid') w-full h-40 object-cover @else w-24 h-24 object-cover rounded-l-lg @endif group-hover:scale-105 transition-transform">
                @elseif($showImage)
                <div class="@if($layout === 'grid') w-full h-40 @else w-24 h-24 @endif bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                    <i class="fas fa-newspaper text-3xl text-blue-300"></i>
                </div>
                @endif
                <div class="p-4 @if($layout !== 'grid') flex-1 @endif">
                    @if($item->published_at)
                    <p class="text-xs text-gray-500 mb-1">{{ $item->published_at->format('d/m/Y') }}</p>
                    @endif
                    <h3 class="font-bold text-gray-900 mb-1 line-clamp-2">{{ $item->title }}</h3>
                    @if($item->excerpt)
                    <p class="text-sm text-gray-600 line-clamp-2">{{ $item->excerpt }}</p>
                    @endif
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-xl shadow-md p-8 text-center text-gray-400">
        <i class="fas fa-newspaper text-4xl mb-3"></i>
        <p>Aucune actualité publiée.</p>
    </div>
    @endif
</div>
