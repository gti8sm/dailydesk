<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Central\SubscriptionPlan;

class StatisticsController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }
        
        $tenants = Tenant::with('domains')->get();
        $plans = SubscriptionPlan::all();
        
        // Statistiques générales
        $stats = [
            'total_tenants' => $tenants->count(),
            'active_tenants' => $tenants->where('status', 'active')->count(),
            'suspended_tenants' => $tenants->where('status', 'suspended')->count(),
            'cancelled_tenants' => $tenants->where('status', 'cancelled')->count(),
            'trial_tenants' => $tenants->filter(function($tenant) {
                return $tenant->trial_ends_at && $tenant->trial_ends_at->isFuture();
            })->count(),
            'expired_trials' => $tenants->filter(function($tenant) {
                return $tenant->trial_ends_at && $tenant->trial_ends_at->isPast();
            })->count(),
        ];
        
        // Statistiques par plan
        $planStats = [];
        foreach ($plans as $plan) {
            $planTenants = $tenants->where('subscription_plan', $plan->slug);
            $planStats[] = [
                'name' => $plan->name,
                'slug' => $plan->slug,
                'count' => $planTenants->count(),
                'active' => $planTenants->where('status', 'active')->count(),
                'revenue_monthly' => $planTenants->count() * $plan->price_monthly,
                'revenue_yearly' => $planTenants->count() * ($plan->price_yearly ?? $plan->price_monthly * 12),
            ];
        }
        
        // Revenus
        $revenue = [
            'monthly' => $tenants->sum(function($tenant) use ($plans) {
                $plan = $plans->firstWhere('slug', $tenant->subscription_plan);
                return $plan ? $plan->price_monthly : 0;
            }),
            'yearly' => $tenants->sum(function($tenant) use ($plans) {
                $plan = $plans->firstWhere('slug', $tenant->subscription_plan);
                return $plan ? ($plan->price_yearly ?? $plan->price_monthly * 12) : 0;
            }),
        ];
        
        // Croissance par mois (12 derniers mois)
        $monthlyGrowth = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyGrowth[$date->format('M Y')] = $tenants->filter(function($tenant) use ($date) {
                return $tenant->created_at && 
                       $tenant->created_at->year == $date->year && 
                       $tenant->created_at->month == $date->month;
            })->count();
        }
        
        // Taux de conversion (essai -> payant)
        $totalTrials = $tenants->filter(function($tenant) {
            return $tenant->trial_ends_at !== null;
        })->count();
        
        $convertedTrials = $tenants->filter(function($tenant) {
            return $tenant->trial_ends_at && 
                   $tenant->trial_ends_at->isPast() && 
                   $tenant->status === 'active';
        })->count();
        
        $conversionRate = $totalTrials > 0 ? round(($convertedTrials / $totalTrials) * 100, 1) : 0;
        
        // Taux de rétention
        $activeRate = $stats['total_tenants'] > 0 ? round(($stats['active_tenants'] / $stats['total_tenants']) * 100, 1) : 0;
        
        return view('central.statistics.index', compact(
            'stats',
            'planStats',
            'revenue',
            'monthlyGrowth',
            'conversionRate',
            'activeRate',
            'tenants'
        ));
    }
}
