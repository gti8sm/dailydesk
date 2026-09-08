<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\FamilyInvitationController;
use App\Http\Controllers\ParentPortalController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Central\TenantController;
use App\Http\Controllers\Central\SubscriptionPlanController;
use App\Http\Controllers\Central\ImpersonationController;
use App\Http\Controllers\Central\ModuleController;
use App\Http\Controllers\Central\ActivityLogController;
use App\Http\Controllers\Central\StatisticsController;
use App\Http\Controllers\Central\ExportController as CentralExportController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
use App\Modules\Garderie\Controllers\GarderiePresenceController;
use App\Modules\Garderie\Controllers\GarderieEventController;
use App\Modules\Cantine\Controllers\CantinePresenceController;
use App\Modules\Cantine\Controllers\CantineEventController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('web');
Route::post('/login', [LoginController::class, 'login'])->middleware('web');
Route::get('/login/pin', function () {
    return redirect()->route('login');
});

// Mot de passe oublié
Route::get('/password/request', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/password/email', [LoginController::class, 'sendResetLink'])->name('password.email');
Route::get('/password/reset/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [LoginController::class, 'resetPassword'])->name('password.update');

Route::middleware('auth')->group(function () {
    Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Routes centrales (super admin uniquement)
    Route::prefix('central')->name('central.')->group(function () {
        Route::get('statistics', [StatisticsController::class, 'index'])->name('statistics');
        
        Route::get('exports', [CentralExportController::class, 'index'])->name('exports');
        Route::post('exports/garderie', [CentralExportController::class, 'exportGarderie'])->name('exports.garderie');
        Route::post('exports/cantine', [CentralExportController::class, 'exportCantine'])->name('exports.cantine');
        
        Route::post('tenants/{tenant}/toggle-status', [TenantController::class, 'toggleStatus'])->name('tenants.toggle-status');
        Route::resource('tenants', TenantController::class);
        
        Route::post('plans/{plan}/toggle-status', [SubscriptionPlanController::class, 'toggleStatus'])->name('plans.toggle-status');
        Route::resource('plans', SubscriptionPlanController::class);
        
        Route::post('impersonate/{tenant}', [ImpersonationController::class, 'impersonate'])->name('impersonate');
        Route::get('do-impersonate', [ImpersonationController::class, 'doImpersonate'])->name('do-impersonate')->withoutMiddleware('auth');
        Route::get('restore-super-admin', [ImpersonationController::class, 'restoreSuperAdmin'])->name('restore-super-admin')->withoutMiddleware('auth');

        Route::get('modules', [ModuleController::class, 'overview'])->name('modules.overview');
        Route::get('modules/settings', [ModuleController::class, 'globalSettings'])->name('modules.settings');
        Route::post('modules/settings', [ModuleController::class, 'updateGlobalSettings'])->name('modules.settings.update');
        Route::post('modules/{tenant}/{module}/toggle', [ModuleController::class, 'toggleModule'])->name('modules.toggle');

        Route::get('logs', [ActivityLogController::class, 'index'])->name('logs.index');
        Route::get('logs/{log}', [ActivityLogController::class, 'show'])->name('logs.show');
    });
    
    // Stop impersonation - accessible from both central and tenant domains
    Route::post('stop-impersonating', [ImpersonationController::class, 'stopImpersonating'])->name('central.stop-impersonating')->withoutMiddleware('auth');
    
    Route::get('/profile', function () {
        return view('profile.index');
    })->name('profile');

    Route::get('/help', function () {
        return view('help.index');
    })->name('help');
    
    Route::get('/settings', [SettingsController::class, 'index'])
        ->name('settings.index')
        ->middleware('can:manage_settings');
    Route::put('/settings', [SettingsController::class, 'update'])
        ->name('settings.update')
        ->middleware('can:manage_settings');
    
    // Routes d'export (admin uniquement - vérification dans le contrôleur)
    Route::prefix('exports')->name('exports.')->group(function () {
        Route::get('/', [ExportController::class, 'index'])->name('index');
        Route::post('/garderie', [ExportController::class, 'exportGarderie'])->name('garderie');
        Route::post('/cantine', [ExportController::class, 'exportCantine'])->name('cantine');
    });
    
    // Routes d'import (admin uniquement - vérification dans le contrôleur)
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
        Route::post('/children/{child}/arrival', [GarderiePresenceController::class, 'recordArrival'])
            ->name('record.arrival')
            ->middleware('can:record_garderie_presence');
        Route::post('/children/{child}/departure', [GarderiePresenceController::class, 'recordDeparture'])
            ->name('record.departure')
            ->middleware('can:record_garderie_presence');
        Route::delete('/presences/{presence}', [GarderiePresenceController::class, 'destroy'])
            ->name('presences.destroy')
            ->middleware('can:record_garderie_presence');
        Route::patch('/presences/{presence}', [GarderiePresenceController::class, 'update'])
            ->name('presences.update')
            ->middleware('can:record_garderie_presence');
        Route::get('/reports/monthly', [GarderiePresenceController::class, 'monthlyReport'])
            ->name('reports.monthly');
        Route::get('/export/monthly', [GarderiePresenceController::class, 'exportMonthly'])
            ->name('export.monthly')
            ->middleware('can:export_data');

        // Événements / Incidents
        Route::get('/events', [GarderieEventController::class, 'index'])->name('events.index');
        Route::get('/events/create', [GarderieEventController::class, 'create'])->name('events.create');
        Route::post('/events', [GarderieEventController::class, 'store'])->name('events.store');
        Route::get('/events/{event}', [GarderieEventController::class, 'show'])->name('events.show');
        Route::post('/events/{event}/notify', [GarderieEventController::class, 'markNotified'])->name('events.notify');
    });
    
    Route::prefix('cantine')->name('cantine.')->middleware('can:view_cantine')->group(function () {
        Route::get('/', [CantinePresenceController::class, 'index'])->name('index');
        Route::get('/search', [CantinePresenceController::class, 'search'])->name('search');
        Route::post('/children/{child}/record', [CantinePresenceController::class, 'record'])
            ->name('record')
            ->middleware('can:record_cantine_presence');
        Route::delete('/presences/{presence}', [CantinePresenceController::class, 'destroy'])
            ->name('presences.destroy')
            ->middleware('can:record_cantine_presence');
        Route::patch('/presences/{presence}', [CantinePresenceController::class, 'update'])
            ->name('presences.update')
            ->middleware('can:record_cantine_presence');
        Route::get('/reports/monthly', [CantinePresenceController::class, 'monthlyReport'])
            ->name('reports.monthly');
        Route::get('/export/monthly', [CantinePresenceController::class, 'exportMonthly'])
            ->name('export.monthly')
            ->middleware('can:export_data');

        // Événements / Incidents
        Route::get('/events', [CantineEventController::class, 'index'])->name('events.index');
        Route::get('/events/create', [CantineEventController::class, 'create'])->name('events.create');
        Route::post('/events', [CantineEventController::class, 'store'])->name('events.store');
        Route::get('/events/{event}', [CantineEventController::class, 'show'])->name('events.show');
        Route::post('/events/{event}/notify', [CantineEventController::class, 'markNotified'])->name('events.notify');
    });
    
    Route::resource('families', FamilyController::class)
        ->middleware('can:manage_families');
    
    // Routes pour la gestion des utilisateurs (admin uniquement - vérification dans le contrôleur)
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
        ->name('users.toggle-status');
    Route::resource('users', UserController::class);
    
    // Routes pour les enfants (imbriquées dans les familles)
    Route::get('/families/{family}/children/create', [ChildController::class, 'create'])
        ->name('families.children.create')
        ->middleware('can:manage_families');
    Route::post('/families/{family}/children', [ChildController::class, 'store'])
        ->name('families.children.store')
        ->middleware('can:manage_families');
    Route::get('/children/{child}/edit', [ChildController::class, 'edit'])
        ->name('children.edit')
        ->middleware('can:manage_families');
    Route::put('/children/{child}', [ChildController::class, 'update'])
        ->name('children.update')
        ->middleware('can:manage_families');
    Route::delete('/children/{child}', [ChildController::class, 'destroy'])
        ->name('children.destroy')
        ->middleware('can:manage_families');
    
    // Gestion des classes
    Route::resource('classes', SchoolClassController::class);

    // Invitations familles (admin)
    Route::get('/invitations', [FamilyInvitationController::class, 'index'])->name('invitations.index');
    Route::post('/invitations/send', [FamilyInvitationController::class, 'send'])->name('invitations.send');
    Route::get('/invitations/qrcodes', [FamilyInvitationController::class, 'qrCodes'])->name('invitations.qrcodes');

    // Portail parent
    Route::prefix('parent')->name('parent.')->group(function () {
        Route::get('/dashboard', [ParentPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [ParentPortalController::class, 'editProfile'])->name('profile');
        Route::put('/profile', [ParentPortalController::class, 'updateProfile'])->name('profile.update');
        Route::get('/children/{child}/edit', [ParentPortalController::class, 'editChild'])->name('children.edit');
        Route::put('/children/{child}', [ParentPortalController::class, 'updateChild'])->name('children.update');
        Route::get('/events', [ParentPortalController::class, 'events'])->name('events');
        Route::get('/notifications', [ParentPortalController::class, 'notifications'])->name('notifications');
        Route::put('/notifications', [ParentPortalController::class, 'updateNotifications'])->name('notifications.update');
    });

});

// Invitation accept (guest route)
Route::get('/invitation/accept/{token}', [FamilyInvitationController::class, 'accept'])->name('invitation.accept');
Route::post('/invitation/register/{token}', [FamilyInvitationController::class, 'register'])->name('invitation.register');
