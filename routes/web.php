<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\FamilyInvitationController;
use App\Http\Controllers\ParentPortalController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TenantModuleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Central\TenantController;
use App\Http\Controllers\Central\SubscriptionPlanController;
use App\Http\Controllers\Central\ImpersonationController;
use App\Http\Controllers\Central\ModuleController;
use App\Http\Controllers\Central\ActivityLogController;
use App\Http\Controllers\Central\StatisticsController;
use App\Http\Controllers\Central\ExportController as CentralExportController;
use App\Http\Controllers\Central\SupportTicketController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\TenantSupportController;
use App\Http\Controllers\OnboardingController;
use App\Modules\Garderie\Controllers\GarderiePresenceController;
use App\Modules\Garderie\Controllers\GarderieEventController;
use App\Modules\Cantine\Controllers\CantinePresenceController;
use App\Modules\Cantine\Controllers\CantineEventController;
use App\Modules\Cantine\Controllers\CantineMenuController;
use App\Modules\Cantine\Controllers\CantineDishController;
use App\Modules\Stock\Controllers\StockLocationController;
use App\Modules\Stock\Controllers\StockItemController;
use App\Modules\Stock\Controllers\StockMovementController;
use App\Modules\Stock\Controllers\StockAlertController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes (no auth, no tenancy)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->hasRole('super_admin')) {
            return redirect()->route('central.dashboard');
        }
        $tenant = \App\Models\Tenant::find($user->tenant_id);
        if ($tenant) {
            return redirect()->route('dashboard', ['tenant' => $tenant->slug]);
        }
    }
    return redirect()->route('landing');
});

Route::get('/accueil', [LandingPageController::class, 'index'])->name('landing');
Route::post('/accueil/register', [LandingPageController::class, 'registerProspect'])->name('landing.register')->middleware('throttle:3,1');
Route::get('/accueil/merci', [LandingPageController::class, 'thankYou'])->name('landing.thank-you');

Route::get('/cgv', function () {
    $plans = \App\Models\Central\SubscriptionPlan::active()->ordered()->get();
    return view('legal.cgv', compact('plans'));
})->name('legal.cgv');
Route::get('/mentions-legales', fn() => view('legal.mentions'))->name('legal.mentions');
Route::get('/rgpd', fn() => view('legal.rgpd'))->name('legal.rgpd');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('web');
Route::post('/login', [LoginController::class, 'login'])->middleware(['web', 'throttle:5,1']);
Route::get('/login/pin', fn() => redirect()->route('login'));

