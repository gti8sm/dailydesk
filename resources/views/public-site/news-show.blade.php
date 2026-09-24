@extends('layouts.public-site')

@section('title', $item->title . ' — ' . $tenant->name)
@section('meta_description', $item->excerpt ?: $item->title)

@section('content')
<section class="py-12 px-4">
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('public.site.news', ['tenant' => $tenant->slug]) }}" class="text-primary hover:text-primary/80 font-medium text-sm mb-6 inline-block">
            <i class="fas fa-arrow-left mr-1"></i> Retour aux actualités
        </a>

        <article class="bg-white rounded-xl shadow-lg overflow-hidden">
            @if($item->image_path)
            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}" class="w-full h-64 object-cover">
            @endif

            <div class="p-8 md:p-10">
                @if($item->published_at)
                <p class="text-sm text-gray-500 mb-2">{{ $item->published_at->format('d/m/Y') }}</p>
                @endif
                <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ $item->title }}</h1>

                @if($item->excerpt)
                <p class="text-lg text-gray-600 mb-6 font-medium">{{ $item->excerpt }}</p>
                @endif

                <div class="prose-public">
                    {!! $item->content !!}
                </div>
            </div>
        </article>
    </div>
</section>
@endsection
