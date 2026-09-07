<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Family;
use Illuminate\Http\Request;

class ChildController extends Controller
{
    public function create(Family $family)
    {
        return view('children.create', compact('family'));
    }

    public function store(Request $request, Family $family)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'class_id' => 'nullable|exists:school_classes,id',
            'allergies' => 'nullable|string',
            'dietary_restrictions' => 'nullable|string',
            'medical_notes' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $validated['family_id'] = $family->id;
        $validated['is_active'] = true;
        $validated['garderie_subscribed'] = $request->has('garderie_subscribed');
        $validated['cantine_subscribed'] = $request->has('cantine_subscribed');

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('children', 'public');
            $validated['photo_path'] = $path;
        }

        $child = Child::create($validated);

        return redirect()->route('families.show', $family)
            ->with('success', "L'enfant {$child->full_name} a été ajouté avec succès !");
    }

    public function edit(Child $child)
    {
        return view('children.edit', compact('child'));
    }

    public function update(Request $request, Child $child)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'class_id' => 'nullable|exists:school_classes,id',
            'allergies' => 'nullable|string',
            'dietary_restrictions' => 'nullable|string',
            'medical_notes' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
            'is_active' => 'boolean',
            'photo' => 'nullable|image|max:2048',
        ]);

        $validated['garderie_subscribed'] = $request->has('garderie_subscribed');
        $validated['cantine_subscribed'] = $request->has('cantine_subscribed');

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('children', 'public');
            $validated['photo_path'] = $path;
        }

        $child->update($validated);

        return redirect()->route('families.show', $child->family)
            ->with('success', "Les informations de {$child->full_name} ont été mises à jour !");
    }

    public function destroy(Child $child)
    {
        $family = $child->family;
        $name = $child->full_name;
        
        $child->delete();

        return redirect()->route('families.show', $family)
            ->with('success', "L'enfant {$name} a été supprimé.");
    }
}
