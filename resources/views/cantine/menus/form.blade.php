@extends('layouts.app')

@section('title', $menu->exists ? 'Modifier le menu' : 'Nouveau menu cantine')

@section('content')
<div class="max-w-3xl mx-auto px-2 sm:px-4 lg:px-8">
    <div class="mb-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
            <i class="fas fa-utensils text-green-600 mr-2"></i>
            {{ $menu->exists ? 'Modifier le menu' : 'Nouveau menu' }}
        </h1>
        <p class="mt-1 text-xs sm:text-sm text-gray-600">Renseignez les composants du repas — les plats déjà saisis apparaissent en autocomplétion</p>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-300 rounded-lg text-green-700">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    @php
        $dishesByCategory = $dishes ?? collect();
        $dishMap = [];
        foreach (['starter', 'main_course', 'side_dish', 'dessert'] as $cat) {
            foreach ($dishesByCategory->get($cat, collect()) as $dish) {
                $dishMap[$cat][] = [
                    'name' => $dish->name,
                    'allergens' => $dish->allergens ?? [],
                    'vegetarian' => $dish->vegetarian,
                ];
            }
        }
    @endphp

    <div class="bg-white shadow-lg rounded-xl p-6">
        <form action="{{ $menu->exists ? route('cantine.menus.update', $menu) : route('cantine.menus.store') }}" method="POST">
            @csrf
            @if($menu->exists) @method('PUT') @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date du repas <span class="text-red-500">*</span></label>
                    <input type="date" name="menu_date" value="{{ old('menu_date', $menu->menu_date?->format('Y-m-d')) }}"
                           required class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                    @error('menu_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de repas <span class="text-red-500">*</span></label>
                    <select name="meal_type" class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                        <option value="lunch" @selected(old('meal_type', $menu->meal_type) === 'lunch')>Déjeuner</option>
                        <option value="snack" @selected(old('meal_type', $menu->meal_type) === 'snack')>Goûter</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Titre (optionnel)</label>
                <input type="text" name="title" value="{{ old('title', $menu->title) }}"
                       placeholder="Ex. Menu du mardi"
                       class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
            </div>

            <!-- Entrée -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Entrée</label>
                <input type="text" name="starter" id="dish-starter" value="{{ old('starter', $menu->starter) }}"
                       list="datalist-starter" autocomplete="off"
                       placeholder="Commencez à taper…"
                       class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                <datalist id="datalist-starter">
                    @foreach(($dishesByCategory->get('starter', collect())) as $dish)
                    <option value="{{ $dish->name }}" data-allergens="{{ implode(', ', $dish->allergens ?? []) }}" data-vegetarian="{{ $dish->vegetarian ? '1' : '0' }}">
                    @endforeach
                </datalist>
            </div>

            <!-- Plat principal -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Plat principal <span class="text-red-500">*</span></label>
                <input type="text" name="main_course" id="dish-main_course" value="{{ old('main_course', $menu->main_course) }}"
                       list="datalist-main_course" autocomplete="off"
                       placeholder="Commencez à taper…" required
                       class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                <datalist id="datalist-main_course">
                    @foreach(($dishesByCategory->get('main_course', collect())) as $dish)
                    <option value="{{ $dish->name }}" data-allergens="{{ implode(', ', $dish->allergens ?? []) }}" data-vegetarian="{{ $dish->vegetarian ? '1' : '0' }}">
                    @endforeach
                </datalist>
                @error('main_course')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Accompagnement -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Accompagnement</label>
                <input type="text" name="side_dish" id="dish-side_dish" value="{{ old('side_dish', $menu->side_dish) }}"
                       list="datalist-side_dish" autocomplete="off"
                       placeholder="Commencez à taper…"
                       class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                <datalist id="datalist-side_dish">
                    @foreach(($dishesByCategory->get('side_dish', collect())) as $dish)
                    <option value="{{ $dish->name }}" data-allergens="{{ implode(', ', $dish->allergens ?? []) }}" data-vegetarian="{{ $dish->vegetarian ? '1' : '0' }}">
                    @endforeach
                </datalist>
            </div>

            <!-- Dessert -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Dessert</label>
                <input type="text" name="dessert" id="dish-dessert" value="{{ old('dessert', $menu->dessert) }}"
                       list="datalist-dessert" autocomplete="off"
                       placeholder="Commencez à taper…"
                       class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                <datalist id="datalist-dessert">
                    @foreach(($dishesByCategory->get('dessert', collect())) as $dish)
                    <option value="{{ $dish->name }}" data-allergens="{{ implode(', ', $dish->allergens ?? []) }}" data-vegetarian="{{ $dish->vegetarian ? '1' : '0' }}">
                    @endforeach
                </datalist>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Allergènes (séparés par des virgules)</label>
                <input type="text" name="allergens_input" id="allergens_input" value="{{ old('allergens_input', is_array($menu->allergens) ? implode(', ', $menu->allergens) : '') }}"
                       placeholder="Ex. gluten, lactose, œufs, arachides"
                       class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                <p class="text-xs text-gray-500 mt-1">Rempli automatiquement si vous sélectionnez un plat connu. Liste : gluten, crustacés, œufs, poissons, arachides, soja, lait, fruits à coque, céleri, moutarde, sésame, sulfites, lupin, mollusques</p>
            </div>

            <div class="mb-4 flex items-center gap-6">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                    <input type="checkbox" name="vegetarian" value="1" id="vegetarian" @checked(old('vegetarian', $menu->vegetarian))
                           class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                    Végétarien
                </label>
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $menu->is_published))
                           class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                    Publier (visible par les parents)
                </label>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2" placeholder="Informations complémentaires…"
                          class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">{{ old('notes', $menu->notes) }}</textarea>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('cantine.menus.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">Annuler</a>
                <button type="submit" class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium shadow-md">
                    <i class="fas fa-save mr-2"></i>{{ $menu->exists ? 'Enregistrer' : 'Créer le menu' }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Autocomplétion : quand on sélectionne un plat connu, on pré-remplit allergènes + végé
const dishMap = @json($dishMap);

['starter', 'main_course', 'side_dish', 'dessert'].forEach(category => {
    const input = document.getElementById('dish-' + category);
    if (!input) return;

    input.addEventListener('change', function() {
        const name = this.value.trim();
        const dishes = dishMap[category] || [];
        const match = dishes.find(d => d.name.toLowerCase() === name.toLowerCase());
        if (!match) return;

        // Pré-remplir les allergènes
        const allergensInput = document.getElementById('allergens_input');
        if (match.allergens && match.allergens.length > 0) {
            allergensInput.value = match.allergens.join(', ');
        }

        // Cocher végétarien
        const vegCheckbox = document.getElementById('vegetarian');
        if (match.vegetarian) {
            vegCheckbox.checked = true;
        }
    });
});
</script>
@endsection
