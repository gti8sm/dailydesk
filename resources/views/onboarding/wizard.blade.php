@extends('layouts.app')

@section('title', 'Configuration de votre espace — Onboarding')

@push('scripts')
<script>
function showStep(step) {
    document.querySelectorAll('[data-step]').forEach(el => el.classList.add('hidden'));
    document.querySelector(`[data-step="${step}"]`)?.classList.remove('hidden');
    document.querySelectorAll('[data-step-indicator]').forEach(el => {
        const s = parseInt(el.dataset.stepIndicator);
        el.classList.toggle('bg-blue-600', s <= step);
        el.classList.toggle('text-white', s <= step);
        el.classList.toggle('bg-gray-200', s > step);
        el.classList.toggle('text-gray-500', s > step);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    showStep({{ $step }});
});
</script>
@endpush

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="text-center mb-8">
        <div class="bg-blue-600 rounded-2xl w-16 h-16 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-rocket text-white text-2xl"></i>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">Bienvenue sur DailyDesk !</h1>
        <p class="text-gray-500 mt-2">Configurons votre espace en quelques étapes.</p>
    </div>

    <!-- Step indicators -->
    <div class="flex items-center justify-center gap-2 mb-8">
        @for($i = 1; $i <= 4; $i++)
        <div class="flex items-center">
            <div data-step-indicator="{{ $i }}"
                 class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-colors
                 {{ $i <= $step ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                {{ $i }}
            </div>
            @if($i < 4)
            <div class="w-8 h-1 {{ $i < $step ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
            @endif
        </div>
        @endfor
    </div>

    @if(session('step_completed'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-400 mr-3"></i>
            <p class="text-sm text-green-700">Étape {{ session('step_completed') }} enregistrée !</p>
        </div>
    </div>
    @endif

    <!-- Step 1: Organization -->
    <div data-step="1" class="hidden bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-2"><i class="fas fa-building text-blue-500 mr-2"></i>Votre organisation</h2>
        <p class="text-sm text-gray-500 mb-6">Renseignez les informations de votre mairie ou collectivité.</p>

        <form action="{{ route('onboarding.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="step" value="1">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom de l'organisation *</label>
                    <input type="text" name="name" required value="{{ $tenant->name }}"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                           placeholder="Mairie de...">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                        <input type="tel" name="phone" value="{{ $tenant->phone }}"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code postal</label>
                        <input type="text" name="postal_code" value="{{ $tenant->postal_code }}"
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                    <input type="text" name="address" value="{{ $tenant->address }}"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                    <input type="text" name="city" value="{{ $tenant->city }}"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo (optionnel)</label>
                    <input type="file" name="logo" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-400 mt-1">PNG ou JPG, 2 Mo max</p>
                </div>
            </div>
            <div class="flex justify-between mt-8">
                <a href="{{ route('onboarding.skip') }}" class="text-sm text-gray-400 hover:text-gray-600">Passer l'onboarding</a>
                <button type="submit" class="btn-tenant px-6 py-2.5 rounded-lg font-medium text-sm">
                    Continuer <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Step 2: Classes -->
    <div data-step="2" class="hidden bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-2"><i class="fas fa-chalkboard text-blue-500 mr-2"></i>Classes et écoles</h2>
        <p class="text-sm text-gray-500 mb-6">Créez les classes de votre école. Vous pourrez en ajouter d'autres plus tard.</p>

        <form action="{{ route('onboarding.store') }}" method="POST">
            @csrf
            <input type="hidden" name="step" value="2">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Année scolaire</label>
                <input type="text" name="school_year" value="{{ date('Y') }}-{{ date('Y') + 1 }}"
                       class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
            </div>
            <div id="classes-container" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 class-row">
                    <input type="text" name="classes[0][name]" placeholder="Nom de la classe (ex: CP)"
                           class="px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <input type="text" name="classes[0][teacher_name]" placeholder="Enseignant (optionnel)"
                           class="px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>
            </div>
            <button type="button" onclick="addClassRow()" class="mt-3 text-sm text-blue-600 hover:text-blue-800">
                <i class="fas fa-plus-circle mr-1"></i> Ajouter une classe
            </button>
            <div class="flex justify-between mt-8">
                <button type="button" onclick="showStep(1)" class="text-sm text-gray-400 hover:text-gray-600">
                    <i class="fas fa-arrow-left mr-1"></i> Retour
                </button>
                <button type="submit" class="btn-tenant px-6 py-2.5 rounded-lg font-medium text-sm">
                    Continuer <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Step 3: Customization -->
    <div data-step="3" class="hidden bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-2"><i class="fas fa-palette text-blue-500 mr-2"></i>Personnalisation</h2>
        <p class="text-sm text-gray-500 mb-6">Choisissez les couleurs de votre espace et le nom affiché.</p>

        <form action="{{ route('onboarding.store') }}" method="POST">
            @csrf
            <input type="hidden" name="step" value="3">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom affiché dans l'interface</label>
                    <input type="text" name="app_name" value="{{ $tenant->name }}"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                           placeholder="DailyDesk">
                    <p class="text-xs text-gray-400 mt-1">Nom affiché dans la barre de navigation, les emails et la page de connexion (ex: "Mairie de Libourne")</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Couleur principale</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="primary_color" value="{{ $tenant->primary_color ?? '#3B82F6' }}"
                                   class="w-12 h-10 rounded-lg border border-gray-300 cursor-pointer">
                            <input type="text" id="primary_color_text" value="{{ $tenant->primary_color ?? '#3B82F6' }}"
                                   class="px-4 py-2.5 rounded-lg border border-gray-300 text-sm w-32"
                                   oninput="document.querySelector('input[name=primary_color]').value = this.value">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Couleur secondaire</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="secondary_color" value="{{ $tenant->secondary_color ?? '#6366F1' }}"
                                   class="w-12 h-10 rounded-lg border border-gray-300 cursor-pointer">
                            <input type="text" id="secondary_color_text" value="{{ $tenant->secondary_color ?? '#6366F1' }}"
                                   class="px-4 py-2.5 rounded-lg border border-gray-300 text-sm w-32"
                                   oninput="document.querySelector('input[name=secondary_color]').value = this.value">
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 mb-2">Aperçu</p>
                    <div class="flex items-center gap-3">
                        <div class="px-4 py-2 rounded-lg text-white text-sm font-medium" style="background: {{ $tenant->primary_color ?? '#3B82F6' }}">Bouton principal</div>
                        <div class="px-4 py-2 rounded-lg text-white text-sm font-medium" style="background: {{ $tenant->secondary_color ?? '#6366F1' }}">Bouton secondaire</div>
                    </div>
                </div>
            </div>
            <div class="flex justify-between mt-8">
                <button type="button" onclick="showStep(2)" class="text-sm text-gray-400 hover:text-gray-600">
                    <i class="fas fa-arrow-left mr-1"></i> Retour
                </button>
                <button type="submit" class="btn-tenant px-6 py-2.5 rounded-lg font-medium text-sm">
                    Continuer <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Step 4: Notifications -->
    <div data-step="4" class="hidden bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-2"><i class="fas fa-bell text-blue-500 mr-2"></i>Notifications aux parents</h2>
        <p class="text-sm text-gray-500 mb-6">Choisissez les notifications envoyées aux parents. Les emails utilisent le serveur SMTP central de DailyDesk.</p>

        <form action="{{ route('onboarding.store') }}" method="POST">
            @csrf
            <input type="hidden" name="step" value="4">

            <div class="space-y-2">
                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" name="notify_arrival" value="1" checked class="w-5 h-5 rounded text-blue-600">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Arrivée en garderie</span>
                        <p class="text-xs text-gray-400">Notifier quand un enfant arrive</p>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" name="notify_departure" value="1" checked class="w-5 h-5 rounded text-blue-600">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Départ de garderie</span>
                        <p class="text-xs text-gray-400">Notifier quand un enfant part</p>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" name="notify_absence" value="1" class="w-5 h-5 rounded text-blue-600">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Absence</span>
                        <p class="text-xs text-gray-400">Notifier en cas d'absence non justifiée</p>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" name="notify_event" value="1" checked class="w-5 h-5 rounded text-blue-600">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Événements / Signalements</span>
                        <p class="text-xs text-gray-400">Notifier lors d'un incident ou événement</p>
                    </div>
                </label>
            </div>

            <div class="flex justify-between mt-8">
                <button type="button" onclick="showStep(3)" class="text-sm text-gray-400 hover:text-gray-600">
                    <i class="fas fa-arrow-left mr-1"></i> Retour
                </button>
                <button type="submit" class="btn-tenant px-6 py-2.5 rounded-lg font-medium text-sm">
                    <i class="fas fa-check mr-2"></i> Terminer la configuration
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let classIndex = 1;
function addClassRow() {
    const container = document.getElementById('classes-container');
    const row = document.createElement('div');
    row.className = 'grid grid-cols-1 md:grid-cols-2 gap-3 class-row';
    row.innerHTML = `
        <input type="text" name="classes[${classIndex}][name]" placeholder="Nom de la classe"
               class="px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
        <input type="text" name="classes[${classIndex}][teacher_name]" placeholder="Enseignant (optionnel)"
               class="px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
    `;
    container.appendChild(row);
    classIndex++;
}
</script>
@endsection
