<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('admin_mairie')) {
            abort(403, 'Accès non autorisé');
        }

        $schools = School::withCount(['children', 'classes', 'users'])
            ->orderBy('name')
            ->get();

        return view('schools.index', compact('schools'));
    }

    public function create()
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('admin_mairie')) {
            abort(403, 'Accès non autorisé');
        }

        return view('schools.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('admin_mairie')) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'type' => 'required|in:maternelle,elementaire,primaire,college',
        ]);

        $school = School::create($validated);

        return redirect()->route('schools.index')
            ->with('success', "École '{$school->name}' créée avec succès");
    }

    public function edit(School $school)
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('admin_mairie')) {
            abort(403, 'Accès non autorisé');
        }

        return view('schools.edit', compact('school'));
    }

    public function update(Request $request, School $school)
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('admin_mairie')) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'type' => 'required|in:maternelle,elementaire,primaire,college',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $school->update($validated);

        return redirect()->route('schools.index')
            ->with('success', "École '{$school->name}' modifiée avec succès");
    }

    public function destroy(School $school)
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('admin_mairie')) {
            abort(403, 'Accès non autorisé');
        }

        if ($school->children()->count() > 0) {
            return redirect()->back()
                ->with('error', "Impossible de supprimer : {$school->children()->count()} enfant(s) sont rattachés à cette école.");
        }

        $schoolName = $school->name;
        $school->delete();

        return redirect()->route('schools.index')
            ->with('success', "École '{$schoolName}' supprimée");
    }

    /**
     * Sélectionne l'école active pour la session (admin global uniquement).
     */
    public function select(Request $request)
    {
        $request->validate([
            'school_id' => 'nullable|exists:schools,id',
        ]);

        if (auth()->user()->school_id) {
            return redirect()->back()
                ->with('error', 'Vous êtes limité à votre école.');
        }

        session(['selected_school_id' => $request->school_id]);

        return redirect()->back();
    }
}
