<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Family;
use App\Modules\Garderie\Models\GarderiePresence;
use App\Modules\Garderie\Models\GarderieEvent;
use App\Modules\Cantine\Models\CantinePresence;
use App\Modules\Cantine\Models\CantineEvent;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Dashboard Super Admin (base centrale)
        if ($user->hasRole('super_admin')) {
            return $this->superAdminDashboard();
        }
        
        // Redirection intelligente pour les rôles ALSH et Cantine selon l'heure
        if ($user->hasRole(['alsh', 'cantine'])) {
            $currentHour = now()->hour;
            
            // 7h-10h : Garderie matin
            if ($currentHour >= 7 && $currentHour < 10) {
                return redirect()->route('garderie.index');
            }
            
            // 10h-14h : Cantine
            if ($currentHour >= 10 && $currentHour < 14) {
                return redirect()->route('cantine.index');
            }
            
            // 14h-19h : Garderie soir
            if ($currentHour >= 14 && $currentHour < 19) {
                return redirect()->route('garderie.index');
            }
            
            // Hors horaires : redirection vers garderie par défaut
            return redirect()->route('garderie.index');
        }

        // Redirection parent vers son portail
        if ($user->hasRole('parent')) {
            return redirect()->route('parent.dashboard');
        }
        
        $stats = [
            'garderie_today' => 0,
            'cantine_today' => 0,
            'families_count' => 0,
            'children_count' => 0,
        ];

        if ($user->can('view_garderie')) {
            $stats['garderie_today'] = GarderiePresence::forDate(today())->count();
        }

        if ($user->can('view_cantine')) {
            $stats['cantine_today'] = CantinePresence::forDate(today())->count();
        }

        if ($user->can('manage_families')) {
            $stats['families_count'] = Family::where('is_active', true)->count();
        }

        if ($user->can('manage_children')) {
            $stats['children_count'] = Child::where('is_active', true)->count();
        }

        $recent_garderie_events = null;
        if ($user->can('view_garderie_events')) {
            $recent_garderie_events = GarderieEvent::with(['child', 'createdBy'])
                ->orderBy('event_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        $recent_cantine_events = null;
        if ($user->can('view_cantine_events')) {
            $recent_cantine_events = CantineEvent::with(['child', 'createdBy'])
                ->orderBy('event_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        $my_children = null;
        if ($user->hasRole('parent') && $user->parent) {
            $my_children = Child::where('family_id', $user->parent->family_id)
                ->where('is_active', true)
                ->get();
        }

        return view('dashboard.index', compact(
            'stats',
            'recent_garderie_events',
            'recent_cantine_events',
            'my_children'
        ));
    }

    private function superAdminDashboard()
    {
        $tenants = \App\Models\Tenant::with('domains')->get();
        $plans = \App\Models\Central\SubscriptionPlan::all();
        
        $stats = [
            'total_tenants' => $tenants->count(),
            'active_tenants' => $tenants->where('status', 'active')->count(),
            'suspended_tenants' => $tenants->where('status', 'suspended')->count(),
            'trial_tenants' => $tenants->filter(function($tenant) {
                return $tenant->trial_ends_at && $tenant->trial_ends_at->isFuture();
            })->count(),
            'expired_trials' => $tenants->filter(function($tenant) {
                return $tenant->trial_ends_at && $tenant->trial_ends_at->isPast();
            })->count(),
            'total_plans' => $plans->count(),
            'active_plans' => $plans->where('is_active', true)->count(),
        ];
        
        // Statistiques par plan
        $planStats = [];
        foreach ($plans as $plan) {
            $planStats[$plan->slug] = [
                'name' => $plan->name,
                'count' => $tenants->where('subscription_plan', $plan->slug)->count(),
                'revenue' => $tenants->where('subscription_plan', $plan->slug)->count() * $plan->price_monthly,
            ];
        }
        
        // Revenus mensuels totaux
        $monthlyRevenue = $tenants->sum(function($tenant) use ($plans) {
            $plan = $plans->firstWhere('slug', $tenant->subscription_plan);
            return $plan ? $plan->price_monthly : 0;
        });
        
        // Nouveaux tenants ce mois
        $newThisMonth = $tenants->filter(function($tenant) {
            return $tenant->created_at && $tenant->created_at->isCurrentMonth();
        })->count();
        
        // Tenants créés par mois (6 derniers mois)
        $monthlyGrowth = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyGrowth[$date->format('M Y')] = $tenants->filter(function($tenant) use ($date) {
                return $tenant->created_at && 
                       $tenant->created_at->year == $date->year && 
                       $tenant->created_at->month == $date->month;
            })->count();
        }
        
        $stats['monthly_revenue'] = $monthlyRevenue;
        $stats['new_this_month'] = $newThisMonth;
        
        return view('dashboard.super-admin', compact('tenants', 'stats', 'planStats', 'monthlyGrowth'));
    }
}