Route::get('/password/request', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/password/email', [LoginController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:3,1');
Route::get('/password/reset/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [LoginController::class, 'resetPassword'])->name('password.update')->middleware('throttle:5,1');

Route::get('/invitation/accept/{token}', [FamilyInvitationController::class, 'accept'])->name('invitation.accept');
Route::post('/invitation/register/{token}', [FamilyInvitationController::class, 'register'])->name('invitation.register');

/*
|--------------------------------------------------------------------------
| Central admin routes (auth, no tenancy — super admin)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('central')->name('central.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('statistics', [StatisticsController::class, 'index'])->name('statistics');
    Route::get('exports', [CentralExportController::class, 'index'])->name('exports');
    Route::post('exports/garderie', [CentralExportController::class, 'exportGarderie'])->name('exports.garderie');
    Route::post('exports/cantine', [CentralExportController::class, 'exportCantine'])->name('exports.cantine');

    Route::post('tenants/{tenant}/toggle-status', [TenantController::class, 'toggleStatus'])->name('tenants.toggle-status');
    Route::resource('tenants', TenantController::class);

    Route::post('plans/{plan}/toggle-status', [SubscriptionPlanController::class, 'toggleStatus'])->name('plans.toggle-status');
    Route::resource('plans', SubscriptionPlanController::class);

    Route::post('impersonate/{tenant}', [ImpersonationController::class, 'impersonate'])->name('impersonate');
    Route::get('restore-super-admin', [ImpersonationController::class, 'restoreSuperAdmin'])->name('restore-super-admin')->withoutMiddleware('auth');

    Route::get('modules', [ModuleController::class, 'overview'])->name('modules.overview');
    Route::get('modules/settings', [ModuleController::class, 'globalSettings'])->name('modules.settings');
    Route::post('modules/settings', [ModuleController::class, 'updateGlobalSettings'])->name('modules.settings.update');
    Route::post('modules/{tenant}/{module}/toggle', [ModuleController::class, 'toggleModule'])->name('modules.toggle');

    Route::get('logs', [ActivityLogController::class, 'index'])->name('logs.index');
    Route::get('logs/{log}', [ActivityLogController::class, 'show'])->name('logs.show');

    Route::get('support', [SupportTicketController::class, 'index'])->name('support.index');
    Route::get('support/{ticket}', [SupportTicketController::class, 'show'])->name('support.show');
    Route::post('support/{ticket}/comment', [SupportTicketController::class, 'comment'])->name('support.comment');
    Route::post('support/{ticket}/archive', [SupportTicketController::class, 'archive'])->name('support.archive');
    Route::post('support/{ticket}/unarchive', [SupportTicketController::class, 'unarchive'])->name('support.unarchive');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::put('/profile/pin', [ProfileController::class, 'updatePin'])->name('profile.pin');
    Route::put('/profile/whitelist', [ProfileController::class, 'updateIpWhitelist'])->name('profile.whitelist');
});

/*
|--------------------------------------------------------------------------
| Tenant routes (prefixed with {tenant}, tenancy initialized by slug)
|--------------------------------------------------------------------------
*/
Route::prefix('{tenant}')->middleware(['tenancy.slug', 'auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');

    // Onboarding wizard
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');
    Route::get('/onboarding/skip', [OnboardingController::class, 'skip'])->name('onboarding.skip');

    // Impersonation (tenant side)
    Route::get('/central/do-impersonate', [ImpersonationController::class, 'doImpersonate'])->name('central.do-impersonate')->withoutMiddleware('auth');
    Route::post('/stop-impersonating', [ImpersonationController::class, 'stopImpersonating'])->name('central.stop-impersonating')->withoutMiddleware('auth');

    // Support tickets (tenant side)
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [TenantSupportController::class, 'index'])->name('index');
        Route::get('/create', [TenantSupportController::class, 'create'])->name('create');
        Route::post('/', [TenantSupportController::class, 'store'])->name('store');
        Route::get('/{ticket}', [TenantSupportController::class, 'show'])->name('show');
        Route::post('/{ticket}/comment', [TenantSupportController::class, 'comment'])->name('comment');
    });

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::put('/profile/pin', [ProfileController::class, 'updatePin'])->name('profile.pin');
    Route::put('/profile/whitelist', [ProfileController::class, 'updateIpWhitelist'])->name('profile.whitelist');

    Route::get('/help', fn() => view('help.index'))->name('help');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index')->middleware('can:manage_settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update')->middleware('can:manage_settings');

    // Tenant module management (admin/admin_mairie)
    Route::get('/settings/modules', [TenantModuleController::class, 'index'])->name('tenant.modules.index');
    Route::post('/settings/modules/{module}/toggle', [TenantModuleController::class, 'toggle'])->name('tenant.modules.toggle');

    Route::prefix('exports')->name('exports.')->group(function () {
        Route::get('/', [ExportController::class, 'index'])->name('index');
        Route::post('/garderie', [ExportController::class, 'exportGarderie'])->name('garderie');
        Route::post('/cantine', [ExportController::class, 'exportCantine'])->name('cantine');
    });

    Route::prefix('imports')->name('imports.')->group(function () {
        Route::get('/', [ImportController::class, 'index'])->name('index');
        Route::post('/families', [ImportController::class, 'importFamilies'])->name('families');
        Route::post('/children', [ImportController::class, 'importChildren'])->name('children');
        Route::get('/template/families', [ImportController::class, 'downloadFamilyTemplate'])->name('template.families');
        Route::get('/template/children', [ImportController::class, 'downloadChildTemplate'])->name('template.children');
    });

    Route::prefix('garderie')->name('garderie.')->middleware('can:view_garderie')->group(function () {
        Route::get('/', [GarderiePresenceController::class, 'index'])->name('index');
        Route::get('/search', [GarderiePresenceController::class, 'search'])->name('search');
        Route::post('/children/{child}/arrival', [GarderiePresenceController::class, 'recordArrival'])->name('record.arrival')->middleware('can:record_garderie_presence');
        Route::post('/children/{child}/departure', [GarderiePresenceController::class, 'recordDeparture'])->name('record.departure')->middleware('can:record_garderie_presence');
        Route::delete('/presences/{presence}', [GarderiePresenceController::class, 'destroy'])->name('presences.destroy')->middleware('can:record_garderie_presence');
        Route::patch('/presences/{presence}', [GarderiePresenceController::class, 'update'])->name('presences.update')->middleware('can:record_garderie_presence');
        Route::get('/reports/monthly', [GarderiePresenceController::class, 'monthlyReport'])->name('reports.monthly');
        Route::get('/export/monthly', [GarderiePresenceController::class, 'exportMonthly'])->name('export.monthly')->middleware('can:export_data');
        Route::get('/events', [GarderieEventController::class, 'index'])->name('events.index');
        Route::get('/events/create', [GarderieEventController::class, 'create'])->name('events.create');
        Route::post('/events', [GarderieEventController::class, 'store'])->name('events.store');
        Route::get('/events/{event}', [GarderieEventController::class, 'show'])->name('events.show');
        Route::post('/events/{event}/notify', [GarderieEventController::class, 'markNotified'])->name('events.notify')->middleware('can:notify_event_parents');
    });

    Route::prefix('cantine')->name('cantine.')->middleware('can:view_cantine')->group(function () {
        Route::get('/', [CantinePresenceController::class, 'index'])->name('index');
        Route::get('/search', [CantinePresenceController::class, 'search'])->name('search');
        Route::post('/children/{child}/record', [CantinePresenceController::class, 'record'])->name('record')->middleware('can:record_cantine_presence');
        Route::delete('/presences/{presence}', [CantinePresenceController::class, 'destroy'])->name('presences.destroy')->middleware('can:record_cantine_presence');
        Route::patch('/presences/{presence}', [CantinePresenceController::class, 'update'])->name('presences.update')->middleware('can:record_cantine_presence');
        Route::get('/reports/monthly', [CantinePresenceController::class, 'monthlyReport'])->name('reports.monthly');
        Route::get('/export/monthly', [CantinePresenceController::class, 'exportMonthly'])->name('export.monthly')->middleware('can:export_data');
        Route::get('/events', [CantineEventController::class, 'index'])->name('events.index');
        Route::get('/events/create', [CantineEventController::class, 'create'])->name('events.create');
        Route::post('/events', [CantineEventController::class, 'store'])->name('events.store');
        Route::get('/events/{event}', [CantineEventController::class, 'show'])->name('events.show');
        Route::post('/events/{event}/notify', [CantineEventController::class, 'markNotified'])->name('events.notify')->middleware('can:notify_event_parents');

        Route::prefix('menus')->name('menus.')->middleware('can:manage_cantine_menus')->group(function () {
            Route::get('/', [CantineMenuController::class, 'index'])->name('index')->withoutMiddleware('can:manage_cantine_menus')->middleware('can:view_cantine_menus');
            Route::get('/create', [CantineMenuController::class, 'create'])->name('create');
            Route::post('/', [CantineMenuController::class, 'store'])->name('store');
            Route::get('/{menu}/edit', [CantineMenuController::class, 'edit'])->name('edit');
            Route::put('/{menu}', [CantineMenuController::class, 'update'])->name('update');
            Route::delete('/{menu}', [CantineMenuController::class, 'destroy'])->name('destroy');
            Route::post('/{menu}/toggle-publish', [CantineMenuController::class, 'togglePublish'])->name('togglePublish');
        });

        Route::prefix('dishes')->name('dishes.')->middleware('can:manage_cantine_menus')->group(function () {
            Route::get('/', [CantineDishController::class, 'index'])->name('index');
            Route::get('/create', [CantineDishController::class, 'create'])->name('create');
            Route::post('/', [CantineDishController::class, 'store'])->name('store');
            Route::get('/{dish}/edit', [CantineDishController::class, 'edit'])->name('edit');
            Route::put('/{dish}', [CantineDishController::class, 'update'])->name('update');
            Route::delete('/{dish}', [CantineDishController::class, 'destroy'])->name('destroy');
            Route::get('/search', [CantineDishController::class, 'search'])->name('search');
        });
    });

    // Module Stock
    Route::prefix('stock')->name('stock.')->middleware('can:view_stock')->group(function () {
        // Lieux de stockage
        Route::prefix('locations')->name('locations.')->middleware('can:manage_stock')->group(function () {
            Route::get('/', [StockLocationController::class, 'index'])->name('index');
            Route::get('/create', [StockLocationController::class, 'create'])->name('create');
            Route::post('/', [StockLocationController::class, 'store'])->name('store');
            Route::get('/{location}/edit', [StockLocationController::class, 'edit'])->name('edit');
            Route::put('/{location}', [StockLocationController::class, 'update'])->name('update');
            Route::delete('/{location}', [StockLocationController::class, 'destroy'])->name('destroy');
        });

        // Articles
        Route::prefix('items')->name('items.')->group(function () {
            Route::get('/', [StockItemController::class, 'index'])->name('index');
            Route::get('/create', [StockItemController::class, 'create'])->name('create')->middleware('can:manage_stock');
            Route::post('/', [StockItemController::class, 'store'])->name('store')->middleware('can:manage_stock');
            Route::get('/{item}', [StockItemController::class, 'show'])->name('show');
            Route::get('/{item}/edit', [StockItemController::class, 'edit'])->name('edit')->middleware('can:manage_stock');
            Route::put('/{item}', [StockItemController::class, 'update'])->name('update')->middleware('can:manage_stock');
            Route::delete('/{item}', [StockItemController::class, 'destroy'])->name('destroy')->middleware('can:manage_stock');
        });

        // Mouvements
        Route::prefix('movements')->name('movements.')->group(function () {
            Route::get('/', [StockMovementController::class, 'index'])->name('index');
            Route::get('/create', [StockMovementController::class, 'create'])->name('create')->middleware('can:record_stock_movement');
            Route::post('/', [StockMovementController::class, 'store'])->name('store')->middleware('can:record_stock_movement');
        });

        // Alertes
        Route::get('/alerts', [StockAlertController::class, 'index'])->name('alerts.index');
    });

    Route::resource('families', FamilyController::class)->middleware('can:manage_families');

    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::resource('users', UserController::class);

    Route::get('/families/{family}/children/create', [ChildController::class, 'create'])->name('families.children.create')->middleware('can:manage_families');
    Route::post('/families/{family}/children', [ChildController::class, 'store'])->name('families.children.store')->middleware('can:manage_families');
    Route::get('/children/{child}/edit', [ChildController::class, 'edit'])->name('children.edit')->middleware('can:manage_families');
    Route::put('/children/{child}', [ChildController::class, 'update'])->name('children.update')->middleware('can:manage_families');
    Route::delete('/children/{child}', [ChildController::class, 'destroy'])->name('children.destroy')->middleware('can:manage_families');

    Route::resource('classes', SchoolClassController::class);

    // Écoles (multi-écoles par tenant)
    Route::resource('schools', SchoolController::class);
    Route::post('/schools/select', [SchoolController::class, 'select'])->name('schools.select');

    Route::get('/invitations', [FamilyInvitationController::class, 'index'])->name('invitations.index');
    Route::post('/invitations/send', [FamilyInvitationController::class, 'send'])->name('invitations.send');
    Route::get('/invitations/qrcodes', [FamilyInvitationController::class, 'qrCodes'])->name('invitations.qrcodes');

    Route::prefix('parent')->name('parent.')->group(function () {
        Route::get('/dashboard', [ParentPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [ParentPortalController::class, 'editProfile'])->name('profile');
        Route::put('/profile', [ParentPortalController::class, 'updateProfile'])->name('profile.update');
        Route::get('/children/{child}/edit', [ParentPortalController::class, 'editChild'])->name('children.edit');
        Route::put('/children/{child}', [ParentPortalController::class, 'updateChild'])->name('children.update');
        Route::get('/events', [ParentPortalController::class, 'events'])->name('events');
        Route::post('/events/mark-viewed', [ParentPortalController::class, 'markEventsViewed'])->name('events.markViewed');
        Route::get('/menus', [ParentPortalController::class, 'menus'])->name('menus');
        Route::get('/notifications', [ParentPortalController::class, 'notifications'])->name('notifications');
        Route::put('/notifications', [ParentPortalController::class, 'updateNotifications'])->name('notifications.update');
    });
});
