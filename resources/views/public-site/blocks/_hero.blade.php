@php
    $title = $config['title'] ?? '';
    $subtitle = $config['subtitle'] ?? '';
    $imageUrl = $config['image_url'] ?? '';
    $ctaLabel = $config['cta_label'] ?? '';
    $ctaUrl = $config['cta_url'] ?? '';
@endphp

<section class="relative overflow-hidden rounded-xl shadow-lg mb-6"
         @if($imageUrl) style="background-image: url('{{ $imageUrl }}'); background-size: cover; background-position: center;"
         @else style="background: linear-gradient(135deg, {{ tenant()->primary_color ?? '#3B82F6' }}, {{ tenant()->secondary_color ?? '#6366F1' }});"
         @endif>
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="relative px-8 py-16 md:py-24 text-center text-white">
        @if($title)
        <h2 class="text-3xl md:text-5xl font-extrabold mb-4">{{ $title }}</h2>
        @endif
        @if($subtitle)
        <p class="text-lg md:text-xl text-white/90 max-w-2xl mx-auto mb-6">{{ $subtitle }}</p>
        @endif
        @if($ctaLabel && $ctaUrl)
        <a href="{{ $ctaUrl }}" class="inline-block bg-white text-gray-900 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
            {{ $ctaLabel }}
        </a>
        @endif
    </div>
</section>
