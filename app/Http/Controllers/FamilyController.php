<?php

namespace App\Http\Controllers;

use App\Models\Family;
use App\Models\ParentModel;
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
        $this->filterEmptyParentRows($request);

        $validated = $request->validate([
            'family_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'parents' => 'nullable|array',
            'parents.*.first_name' => 'required_with:parents|string|max:255',
            'parents.*.last_name' => 'required_with:parents|string|max:255',
            'parents.*.email' => 'nullable|email|max:255',
            'parents.*.phone' => 'nullable|string|max:20',
            'parents.*.mobile' => 'nullable|string|max:20',
            'parents.*.address' => 'nullable|string|max:255',
            'parents.*.postal_code' => 'nullable|string|max:10',
            'parents.*.city' => 'nullable|string|max:100',
            'parents.*.relationship' => 'required_with:parents|in:mother,father,guardian,other',
            'parents.*.is_primary_contact' => 'boolean',
            'parents.*.can_pickup' => 'boolean',
            'parents.*.is_legal_guardian' => 'boolean',
        ]);

        $family = Family::create($validated);

        if (!empty($validated['parents'])) {
            $hasPrimary = false;
            foreach ($validated['parents'] as $parentData) {
                if (empty($parentData['first_name']) && empty($parentData['last_name'])) {
                    continue;
                }
                $parentData['family_id'] = $family->id;
                $parentData['is_primary_contact'] = ($parentData['is_primary_contact'] ?? false) && !$hasPrimary;
                if ($parentData['is_primary_contact']) {
                    $hasPrimary = true;
                }
                $parentData['can_pickup'] = $parentData['can_pickup'] ?? true;
                $parentData['is_legal_guardian'] = $parentData['is_legal_guardian'] ?? false;
                ParentModel::create($parentData);
            }
        }

        return redirect()->route('families.show', $family)
            ->with('success', 'Famille créée avec succès !');
    }

    public function show(Family $family)
    {
        $family->load(['children', 'parents']);
        return view('families.show', compact('family'));
    }

    public function edit(Family $family)
    {
        $family->load('parents');
        return view('families.edit', compact('family'));
    }

    public function update(Request $request, Family $family)
    {
        $this->filterEmptyParentRows($request);

        $validated = $request->validate([
            'family_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'parents' => 'nullable|array',
            'parents.*.id' => 'nullable|exists:parents,id',
            'parents.*.first_name' => 'required_with:parents|string|max:255',
            'parents.*.last_name' => 'required_with:parents|string|max:255',
            'parents.*.email' => 'nullable|email|max:255',
            'parents.*.phone' => 'nullable|string|max:20',
            'parents.*.mobile' => 'nullable|string|max:20',
            'parents.*.address' => 'nullable|string|max:255',
            'parents.*.postal_code' => 'nullable|string|max:10',
            'parents.*.city' => 'nullable|string|max:100',
            'parents.*.relationship' => 'required_with:parents|in:mother,father,guardian,other',
            'parents.*.is_primary_contact' => 'boolean',
            'parents.*.can_pickup' => 'boolean',
            'parents.*.is_legal_guardian' => 'boolean',
        ]);

        $family->update($validated);

        if (isset($validated['parents'])) {
            $existingIds = [];
            $hasPrimary = false;
            foreach ($validated['parents'] as $parentData) {
                if (empty($parentData['first_name']) && empty($parentData['last_name'])) {
                    continue;
                }
                $parentData['family_id'] = $family->id;
                $parentData['is_primary_contact'] = ($parentData['is_primary_contact'] ?? false) && !$hasPrimary;
                if ($parentData['is_primary_contact']) {
                    $hasPrimary = true;
                }
                $parentData['can_pickup'] = $parentData['can_pickup'] ?? true;
                $parentData['is_legal_guardian'] = $parentData['is_legal_guardian'] ?? false;

                if (!empty($parentData['id'])) {
                    $parent = ParentModel::find($parentData['id']);
                    if ($parent && $parent->family_id === $family->id) {
                        $parent->update($parentData);
                        $existingIds[] = $parent->id;
                    }
                } else {
                    $parent = ParentModel::create($parentData);
                    $existingIds[] = $parent->id;
                }
            }

            $family->parents()->whereNotIn('id', $existingIds)->delete();
        }

        return redirect()->route('families.show', $family)
            ->with('success', 'Famille mise à jour avec succès !');
    }

    public function destroy(Family $family)
    {
        $family->delete();

        return redirect()->route('families.index')
            ->with('success', 'Famille supprimée avec succès !');
    }
    /**
     * Retire les lignes parents entièrement vides du formulaire.
     * Le formulaire envoie toujours une ligne par défaut (vide) : sans ce
     * filtre, required_with:parents la rejette et la création de famille
     * échoue silencieusement (aucune famille créée).
     * Les lignes vidées sur un parent existant conservent leur suppression
     * implicite (id absent de la synchronisation).
     */
    private function filterEmptyParentRows(Request $request): void
    {
        $parents = $request->input('parents');

        if (!is_array($parents)) {
            return;
        }

        $filtered = array_values(array_filter($parents, function ($parent) {
            if (!is_array($parent)) {
                return false;
            }
            return trim((string) ($parent['first_name'] ?? '')) !== ''
                || trim((string) ($parent['last_name'] ?? '')) !== '';
        }));

        $request->merge(['parents' => $filtered]);
    }
}
