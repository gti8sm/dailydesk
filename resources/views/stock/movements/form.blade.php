@extends('layouts.app')

@section('title', 'Nouveau mouvement de stock')

@section('content')
<div class="max-w-xl mx-auto px-2 sm:px-4 lg:px-8">
    <div class="mb-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
            <i class="fas fa-arrow-right-arrow-left text-indigo-600 mr-2"></i>Nouveau mouvement
        </h1>
    </div>

    <div class="bg-white shadow-lg rounded-xl p-6">
        <form action="{{ route('stock.movements.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Article <span class="text-red-500">*</span></label>
                <select name="stock_item_id" id="stock_item_id" required
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">— Choisir un article —</option>
                    @foreach($items as $item)
                    <option value="{{ $item->id }}" data-quantity="{{ $item->quantity }}" data-unit="{{ $item->unit }}"
                            @selected(old('stock_item_id', $preselectedItem) == $item->id)>
                        {{ $item->name }} ({{ $item->location?->name ?? '—' }}) — Stock: {{ number_format($item->quantity, 2) }} {{ $item->unit }}
                    </option>
                    @endforeach
                </select>
                @error('stock_item_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <!-- Info stock actuel -->
            <div id="current-stock-info" class="mb-4 p-3 bg-gray-50 rounded-lg text-sm text-gray-600 hidden">
                <i class="fas fa-box mr-1"></i>Stock actuel: <span id="current-quantity" class="font-bold">—</span>
            </div>

            <div class="grid grid-cols-3 gap-3 mb-4">
                <label class="cursor-pointer">
                    <input type="radio" name="type" value="in" @checked(old('type','in') === 'in') class="hidden peer">
                    <div class="p-3 border-2 rounded-lg text-center peer-checked:border-green-500 peer-checked:bg-green-50 transition-colors">
                        <i class="fas fa-arrow-down text-green-600 text-xl"></i>
                        <p class="text-sm font-medium mt-1">Entrée</p>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="type" value="out" @checked(old('type') === 'out') class="hidden peer">
                    <div class="p-3 border-2 rounded-lg text-center peer-checked:border-red-500 peer-checked:bg-red-50 transition-colors">
                        <i class="fas fa-arrow-up text-red-600 text-xl"></i>
                        <p class="text-sm font-medium mt-1">Sortie</p>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="type" value="adjust" @checked(old('type') === 'adjust') class="hidden peer">
                    <div class="p-3 border-2 rounded-lg text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-colors">
                        <i class="fas fa-equals text-blue-600 text-xl"></i>
                        <p class="text-sm font-medium mt-1">Ajust.</p>
                    </div>
                </label>
            </div>
            @error('type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1" id="quantity-label">
                    Quantité <span class="text-red-500">*</span>
                </label>
                <input type="number" name="quantity" value="{{ old('quantity') }}" step="0.01" min="0.01" required
                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                @error('quantity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                <p id="quantity-hint" class="text-xs text-gray-400 mt-1"></p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Motif</label>
                <input type="text" name="reason" value="{{ old('reason') }}"
                       placeholder="Ex. Commande fournisseur, Repas du midi, Inventaire…"
                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('stock.movements.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">Annuler</a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium shadow-md">
                    <i class="fas fa-check mr-2"></i>Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('stock_item_id').addEventListener('change', function() {
    const opt = this.selectedOptions[0];
    const info = document.getElementById('current-stock-info');
    const qty = document.getElementById('current-quantity');
    const hint = document.getElementById('quantity-hint');
    const label = document.getElementById('quantity-label');

    if (this.value && opt.dataset.quantity !== undefined) {
        info.classList.remove('hidden');
        qty.textContent = opt.dataset.quantity + ' ' + opt.dataset.unit;
        hint.textContent = 'Pour ajustement: saisir la quantité réelle exacte';
    } else {
        info.classList.add('hidden');
        hint.textContent = '';
    }
});

// Mettre à jour le label selon le type
document.querySelectorAll('input[name="type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const label = document.getElementById('quantity-label');
        if (this.value === 'adjust') {
            label.innerHTML = 'Nouvelle quantité <span class="text-red-500">*</span>';
        } else {
            label.innerHTML = 'Quantité <span class="text-red-500">*</span>';
        }
    });
});
</script>
@endsection
