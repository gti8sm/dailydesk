@php
    $style = $config['style'] ?? 'rounded';
    $radius = $style === 'rounded' ? 'rounded-full' : 'rounded-lg';
    $socials = [
        'facebook_url' => ['fab fa-facebook-f', '#1877F2', 'Facebook'],
        'instagram_url' => ['fab fa-instagram', '#E4405F', 'Instagram'],
        'twitter_url' => ['fab fa-x-twitter', '#000000', 'Twitter/X'],
        'youtube_url' => ['fab fa-youtube', '#FF0000', 'YouTube'],
    ];
@endphp

<div class="bg-white rounded-xl shadow-md p-6 mb-6 text-center">
    <h2 class="text-lg font-bold text-gray-900 mb-4">Suivez-nous</h2>
    <div class="flex items-center justify-center gap-3 flex-wrap">
        @foreach($socials as $key => $info)
        @if(!empty($config[$key]))
        <a href="{{ $config[$key] }}" target="_blank" rel="noopener"
           class="{{ $radius }} w-12 h-12 flex items-center justify-center text-white text-xl hover:scale-110 transition-transform"
           style="background-color: {{ $info[1] }}"
           title="{{ $info[2] }}">
            <i class="{{ $info[0] }}"></i>
        </a>
        @endif
        @endforeach
    </div>
</div>
