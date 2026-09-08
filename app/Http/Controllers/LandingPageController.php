<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class LandingPageController extends Controller
{
    public function index()
    {
        return view('landing.index');
    }

    public function registerProspect(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'organization_name' => 'nullable|string|max:255',
        ]);

        $existingTenant = Tenant::where('email', $validated['email'])->first();
        if ($existingTenant) {
            return back()
                ->withInput()
                ->with('error', 'Un compte existe déjà avec cet email. Contactez-nous si besoin.');
        }

        try {
            DB::beginTransaction();

            $slug = $this->generateUniqueSlug($validated['name'], $validated['organization_name'] ?? null);

            $tenant = Tenant::create([
                'id' => Str::uuid()->toString(),
                'name' => $validated['organization_name'] ?? $validated['name'],
                'slug' => $slug,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'city' => $validated['city'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'status' => 'prospect',
                'subscription_plan' => 'starter',
                'subscription_starts_at' => null,
                'subscription_expires_at' => null,
                'trial_ends_at' => now()->addDays(30),
                'primary_color' => '#3B82F6',
                'secondary_color' => '#6366F1',
                'modules_enabled' => ['garderie'],
                'max_children' => 50,
                'settings' => [
                    'contact_name' => $validated['name'],
                    'prospect_created_at' => now()->toIso8601String(),
                    'source' => 'landing_page',
                ],
            ]);

            $domain = $slug . '.dailydesk.fr';
            $tenant->domains()->create(['domain' => $domain]);

            $this->createTenantAdmin($tenant, $validated);

            DB::commit();

            try {
                Mail::raw(
                    "Bonjour,\n\nUne nouvelle demande de démo a été enregistrée :\n\n" .
                    "Contact : {$validated['name']}\n" .
                    "Email : {$validated['email']}\n" .
                    "Téléphone : {$validated['phone']}\n" .
                    "Adresse : {$validated['address']}\n" .
                    "Organisation : " . ($validated['organization_name'] ?? 'N/A') . "\n\n" .
                    "Le tenant a été créé avec le statut 'prospect'.\n" .
                    "Connectez-vous au super admin pour l'activer.",
                    function ($message) use ($validated) {
                        $message->to('simonmaraval@smallwebconcept.fr')
                            ->subject('Nouvelle demande de démo - ' . ($validated['organization_name'] ?? $validated['name']));
                    }
                );
            } catch (\Exception $e) {
                // Email failure should not block the process
            }

            return redirect()->route('landing.thank-you');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.');
        }
    }

    public function thankYou()
    {
        return view('landing.thank-you');
    }

    private function generateUniqueSlug(string $name, ?string $orgName): string
    {
        $base = Str::slug($orgName ?? $name, '');
        $base = preg_replace('/[^a-zA-Z0-9]/', '', $base);
        $base = strtolower(substr($base, 0, 20));

        if (empty($base)) {
            $base = 'tenant';
        }

        $slug = $base;
        $counter = 1;
        while (Tenant::where('slug', $slug)->exists()) {
            $slug = $base . $counter;
            $counter++;
        }

        return $slug;
    }

    private function createTenantAdmin(Tenant $tenant, array $data): void
    {
        $tempPassword = Str::random(12);

        $admin = \App\Models\User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($tempPassword),
            'is_active' => false,
            'tenant_id' => $tenant->id,
        ]);

        tenancy()->initialize($tenant);
        $this->seedRolesAndPermissions();
        $admin->assignRole('admin');
        tenancy()->end();

        $tenant->settings = array_merge($tenant->settings ?? [], [
            'temp_admin_password' => $tempPassword,
        ]);
        $tenant->save();
    }

    private function seedRolesAndPermissions(): void
    {
        $permissions = [
            'view_dashboard', 'manage_users', 'manage_families', 'manage_children',
            'import_families', 'export_data', 'manage_licenses', 'manage_settings',
            'view_garderie', 'record_garderie_presence', 'create_garderie_event', 'view_garderie_events',
            'view_cantine', 'record_cantine_presence', 'create_cantine_event', 'view_cantine_events',
            'view_own_children', 'view_own_events', 'manage_notifications',
        ];

        foreach ($permissions as $perm) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($permissions);

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'personnel_mairie', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'enseignant', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'alsh', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'cantine', 'guard_name' => 'web']);
    }
}
