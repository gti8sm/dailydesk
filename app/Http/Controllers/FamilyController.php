<?php

namespace App\Http\Controllers;

use App\Models\Family;
use Illuminate\Http\Request;

class FamilyController extends Controller
{
    public function index()
    {
        return view('families.index');
    }

    public function create()
    {
        return view('families.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'family_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ]);

        $family = Family::create($validated);

        return redirect()->route('families.index')
            ->with('success', 'Famille créée avec succès !');
    }

    public function show(Family $family)
    {
        $family->load('children');
        return view('families.show', compact('family'));
    }

    public function edit(Family $family)
    {
        return view('families.edit', compact('family'));
    }

    public function update(Request $request, Family $family)
    {
        $validated = $request->validate([
            'family_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ]);

        $family->update($validated);

        return redirect()->route('families.index')
            ->with('success', 'Famille mise à jour avec succès !');
    }

    public function destroy(Family $family)
    {
        $family->delete();

        return redirect()->route('families.index')
            ->with('success', 'Famille supprimée avec succès !');
    }
}
