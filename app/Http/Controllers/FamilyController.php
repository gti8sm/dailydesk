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
     * Export RGPD (droit d'accès / portabilité) — toutes les données
     * détenues sur la famille au format JSON.
     */
    public function exportRgpd(Family $family)
    {
        $family->load(['parents', 'children']);

        $data = [
            'export' => [
                'date' => now()->toIso8601String(),
                'objet' => 'Droit d\'accès — export des données famille (RGPD art. 15)',
                'logiciel' => 'DailyDesk',
            ],
            'famille' => $family->only(['id', 'family_name', 'address', 'postal_code', 'city', 'phone', 'email', 'notes', 'is_active', 'created_at', 'updated_at']),
            'parents' => $family->parents->map(fn ($p) => $p->only(['first_name', 'last_name', 'email', 'phone', 'mobile', 'address', 'postal_code', 'city', 'relationship', 'is_primary_contact', 'can_pickup', 'is_legal_guardian', 'created_at'])),
            'enfants' => $family->children->map(function ($c) {
                return [
                    'enfant' => $c->only(['first_name', 'last_name', 'birth_date', 'gender', 'allergies', 'medical_notes', 'cantine_subscribed', 'garderie_subscribed', 'created_at']),
                    'presences_cantine' => $c->cantinePresences()->select('date', 'is_present', 'meal_type')->orderBy('date')->get(),
                    'presences_garderie' => $c->garderiePresences()->select('date', 'arrival_time', 'departure_time')->orderBy('date')->get(),
                ];
            }),
        ];

        return response()->streamDownload(
            function () use ($data) {
                echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            },
            "rgpd_famille_{$family->id}.json",
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * Purge RGPD (droit à l'effacement — art. 17) : suppression
     * définitive de la famille et de toutes ses données liées.
     * Confirmation renforcée : saisie du nom de la famille.
     */
    public function purgeRgpd(Request $request, Family $family)
    {
        if ($request->input('confirm') !== $family->family_name) {
            return redirect()->route('families.show', $family)
                ->with('error', 'Purge annulée : vous devez saisir le nom exact de la famille (' . $family->family_name . ') pour confirmer.');
        }

        $family->load(['parents', 'children']);
        $childIds = $family->children->pluck('id');
        $parentIds = $family->parents->pluck('id');
        $familyName = $family->family_name;

        \DB::transaction(function () use ($family, $childIds, $parentIds) {
            // Présences et événements des enfants
            \App\Modules\Cantine\Models\CantinePresence::whereIn('child_id', $childIds)->delete();
            \App\Modules\Cantine\Models\CantineEvent::whereIn('child_id', $childIds)->delete();
            \App\Modules\Garderie\Models\GarderiePresence::whereIn('child_id', $childIds)->delete();
            \App\Modules\Garderie\Models\GarderieEvent::whereIn('child_id', $childIds)->delete();

            // Invitations en cours
            \App\Models\FamilyInvitation::where('family_id', $family->id)->delete();

            // Comptes portail parent liés, puis parents et enfants
            \App\Models\User::whereIn('id', \App\Models\ParentModel::whereIn('id', $parentIds)->pluck('user_id'))->delete();
            \App\Models\ParentModel::whereIn('id', $parentIds)->forceDelete();
            \App\Models\Child::whereIn('id', $childIds)->forceDelete();

            $family->forceDelete();
        });

        return redirect()->route('families.index')
            ->with('success', "Famille « {$familyName} » et toutes ses données associées ont été définitivement supprimées (purge RGPD).");
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

        // Normalise les booléens : une checkbox cochée sans attribut value
        // envoie "on", que la règle de validation boolean rejette.
        foreach ($filtered as &$parent) {
            foreach (['is_primary_contact', 'can_pickup', 'is_legal_guardian'] as $boolField) {
                if (array_key_exists($boolField, $parent)) {
                    $parent[$boolField] = in_array($parent[$boolField], [true, 1, '1', 'on'], true);
                }
            }
        }
        unset($parent);

        $request->merge(['parents' => $filtered]);
    }
}
