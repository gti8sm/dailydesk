<?php

namespace App\Modules\Stock\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Stock\Models\StockItem;
use App\Modules\Stock\Models\StockMovement;
use App\Modules\Stock\Models\StockLocation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with(['stockItem.location', 'createdBy'])->latest();

        if ($request->filled('stock_item_id')) {
            $query->where('stock_item_id', $request->stock_item_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('location_id')) {
            $query->whereHas('stockItem', fn($q) => $q->where('location_id', $request->location_id));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->paginate(50)->withQueryString();
        $locations = StockLocation::active()->orderBy('name')->get();
        $types = ['in' => 'Entrée', 'out' => 'Sortie', 'adjust' => 'Ajustement'];

        return view('stock.movements.index', compact('movements', 'locations', 'types'));
    }

    public function create(Request $request)
    {
        $items = StockItem::active()->with('location')->orderBy('name')->get();
        $preselectedItem = $request->get('stock_item_id');

        return view('stock.movements.form', compact('items', 'preselectedItem'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'stock_item_id' => 'required|exists:stock_items,id',
            'type' => 'required|in:in,out,adjust',
            'quantity' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:255',
        ]);

        $item = StockItem::findOrFail($validated['stock_item_id']);

        $newQuantity = match ($validated['type']) {
            'in' => $item->quantity + $validated['quantity'],
            'out' => max(0, $item->quantity - $validated['quantity']),
            'adjust' => $validated['quantity'],
        };

        $item->update(['quantity' => $newQuantity]);

        $movement = StockMovement::create([
            'stock_item_id' => $item->id,
            'type' => $validated['type'],
            'quantity' => $validated['quantity'],
            'reason' => $validated['reason'],
            'new_quantity' => $newQuantity,
            'created_by' => auth()->id(),
        ]);

        $this->checkLowStock($item);

        return redirect()->route('stock.movements.index')
            ->with('success', "Mouvement enregistré : {$movement->type_label} de {$validated['quantity']} {$item->unit} pour {$item->name}.");
    }

    private function checkLowStock(StockItem $item): void
    {
        $item->refresh();

        if (!$item->is_low_stock) {
            return;
        }

        $admins = User::where('tenant_id', $item->tenant_id)
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'admin_mairie']))
            ->get();

        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new \App\Mail\StockAlertMail($item, $admin));
            } catch (\Exception $e) {
                // Mail may fail in dev
            }
        }
    }
}
