@extends('layouts.public-site')

@section('title', $tenant->name . ' — Accueil')
@section('meta_description', 'Site officiel de ' . $tenant->name . '. Actualités, menus cantine, écoles et informations de la commune.')

@section('content')
<!-- Hero -->
<section class="bg-gradient-to-br from-primary to-secondary text-white py-20 px-4">
    <div class="max-w-5xl mx-auto text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-4">{{ $tenant->name }}</h1>
        <p class="text-lg md:text-xl text-white/90 max-w-2xl mx-auto">
            @if($tenant->city)
            {{ $tenant->city }} — {{ $tenant->postal_code ?? '' }}
            @else
            Bienvenue sur le site officiel de notre commune
            @endif
        </p>
    </div>
</section>

<!-- Page d'accueil éditable -->
@if($homePage && $homePage->content)
<section class="py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="prose-public bg-white rounded-xl shadow-lg p-8 md:p-12">
            {!! $homePage->content !!}
        </div>
    </div>
</section>
@endif

<!-- Actualités -->
@if($news->count() > 0)
<section class="py-12 px-4 bg-gray-100">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-gray-900"><i class="fas fa-newspaper text-primary mr-2"></i>Actualités</h2>
            <a href="{{ route('public.site.news', ['tenant' => $tenant->slug]) }}" class="text-primary hover:text-primary/80 font-medium text-sm">
                Toutes les actualités <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($news as $item)
            <a href="{{ route('public.site.news.show', ['tenant' => $tenant->slug, 'newsSlug' => $item->slug]) }}" class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow group">
                @if($item->image_path)
                <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}" class="w-full h-40 object-cover group-hover:scale-105 transition-transform">
                @else
                <div class="w-full h-40 bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center">
                    <i class="fas fa-newspaper text-4xl text-primary/40"></i>
                </div>
                @endif
                <div class="p-4">
                    @if($item->published_at)
                    <p class="text-xs text-gray-500 mb-1">{{ $item->published_at->format('d/m/Y') }}</p>
                    @endif
                    <h3 class="font-bold text-gray-900 mb-2 line-clamp-2">{{ $item->title }}</h3>
                    @if($item->excerpt)
                    <p class="text-sm text-gray-600 line-clamp-3">{{ $item->excerpt }}</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Menus cantine du mois -->
@if($menus->count() > 0)
<section class="py-12 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-gray-900"><i class="fas fa-utensils text-primary mr-2"></i>Menus de la cantine</h2>
            <a href="{{ route('public.site.menus', ['tenant' => $tenant->slug]) }}" class="text-primary hover:text-primary/80 font-medium text-sm">
                Voir tous les menus <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($menus->take(6) as $date => $dayMenus)
            <div class="bg-white rounded-lg shadow-md p-4">
                <p class="font-bold text-primary mb-2">{{ \Carbon\Carbon::parse($date)->locale('fr')->isoFormat('dddd D MMMM') }}</p>
                @foreach($dayMenus as $menu)
                <div class="border-t border-gray-100 pt-2 mt-2">
                    <p class="text-xs font-medium text-gray-500 uppercase">{{ $menu->meal_type === 'midi' ? 'Déjeuner' : 'Goûter' }}</p>
                    @if($menu->title)<p class="font-semibold text-gray-800 text-sm">{{ $menu->title }}</p>@endif
                    @if($menu->main_course)<p class="text-xs text-gray-600">{{ $menu->main_course }}</p>@endif
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Écoles -->
@if($schools->count() > 0)
<section class="py-12 px-4 bg-gray-100">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl font-bold text-gray-900 mb-8"><i class="fas fa-school text-primary mr-2"></i>Nos écoles</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($schools as $school)
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center gap-3 mb-3">
                    <div class="bg-primary/10 rounded-lg w-12 h-12 flex items-center justify-center">
                        <i class="fas fa-school text-primary text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">{{ $school->name }}</h3>
                        <span class="text-xs text-gray-500">{{ $school->type_label }}</span>
                    </div>
                </div>
                @if($school->address)
                <p class="text-sm text-gray-600"><i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>{{ $school->address }}</p>
                @endif
                @if($school->is_shared)
                <span class="inline-block mt-2 text-xs bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full"><i class="fas fa-handshake mr-1"></i>École partagée</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
