@php
    $limit = $config['limit'] ?? 4;
    $showImage = $config['show_image'] ?? true;

    $events = \App\Models\PublicSiteEvent::published()->upcoming()->take($limit)->get();
@endphp

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-4">
        <i class="fas fa-calendar-days mr-2" style="color: {{ tenant()->primary_color ?? '#3B82F6' }}"></i>
        Prochains événements
    </h2>

    @if($events->count() > 0)
    <div class="space-y-3">
        @foreach($events as $event)
        <div class="bg-white rounded-xl shadow-md p-4 flex items-start gap-4">
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 text-white rounded-lg w-16 h-16 flex flex-col items-center justify-center flex-shrink-0">
                <span class="text-2xl font-bold">{{ $event->starts_at->day }}</span>
                <span class="text-xs uppercase">{{ $event->starts_at->locale('fr')->shortMonthName }}</span>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-gray-900">{{ $event->title }}</h3>
                <p class="text-sm text-gray-500 mb-1">
                    {{ $event->starts_at->format('d/m/Y') }}
                    @if($event->ends_at && $event->ends_at != $event->starts_at) → {{ $event->ends_at->format('d/m/Y') }} @endif
                </p>
                @if($event->location)
                <p class="text-sm text-gray-600"><i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>{{ $event->location }}</p>
                @endif
                @if($event->description)
                <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $event->description }}</p>
                @endif
            </div>
            @if($showImage && $event->image_path)
            <img src="{{ asset('storage/' . $event->image_path) }}" alt="{{ $event->title }}"
                 class="w-20 h-20 rounded-lg object-cover flex-shrink-0">
            @endif
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-md p-8 text-center text-gray-400">
        <i class="fas fa-calendar-days text-4xl mb-3"></i>
        <p>Aucun événement à venir.</p>
    </div>
    @endif
</div>
