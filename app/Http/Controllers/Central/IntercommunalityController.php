<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Intercommunality;
use App\Models\Tenant;
use Illuminate\Http\Request;

class IntercommunalityController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403);
        }

        $intercommunalities = Intercommunality::withCount('tenants')
            ->orderBy('name')
            ->paginate(15);

        return view('central.intercommunalities.index', compact('intercommunalities'));
    }

    public function create()
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403);
        }

        $tenants = Tenant::orderBy('name')->get();

        return view('central.intercommunalities.create', compact('tenants'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:communaute_communes,communaute_agglomeration,syndicat,epci',
            'siren' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'tenant_ids' => 'nullable|array',
            'tenant_ids.*' => 'exists:tenants,id',
        ]);

        $interco = Intercommunality::create($validated);

        if (!empty($validated['tenant_ids'])) {
            $interco->tenants()->sync($validated['tenant_ids']);
        }

        return redirect()->route('central.intercommunalities.show', $interco)
            ->with('success', 'Intercommunalité créée avec succès.');
    }

    public function show(Intercommunality $intercommunality)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403);
        }

        $intercommunality->load(['tenants', 'schools']);

        return view('central.intercommunalities.show', compact('intercommunality'));
    }

    public function edit(Intercommunality $intercommunality)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403);
        }

        $tenants = Tenant::orderBy('name')->get();
        $intercommunality->load('tenants');

        return view('central.intercommunalities.edit', compact('intercommunality', 'tenants'));
    }

    public function update(Request $request, Intercommunality $intercommunality)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:communaute_communes,communaute_agglomeration,syndicat,epci',
            'siren' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
            'tenant_ids' => 'nullable|array',
            'tenant_ids.*' => 'exists:tenants,id',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $intercommunality->update($validated);

        $intercommunality->tenants()->sync($validated['tenant_ids'] ?? []);

        return redirect()->route('central.intercommunalities.show', $intercommunality)
            ->with('success', 'Intercommunalité modifiée avec succès.');
    }

    public function destroy(Intercommunality $intercommunality)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403);
        }

        // Détacher les tenants avant suppression
        $intercommunality->tenants()->detach();

        // Retirer l'intercommunality_id des écoles
        $intercommunality->schools()->update(['intercommunality_id' => null]);

        $name = $intercommunality->name;
        $intercommunality->delete();

        return redirect()->route('central.intercommunalities.index')
            ->with('success', "Intercommunalité '{$name}' supprimée.");
    }
}
