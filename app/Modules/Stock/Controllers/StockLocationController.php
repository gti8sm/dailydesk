<?php

namespace App\Modules\Stock\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Stock\Models\StockLocation;
use Illuminate\Http\Request;

class StockLocationController extends Controller
{
    public function index(Request $request)
    {
        $query = StockLocation::with(['items', 'createdBy'])->orderBy('name');

        if ($request->boolean('active_only')) {
            $query->active();
        }

        $locations = $query->get();

        return view('stock.locations.index', compact('locations'));
    }

    public function create()
    {
        $location = new StockLocation();

        return view('stock.locations.form', compact('location'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);
        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->boolean('is_active', true);

        StockLocation::create($validated);

        return redirect()->route('stock.locations.index')
            ->with('success', 'Lieu de stockage créé.');
    }

    public function edit(StockLocation $location)
    {
        return view('stock.locations.form', compact('location'));
    }

    public function update(Request $request, StockLocation $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);

        $location->update($validated);

        return redirect()->route('stock.locations.index')
            ->with('success', 'Lieu de stockage modifié.');
    }

    public function destroy(StockLocation $location)
    {
        $location->update(['is_active' => false]);

        return redirect()->route('stock.locations.index')
            ->with('success', 'Lieu de stockage archivé.');
    }
}
