@foreach($procedureItems as $item)
@php
    $url = trim($item['url'] ?? '');
    $icon = $item['icon'] ?? 'fas fa-arrow-right';
@endphp
@if($url)
<a href="{{ $url }}" target="_blank" rel="noopener"
   class="bg-white rounded-xl shadow-md p-5 hover:shadow-xl transition-shadow group flex items-center gap-4">
    <div class="rounded-lg w-12 h-12 flex items-center justify-center shrink-0"
         style="background: {{ tenant()->primary_color ?? '#3B82F6' }}15;">
        <i class="{{ $icon }} text-lg" style="color: {{ tenant()->primary_color ?? '#3B82F6' }};"></i>
    </div>
    <div class="min-w-0 flex-1">
        <p class="font-semibold text-gray-900 text-sm group-hover:underline">{{ $item['label'] }}</p>
        <p class="text-xs text-gray-400 mt-0.5"><i class="fas fa-up-right-from-square mr-1"></i>Démarche en ligne</p>
    </div>
    <i class="fas fa-chevron-right text-gray-300 group-hover:text-gray-500 transition-colors"></i>
</a>
@else
<div class="bg-white rounded-xl shadow-md p-5 flex items-center gap-4">
    <div class="rounded-lg w-12 h-12 flex items-center justify-center shrink-0"
         style="background: {{ tenant()->primary_color ?? '#3B82F6' }}15;">
        <i class="{{ $icon }} text-lg" style="color: {{ tenant()->primary_color ?? '#3B82F6' }};"></i>
    </div>
    <div class="min-w-0 flex-1">
        <p class="font-semibold text-gray-900 text-sm">{{ $item['label'] }}</p>
        <p class="text-xs text-gray-400 mt-0.5">Rendez-vous en mairie</p>
    </div>
</div>
@endif
@endforeach
