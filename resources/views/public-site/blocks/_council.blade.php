@php
    $members = collect($config['members'] ?? [])->filter(fn($member) => !empty($member['name']));
    $deliberationsUrl = trim($config['deliberations_url'] ?? '');
    $primaryColor = tenant()->primary_color ?? '#3B82F6';
@endphp

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-4">
        <i class="fas fa-people-group mr-2" style="color: {{ $primaryColor }}"></i>
        Conseil municipal
    </h2>

    @if($members->isNotEmpty())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($members as $member)
        <div class="bg-white rounded-xl shadow-md p-5 text-center hover:shadow-lg transition-shadow">
            @if(!empty($member['photo_url']))
            <img src="{{ $member['photo_url'] }}" alt="{{ $member['name'] }}"
                 class="w-20 h-20 rounded-full object-cover mx-auto mb-3 ring-4" style="--tw-ring-color: {{ $primaryColor }}22;">
            @else
            <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-3"
                 style="background: {{ $primaryColor }}15;">
                <i class="fas fa-user text-2xl" style="color: {{ $primaryColor }};"></i>
            </div>
            @endif
            <p class="font-bold text-gray-900">{{ $member['name'] }}</p>
            @if(!empty($member['role']))
            <p class="text-sm font-medium mt-1" style="color: {{ $primaryColor }};">{{ $member['role'] }}</p>
            @endif
            @if(!empty($member['delegation']))
            <p class="text-xs text-gray-500 mt-2 leading-relaxed">{{ $member['delegation'] }}</p>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-xl shadow-md p-8 text-center text-gray-400">
        <i class="fas fa-people-group text-4xl mb-3"></i>
        <p>Composition du conseil à venir.</p>
    </div>
    @endif

    @if($deliberationsUrl)
    <div class="mt-6 text-center">
        <a href="{{ $deliberationsUrl }}" target="_blank" rel="noopener"
           class="inline-flex items-center gap-2 bg-white border-2 rounded-lg px-5 py-2.5 text-sm font-semibold hover:bg-gray-50 transition-colors"
           style="border-color: {{ $primaryColor }}; color: {{ $primaryColor }};">
            <i class="fas fa-gavel"></i>
            Délibérations et comptes-rendus du conseil
        </a>
    </div>
    @endif
</div>
