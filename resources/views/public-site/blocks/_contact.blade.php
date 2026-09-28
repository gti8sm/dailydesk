@php
    $tenant = tenant();
    $address = trim($config['address_override'] ?? '') ?: $tenant->address;
    $phone = trim($config['phone_override'] ?? '') ?: $tenant->phone;
    $email = trim($config['email_override'] ?? '') ?: $tenant->email;
    $city = trim($tenant->postal_code . ' ' . $tenant->city);
    $mapUrl = trim($config['map_url'] ?? '');
    $showForm = $config['show_form'] ?? true;
    $primaryColor = $tenant->primary_color ?? '#3B82F6';
@endphp

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">
        <i class="fas fa-envelope mr-2" style="color: {{ $primaryColor }}"></i>
        Contact
    </h2>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-md p-6 space-y-4">
            @if($address)
            <div class="flex items-start gap-4">
                <div class="rounded-lg w-11 h-11 flex items-center justify-center shrink-0"
                     style="background: {{ $primaryColor }}15;">
                    <i class="fas fa-map-marker-alt" style="color: {{ $primaryColor }};"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 text-sm">Adresse</p>
                    <p class="text-sm text-gray-600 mt-0.5">
                        {{ $address }}<br>
                        @if(trim($city)){{ $city }}@endif
                    </p>
                </div>
            </div>
            @endif

            @if($phone)
            <div class="flex items-start gap-4">
                <div class="rounded-lg w-11 h-11 flex items-center justify-center shrink-0"
                     style="background: {{ $primaryColor }}15;">
                    <i class="fas fa-phone" style="color: {{ $primaryColor }};"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 text-sm">Téléphone</p>
                    <p class="text-sm text-gray-600 mt-0.5">
                        <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}" class="hover:underline">{{ $phone }}</a>
                    </p>
                </div>
            </div>
            @endif

            @if($email)
            <div class="flex items-start gap-4">
                <div class="rounded-lg w-11 h-11 flex items-center justify-center shrink-0"
                     style="background: {{ $primaryColor }}15;">
                    <i class="fas fa-envelope" style="color: {{ $primaryColor }};"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 text-sm">Email</p>
                    <p class="text-sm text-gray-600 mt-0.5">
                        <a href="mailto:{{ $email }}" class="hover:underline">{{ $email }}</a>
                    </p>
                </div>
            </div>
            @endif

            @if($mapUrl)
            <a href="{{ $mapUrl }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold text-white hover:opacity-90 transition-opacity"
               style="background: {{ $primaryColor }};">
                <i class="fas fa-location-dot"></i>Voir sur la carte
            </a>
            @endif
        </div>

        @if($showForm)
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="font-bold text-gray-900 mb-4">Écrivez-nous</h3>
            @if(session('contact_success'))
            <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded">
                <p class="text-sm text-green-700">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('contact_success') }}
                </p>
            </div>
            @endif
            <form action="{{ route('public.site.contact', ['tenant' => $tenant->slug]) }}" method="POST" class="space-y-3">
                @csrf
                <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1" for="contact-name">Nom complet *</label>
                        <input type="text" id="contact-name" name="name" value="{{ old('name') }}" required maxlength="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1" for="contact-email">Email *</label>
                        <input type="email" id="contact-email" name="email" value="{{ old('email') }}" required maxlength="255"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1" for="contact-phone">Téléphone</label>
                        <input type="tel" id="contact-phone" name="phone" value="{{ old('phone') }}" maxlength="30"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1" for="contact-subject">Sujet</label>
                        <input type="text" id="contact-subject" name="subject" value="{{ old('subject') }}" maxlength="150"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1" for="contact-message">Message *</label>
                    <textarea id="contact-message" name="message" rows="4" required maxlength="5000"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('message') }}</textarea>
                </div>
                <button type="submit"
                        class="w-full text-white px-4 py-2.5 rounded-lg font-semibold text-sm hover:opacity-90 transition-opacity"
                        style="background: {{ $primaryColor }};">
                    <i class="fas fa-paper-plane mr-2"></i>Envoyer le message
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
