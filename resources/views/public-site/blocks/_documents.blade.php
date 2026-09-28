@php
    $items = collect($config['items'] ?? [])->filter(fn($item) => !empty($item['label']));
    $grouped = $items->groupBy(fn($item) => trim($item['category'] ?? ''))->sortKeys();
    $primaryColor = tenant()->primary_color ?? '#3B82F6';
@endphp

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-4">
        <i class="fas fa-folder-open mr-2" style="color: {{ $primaryColor }}"></i>
        Documents
    </h2>

    @if($items->isNotEmpty())
    <div class="bg-white rounded-xl shadow-md divide-y divide-gray-100 overflow-hidden">
        @foreach($grouped as $category => $categoryItems)
            @if($category !== '' && $grouped->count() > 1)
            <div class="px-5 py-2 bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ $category }}</div>
            @endif
            @foreach($categoryItems as $item)
            @php
                $url = trim($item['url'] ?? '');
                $date = $item['date'] ?? '';
                $timestamp = $date ? \Carbon\Carbon::parse($date) : null;
            @endphp
            <div class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition-colors">
                <div class="rounded-lg w-10 h-10 flex items-center justify-center shrink-0"
                     style="background: {{ $primaryColor }}15;">
                    <i class="fas fa-file-pdf text-lg" style="color: {{ $primaryColor }};"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-gray-900 text-sm">{{ $item['label'] }}</p>
                    <div class="flex items-center gap-3 mt-0.5 text-xs text-gray-400">
                        @if($timestamp)
                        <span><i class="fas fa-calendar mr-1"></i>Publié le {{ $timestamp->format('d/m/Y') }}</span>
                        @endif
                        @if($category !== '' && $grouped->count() <= 1)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">{{ $category }}</span>
                        @endif
                    </div>
                </div>
                @if($url)
                <a href="{{ $url }}" target="_blank" rel="noopener"
                   class="shrink-0 text-white text-sm font-medium px-4 py-2 rounded-lg hover:opacity-90 transition-opacity"
                   style="background: {{ $primaryColor }};">
                    <i class="fas fa-download mr-2"></i>Télécharger
                </a>
                @else
                <span class="shrink-0 text-xs text-gray-300 italic">Bientôt disponible</span>
                @endif
            </div>
            @endforeach
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-xl shadow-md p-8 text-center text-gray-400">
        <i class="fas fa-folder-open text-4xl mb-3"></i>
        <p>Aucun document publié pour le moment.</p>
    </div>
    @endif
</div>
