<?php

namespace App\Modules\Stock\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Stock\Models\StockItem;
use App\Modules\Stock\Models\StockLocation;
use App\Modules\Stock\Models\StockMovement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class StockItemController extends Controller
{
    public function index(Request $request)
    {
        $query = StockItem::with(['location', 'createdBy'])->active();

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->boolean('low_stock')) {
            $query->lowStock();
        }

        $items = $query->orderBy('name')->paginate(50)->withQueryString();
        $locations = StockLocation::active()->orderBy('name')->get();
        $categories = StockItem::active()->whereNotNull('category')->distinct()->pluck('category')->sort();

        $lowStockCount = StockItem::active()->lowStock()->count();

        return view('stock.items.index', compact('items', 'locations', 'categories', 'lowStockCount'));
    }

    public function create(Request $request)
    {
        $item = new StockItem();
        $item->quantity = 0;
        $item->min_quantity = 0;
        $item->unit = 'pièce';
        $item->location_id = $request->get('location_id');

        $locations = StockLocation::active()->orderBy('name')->get();

        return view('stock.items.form', compact('item', 'locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:stock_locations,id',
            'name' => 'required|string|max:255',
            'reference' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'unit' => 'required|string|max:20',
            'quantity' => 'required|numeric|min:0',
            'min_quantity' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);
        $validated['created_by'] = auth()->id();
        $validated['min_quantity'] = $validated['min_quantity'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active', true);

        $item = StockItem::create($validated);

        if ($validated['quantity'] > 0) {
            StockMovement::create([
                'stock_item_id' => $item->id,
                'type' => 'in',
                'quantity' => $validated['quantity'],
                'reason' => 'Stock initial',
                'new_quantity' => $validated['quantity'],
                'created_by' => auth()->id(),
            ]);
        }

        $this->checkLowStock($item);

        return redirect()->route('stock.items.index')
            ->with('success', 'Article créé.');
    }

    public function edit(StockItem $item)
    {
        $locations = StockLocation::active()->orderBy('name')->get();

        return view('stock.items.form', compact('item', 'locations'));
    }

    public function update(Request $request, StockItem $item)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:stock_locations,id',
            'name' => 'required|string|max:255',
            'reference' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'unit' => 'required|string|max:20',
            'min_quantity' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);
        $validated['min_quantity'] = $validated['min_quantity'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active', true);

        $item->update($validated);

        $this->checkLowStock($item);

        return redirect()->route('stock.items.index')
            ->with('success', 'Article modifié.');
    }

    public function destroy(StockItem $item)
    {
        $item->update(['is_active' => false]);

        return redirect()->route('stock.items.index')
            ->with('success', 'Article archivé.');
    }

    public function show(StockItem $item)
    {
        $item->load(['location', 'movements.createdBy']);
        $movements = $item->movements()->with('createdBy')->latest()->paginate(20);

        return view('stock.items.show', compact('item', 'movements'));
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
