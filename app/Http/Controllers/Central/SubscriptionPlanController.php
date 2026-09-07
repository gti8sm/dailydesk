<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Central\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }
        
        $plans = SubscriptionPlan::orderBy('price_monthly')->get();
        return view('central.plans.index', compact('plans'));
    }

    public function create()
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }
        
        return view('central.plans.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subscription_plans,slug|alpha_dash',
            'description' => 'nullable|string',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'nullable|numeric|min:0',
            'max_children' => 'required|integer|min:1',
            'modules' => 'required|array|min:1',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $plan = SubscriptionPlan::create($validated);

        return redirect()
            ->route('central.plans.index')
            ->with('success', "Plan '{$plan->name}' créé avec succès !");
    }

    public function edit(SubscriptionPlan $plan)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }
        
        return view('central.plans.edit', compact('plan'));
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'nullable|numeric|min:0',
            'max_children' => 'required|integer|min:1',
            'modules' => 'required|array|min:1',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $plan->update($validated);

        return redirect()
            ->route('central.plans.index')
            ->with('success', "Plan '{$plan->name}' mis à jour avec succès !");
    }

    public function destroy(SubscriptionPlan $plan)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier si des tenants utilisent ce plan
        $tenantsCount = \App\Models\Tenant::where('subscription_plan', $plan->slug)->count();
        
        if ($tenantsCount > 0) {
            return redirect()
                ->back()
                ->with('error', "Impossible de supprimer ce plan : {$tenantsCount} tenant(s) l'utilisent actuellement.");
        }

        $planName = $plan->name;
        $plan->delete();

        return redirect()
            ->route('central.plans.index')
            ->with('success', "Plan '{$planName}' supprimé avec succès !");
    }

    public function toggleStatus(SubscriptionPlan $plan)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }

        $plan->update(['is_active' => !$plan->is_active]);

        $message = $plan->is_active 
            ? "Plan '{$plan->name}' activé avec succès !" 
            : "Plan '{$plan->name}' désactivé avec succès !";

        return redirect()
            ->back()
            ->with('success', $message);
    }
}
