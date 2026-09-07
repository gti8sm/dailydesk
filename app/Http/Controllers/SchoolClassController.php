<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Child;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Accès non autorisé');
        }

        $year = $request->get('year', now()->year . '-' . (now()->year + 1));

        $classes = SchoolClass::where('school_year', $year)
            ->withCount(['children'])
            ->orderBy('name')
            ->get();

        $years = SchoolClass::select('school_year')
            ->distinct()
            ->orderBy('school_year', 'desc')
            ->pluck('school_year');

        return view('classes.index', compact('classes', 'year', 'years'));
    }

    public function create()
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Accès non autorisé');
        }

        $currentYear = now()->year . '-' . (now()->year + 1);

        return view('classes.create', compact('currentYear'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'teacher_name' => 'nullable|string|max:100',
            'school_year' => 'required|string|max:20',
        ]);

        SchoolClass::create($validated);

        return redirect()->route('classes.index', ['year' => $validated['school_year']])
            ->with('success', 'Classe créée avec succès');
    }

    public function edit(SchoolClass $class)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Accès non autorisé');
        }

        return view('classes.edit', compact('class'));
    }

    public function update(Request $request, SchoolClass $class)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'teacher_name' => 'nullable|string|max:100',
            'school_year' => 'required|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $class->update($validated);

        return redirect()->route('classes.index', ['year' => $class->school_year])
            ->with('success', 'Classe modifiée avec succès');
    }

    public function destroy(SchoolClass $class)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Accès non autorisé');
        }

        $year = $class->school_year;
        $class->delete();

        return redirect()->route('classes.index', ['year' => $year])
            ->with('success', 'Classe supprimée');
    }
}
