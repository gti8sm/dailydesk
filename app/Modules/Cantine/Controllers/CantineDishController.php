<?php

namespace App\Modules\Cantine\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cantine\Models\CantineDish;
use Illuminate\Http\Request;

class CantineDishController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category');

        $query = CantineDish::with('createdBy')->orderBy('category')->orderBy('name');

        if ($category && in_array($category, ['starter', 'main_course', 'side_dish', 'dessert'])) {
            $query->forCategory($category);
        }

        $dishes = $query->get()->groupBy('category');

        return view('cantine.dishes.index', compact('dishes', 'category'));
    }

    public function create()
    {
        $dish = new CantineDish();

        return view('cantine.dishes.form', compact('dish'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateDish($request);
        $validated['created_by'] = auth()->id();
        $validated['allergens'] = $this->parseAllergens($request->get('allergens_input'));

        CantineDish::create($validated);

        return redirect()->route('cantine.dishes.index')
            ->with('success', 'Plat enregistré.');
    }

    public function edit(CantineDish $dish)
    {
        return view('cantine.dishes.form', compact('dish'));
    }

    public function update(Request $request, CantineDish $dish)
    {
        $validated = $this->validateDish($request);
        $validated['allergens'] = $this->parseAllergens($request->get('allergens_input'));

        $dish->update($validated);

        return redirect()->route('cantine.dishes.index')
            ->with('success', 'Plat modifié.');
    }

    public function destroy(CantineDish $dish)
    {
        $dish->delete();

        return redirect()->route('cantine.dishes.index')
            ->with('success', 'Plat supprimé.');
    }

    public function search(Request $request)
    {
        $request->validate([
            'category' => 'required|in:starter,main_course,side_dish,dessert',
            'q' => 'nullable|string|max:100',
        ]);

        $dishes = CantineDish::forCategory($request->category)
            ->when($request->filled('q'), fn($q) => $q->where('name', 'like', '%' . $request->q . '%'))
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'allergens', 'vegetarian']);

        return response()->json($dishes);
    }

    private function validateDish(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:starter,main_course,side_dish,dessert',
            'allergens_input' => 'nullable|string|max:500',
            'vegetarian' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);
    }

    private function parseAllergens(?string $input): ?array
    {
        if (empty($input)) {
            return null;
        }

        return collect(array_map('trim', explode(',', $input)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
