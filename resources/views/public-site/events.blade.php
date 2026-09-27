@extends('layouts.public-site')

@section('title', 'Événements — ' . $tenant->name)
@section('meta_description', 'Tous les événements de ' . $tenant->name)

@section('content')
<section class="py-12 px-4">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8"><i class="fas fa-calendar-days text-primary mr-2"></i>Événements</h1>

        @if($events->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($events as $event)
            <a href="{{ route('public.site.events.show', ['tenant' => $tenant->slug, 'eventSlug' => $event->slug]) }}"
               class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow group">
                @if($event->image_path)
                <img src="{{ asset('storage/' . $event->image_path) }}" alt="{{ $event->title }}"
                     class="w-full h-48 object-cover group-hover:scale-105 transition-transform">
                @else
                <div class="w-full h-48 bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center">
                    <i class="fas fa-calendar-days text-5xl text-primary/40"></i>
                </div>
                @endif
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="bg-primary text-white text-xs font-bold px-2 py-1 rounded-lg">
                            {{ $event->starts_at->format('d/m') }}
                        </span>
                        @if($event->ends_at && $event->ends_at != $event->starts_at)
                        <span class="text-xs text-gray-500">→ {{ $event->ends_at->format('d/m/Y') }}</span>
                        @endif
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1">{{ $event->title }}</h3>
                    @if($event->location)
                    <p class="text-sm text-gray-600"><i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>{{ $event->location }}</p>
                    @endif
                    @if($event->description)
                    <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ $event->description }}</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $events->links() }}
        </div>
        @else
        <div class="text-center py-16 text-gray-400">
            <i class="fas fa-calendar-days text-6xl mb-4"></i>
            <p class="text-lg">Aucun événement pour le moment.</p>
        </div>
        @endif
    </div>
</section>
@endsection
