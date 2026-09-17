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

        // Check onboarding for admin users
        if ($user->hasRole('admin')) {
            $tenant = \App\Models\Tenant::find($user->tenant_id);
            if ($tenant) {
                $settings = $tenant->settings ?? [];
                $onboardingCompleted = $settings['onboarding_completed'] ?? false;
                if (!$onboardingCompleted && !request()->routeIs('onboarding.*')) {
                    return redirect()->route('onboarding.index');
                }
            }
        }
        
        $stats = [
            'garderie_today' => 0,
            'cantine_today' => 0,
            'families_count' => 0,
            'children_count' => 0,
            'stock_items' => 0,
            'stock_alerts' => 0,
        ];

        if ($user->can('view_garderie')) {
            $stats['garderie_today'] = GarderiePresence::forDate(today())->count();
        }

        if ($user->can('view_cantine')) {
            $stats['cantine_today'] = CantinePresence::forDate(today())->where('is_present', true)->count();
        }

        if ($user->can('manage_families')) {
            $stats['families_count'] = Family::where('is_active', true)->count();
        }

        if ($user->can('manage_children')) {
            $stats['children_count'] = Child::where('is_active', true)->count();
        }

        if ($user->can('view_stock')) {
            $stats['stock_items'] = \App\Modules\Stock\Models\StockItem::count();
            $stats['stock_alerts'] = \App\Modules\Stock\Models\StockItem::whereColumn('quantity', '<=', 'min_quantity')->count();
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

        // --- Nouvelles données pour le dashboard enrichi ---

        // 1. Répartition par école (pour les admins)
        $schoolStats = collect();
        if ($user->hasRole(['admin', 'admin_mairie']) && $user->can('view_garderie')) {
            $schools = \App\Models\School::active()->orderBy('name')->get();
            foreach ($schools as $school) {
                $childIds = \App\Models\Child::where('school_id', $school->id)->where('is_active', true)->pluck('id');
                $garderiePresent = GarderiePresence::forDate(today())->whereIn('child_id', $childIds)->count();
                $cantinePresent = 0;
                if ($user->can('view_cantine')) {
                    $cantinePresent = CantinePresence::forDate(today())
                        ->whereIn('child_id', $childIds)
                        ->where('is_present', true)
                        ->count();
                }
                $schoolStats->push([
                    'id' => $school->id,
                    'name' => $school->name,
                    'type' => $school->type_label,
                    'children_count' => $childIds->count(),
                    'garderie_present' => $garderiePresent,
                    'cantine_present' => $cantinePresent,
                ]);
            }
        }

        // 2. Présences 7 derniers jours (pour mini-graphique)
        $weeklyStats = [];
        if ($user->can('view_garderie') || $user->can('view_cantine')) {
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dayKey = $date->format('Y-m-d');
                $garderie = 0;
                $cantine = 0;
                if ($user->can('view_garderie')) {
                    $garderie = GarderiePresence::forDate($date)->count();
                }
                if ($user->can('view_cantine')) {
                    $cantine = CantinePresence::forDate($date)->where('is_present', true)->count();
                }
                $weeklyStats[] = [
                    'date' => $dayKey,
                    'day' => $date->translatedFormat('D'),
                    'day_num' => $date->format('d/m'),
                    'garderie' => $garderie,
                    'cantine' => $cantine,
                    'total' => $garderie + $cantine,
                ];
            }
        }

        // 3. Actions en attente (à traiter)
        $pendingActions = collect();
        if ($user->can('view_garderie_events')) {
            $unnotifiedGarderie = GarderieEvent::unnotified()
                ->whereDate('event_date', '>=', today()->subDays(7))
                ->count();
            if ($unnotifiedGarderie > 0) {
                $pendingActions->push([
                    'label' => $unnotifiedGarderie . ' événement(s) garderie non notifié(s)',
                    'url' => route('garderie.events.index'),
                    'icon' => 'fa-exclamation-triangle',
                    'color' => 'yellow',
                ]);
            }
        }
        if ($user->can('view_cantine_events')) {
            $unnotifiedCantine = CantineEvent::unnotified()
                ->whereDate('event_date', '>=', today()->subDays(7))
                ->count();
            if ($unnotifiedCantine > 0) {
                $pendingActions->push([
                    'label' => $unnotifiedCantine . ' événement(s) cantine non notifié(s)',
                    'url' => route('cantine.events.index'),
                    'icon' => 'fa-exclamation-triangle',
                    'color' => 'orange',
                ]);
            }
        }
        if ($user->can('view_stock') && $stats['stock_alerts'] > 0) {
            $pendingActions->push([
                'label' => $stats['stock_alerts'] . ' article(s) en alerte stock',
                'url' => route('stock.alerts.index'),
                'icon' => 'fa-bell',
                'color' => 'red',
            ]);
        }
        if ($user->hasRole(['admin', 'admin_mairie'])) {
            $pendingInvitations = \App\Models\FamilyInvitation::whereNull('accepted_at')
                ->where('expires_at', '>', now())
                ->count();
            if ($pendingInvitations > 0) {
                $pendingActions->push([
                    'label' => $pendingInvitations . ' invitation(s) famille en attente',
                    'url' => route('invitations.index'),
                    'icon' => 'fa-envelope-open-text',
                    'color' => 'blue',
                ]);
            }
        }

        // 4. Tickets de support non lus (côté tenant)
        if (class_exists(\App\Models\SupportTicket::class)) {
            $unreadTickets = \App\Models\SupportTicket::unreadByUser()->count();
            if ($unreadTickets > 0) {
                $pendingActions->push([
                    'label' => $unreadTickets . ' réponse(s) support non lue(s)',
                    'url' => route('support.index'),
                    'icon' => 'fa-life-ring',
                    'color' => 'purple',
                ]);
            }
        }

        // 5. Valeur max pour le graphique
        $weeklyMax = max(array_map(fn($d) => $d['total'], $weeklyStats)) ?: 1;

        $my_children = null;
        if ($user->hasRole('parent') && $user->parent) {
            $my_children = Child::where('family_id', $user->parent->family_id)
                ->where('is_active', true)
                ->get();
        }

        $tenant = \App\Models\Tenant::find($user->tenant_id);
        $plan = null;
        if ($tenant) {
            $plan = \App\Models\Central\SubscriptionPlan::where('slug', $tenant->subscription_plan)->first();
        }

        // Modules disponibles (config) filtrés par modules activés du tenant
        $allModules = config('modules', []);
        $enabledModules = $tenant?->modules_enabled ?? array_keys($allModules);
        $activeModules = array_filter($allModules, fn($key) => in_array($key, $enabledModules), ARRAY_FILTER_USE_KEY);

        return view('dashboard.index', compact(
            'stats',
            'recent_garderie_events',
            'recent_cantine_events',
            'my_children',
            'tenant',
            'plan',
            'activeModules',
            'schoolStats',
            'weeklyStats',
            'weeklyMax',
            'pendingActions'
        ));
    }

    private function superAdminDashboard()
    {
        $tenants = \App\Models\Tenant::with('domains')->latest()->paginate(10);
        $allTenants = \App\Models\Tenant::with('domains')->get();
        $plans = \App\Models\Central\SubscriptionPlan::all();
        
        $stats = [
            'total_tenants' => $allTenants->count(),
            'active_tenants' => $allTenants->where('status', 'active')->count(),
            'suspended_tenants' => $allTenants->where('status', 'suspended')->count(),
            'trial_tenants' => $allTenants->filter(function($tenant) {
                return $tenant->trial_ends_at && $tenant->trial_ends_at->isFuture();
            })->count(),
            'expired_trials' => $allTenants->filter(function($tenant) {
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
                'count' => $allTenants->where('subscription_plan', $plan->slug)->count(),
                'revenue' => $allTenants->where('subscription_plan', $plan->slug)->count() * $plan->price_monthly,
            ];
        }
        
        // Revenus mensuels totaux
        $monthlyRevenue = $allTenants->sum(function($tenant) use ($plans) {
            $plan = $plans->firstWhere('slug', $tenant->subscription_plan);
            return $plan ? $plan->price_monthly : 0;
        });
        
        // Nouveaux tenants ce mois
        $newThisMonth = $allTenants->filter(function($tenant) {
            return $tenant->created_at && $tenant->created_at->isCurrentMonth();
        })->count();
        
        // Tenants créés par mois (6 derniers mois)
        $monthlyGrowth = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyGrowth[$date->format('M Y')] = $allTenants->filter(function($tenant) use ($date) {
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
