@extends('layouts.public-site')

@section('title', $event->title . ' — ' . $tenant->name)
@section('meta_description', $event->description ?: $event->title)

@section('content')
<section class="py-12 px-4">
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('public.site.events', ['tenant' => $tenant->slug]) }}" class="text-primary hover:text-primary/80 font-medium text-sm mb-6 inline-block">
            <i class="fas fa-arrow-left mr-1"></i> Retour aux événements
        </a>

        <article class="bg-white rounded-xl shadow-lg overflow-hidden">
            @if($event->image_path)
            <img src="{{ asset('storage/' . $event->image_path) }}" alt="{{ $event->title }}" class="w-full h-64 object-cover">
            @endif

            <div class="p-8 md:p-10">
                <div class="flex items-center gap-3 mb-4">
                    <span class="bg-primary text-white text-sm font-bold px-3 py-1 rounded-lg">
                        {{ $event->starts_at->format('d/m/Y') }}
                    </span>
                    @if($event->ends_at && $event->ends_at != $event->starts_at)
                    <span class="text-sm text-gray-500">→ {{ $event->ends_at->format('d/m/Y') }}</span>
                    @endif
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $event->title }}</h1>

                @if($event->location)
                <p class="text-gray-600 mb-4"><i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>{{ $event->location }}</p>
                @endif

                @if($event->description)
                <div class="prose-public mt-6">
                    {!! nl2br(e($event->description)) !!}
                </div>
                @endif
            </div>
        </article>
    </div>
</section>
@endsection
