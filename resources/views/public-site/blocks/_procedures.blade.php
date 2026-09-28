@php
    $items = collect($config['items'] ?? [])->filter(fn($item) => !empty($item['label']));
    $grouped = $items->groupBy(fn($item) => trim($item['category'] ?? ''))->sortKeys();
    $hasCategories = $grouped->keys()->filter(fn($key) => $key !== '')->count() > 0;
    $primaryColor = tenant()->primary_color ?? '#3B82F6';
@endphp

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-4">
        <i class="fas fa-list-check mr-2" style="color: {{ $primaryColor }}"></i>
        Démarches administratives
    </h2>

    @if($hasCategories)
        @foreach($grouped as $category => $categoryItems)
            @if($category !== '')
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3 mt-6 first:mt-0">{{ $category }}</h3>
            @endif
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 @if($category !== '') mb-2 @else mb-6 @endif">
                @include('public-site.blocks._procedure-card', ['procedureItems' => $categoryItems])
            </div>
        @endforeach
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @include('public-site.blocks._procedure-card', ['procedureItems' => $items->values()])
        </div>
    @endif

    @if($items->isEmpty())
    <div class="bg-white rounded-xl shadow-md p-8 text-center text-gray-400">
        <i class="fas fa-list-check text-4xl mb-3"></i>
        <p>Aucune démarche configurée.</p>
    </div>
    @endif
</div>
