<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Intercommunality;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('admin_mairie')) {
            abort(403, 'Accès non autorisé');
        }

        $schools = School::withCount(['children', 'classes', 'users'])
            ->with('intercommunality')
            ->orderBy('name')
            ->get();

        return view('schools.index', compact('schools'));
    }

    public function create()
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('admin_mairie')) {
            abort(403, 'Accès non autorisé');
        }

        $intercommunalities = Intercommunality::active()->orderBy('name')->get();

        return view('schools.create', compact('intercommunalities'));
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
            'intercommunality_id' => 'nullable|exists:intercommunalities,id',
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

        $intercommunalities = Intercommunality::active()->orderBy('name')->get();

        return view('schools.edit', compact('school', 'intercommunalities'));
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
            'intercommunality_id' => 'nullable|exists:intercommunalities,id',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['intercommunality_id'] = $request->input('intercommunality_id') ?: null;

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
     * Sélectionne l'école active pour la session.
     */
    public function select(Request $request)
    {
        $request->validate([
            'school_id' => 'nullable|exists:schools,id',
        ]);

        $user = auth()->user();

        // Admin global (school_id null) : peut sélectionner n'importe quelle école
        if (!$user->school_id) {
            session(['selected_school_id' => $request->school_id]);
            return redirect()->back();
        }

        // Agent limité : ne peut sélectionner que son école ou ses écoles de remplacement
        $schoolId = $request->school_id;
        if ($schoolId && !$user->canAccessSchool((int) $schoolId)) {
            return redirect()->back()
                ->with('error', 'Vous n\'avez pas accès à cette école.');
        }

        session(['selected_school_id' => $schoolId]);

        return redirect()->back();
    }
}
