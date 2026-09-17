<?php

namespace App\Modules\Cantine\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Cantine\Models\CantineMenu;
use App\Modules\Cantine\Models\CantineDish;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CantineMenuController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        $schoolId = \App\Models\User::getCurrentSchoolId();
        $menuMode = \App\Models\Setting::get('cantine_menu_mode', 'global');

        $query = CantineMenu::with('createdBy', 'school')->forMonth($year, $month);

        // Filtre par école si mode per_school ou si l'utilisateur est limité
        if ($menuMode === 'per_school' || $schoolId) {
            if ($schoolId) {
                $query->where('school_id', $schoolId);
            } else {
                // admin global en mode per_school : filtre par école sélectionnée si présente
                $selectedSchool = session('selected_school_id');
                if ($selectedSchool) {
                    $query->where('school_id', $selectedSchool);
                }
            }
        }

        if ($request->filled('meal_type')) {
            $query->where('meal_type', $request->meal_type);
        }

        if ($request->boolean('drafts_only')) {
            $query->where('is_published', false);
        }

        $menus = $query->orderBy('menu_date')->orderBy('meal_type')->get();

        $firstDay = Carbon::create($year, $month, 1);
        $monthName = $firstDay->locale('fr')->monthName;
        $prevMonth = $firstDay->copy()->subMonth();
        $nextMonth = $firstDay->copy()->addMonth();

        $schools = \App\Models\School::active()->orderBy('name')->get();

        return view('cantine.menus.index', compact(
            'menus', 'year', 'month', 'monthName', 'prevMonth', 'nextMonth', 'schools', 'menuMode'
        ));
    }

    public function create(Request $request)
    {
        $menu = new CantineMenu();
        $menu->menu_date = $request->get('date', now()->format('Y-m-d'));
        $menu->meal_type = $request->get('meal_type', 'lunch');

        $dishes = CantineDish::orderBy('category')->orderBy('name')->get()->groupBy('category');

        return view('cantine.menus.form', compact('menu', 'dishes'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateMenu($request);
        $validated['created_by'] = auth()->id();
        $validated['is_published'] = $request->boolean('is_published');
        $validated['allergens'] = $this->parseAllergens($request->get('allergens_input'));

        // Assigner school_id selon le mode
        $menuMode = \App\Models\Setting::get('cantine_menu_mode', 'global');
        $schoolId = \App\Models\User::getCurrentSchoolId();
        if ($menuMode === 'per_school') {
            $validated['school_id'] = $schoolId ?? $request->input('school_id');
        } else {
            $validated['school_id'] = null; // global
        }

        $menu = CantineMenu::create($validated);

        $this->saveDishesFromMenu($validated);

        return redirect()
            ->route('cantine.menus.index', ['year' => $menu->menu_date->year, 'month' => $menu->menu_date->month])
            ->with('success', 'Menu créé pour le ' . $menu->menu_date->format('d/m/Y') . '.');
    }

    public function edit(CantineMenu $menu)
    {
        $dishes = CantineDish::orderBy('category')->orderBy('name')->get()->groupBy('category');

        return view('cantine.menus.form', compact('menu', 'dishes'));
    }

    public function update(Request $request, CantineMenu $menu)
    {
        $validated = $this->validateMenu($request);
        $validated['is_published'] = $request->boolean('is_published');
        $validated['allergens'] = $this->parseAllergens($request->get('allergens_input'));

        $menu->update($validated);

        $this->saveDishesFromMenu($validated);

        return redirect()
            ->route('cantine.menus.index', ['year' => $menu->menu_date->year, 'month' => $menu->menu_date->month])
            ->with('success', 'Menu mis à jour.');
    }

    public function destroy(CantineMenu $menu)
    {
        $menu->delete();

        return redirect()
            ->route('cantine.menus.index')
            ->with('success', 'Menu supprimé.');
    }

    public function togglePublish(CantineMenu $menu)
    {
        $menu->update(['is_published' => !$menu->is_published]);

        return redirect()->back()->with(
            'success',
            $menu->is_published ? 'Menu publié — visible par les parents.' : 'Menu dépublié.'
        );
    }

    private function validateMenu(Request $request): array
    {
        return $request->validate([
            'menu_date' => 'required|date',
            'meal_type' => 'required|in:lunch,snack',
            'title' => 'nullable|string|max:255',
            'starter' => 'nullable|string|max:255',
            'main_course' => 'required|string|max:255',
            'side_dish' => 'nullable|string|max:255',
            'dessert' => 'nullable|string|max:255',
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

    private function saveDishesFromMenu(array $validated): void
    {
        $categoryFields = [
            'starter' => 'starter',
            'main_course' => 'main_course',
            'side_dish' => 'side_dish',
            'dessert' => 'dessert',
        ];

        $allergens = $validated['allergens'] ?? [];
        $vegetarian = $validated['vegetarian'] ?? false;

        foreach ($categoryFields as $field => $category) {
            $name = trim($validated[$field] ?? '');
            if (empty($name)) {
                continue;
            }

            CantineDish::firstOrCreate(
                [
                    'name' => $name,
                    'category' => $category,
                ],
                [
                    'allergens' => $allergens,
                    'vegetarian' => $vegetarian,
                    'created_by' => auth()->id(),
                ]
            );
        }
    }
}
