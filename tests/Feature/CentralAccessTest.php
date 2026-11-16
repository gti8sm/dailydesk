<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CentralAccessTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $tenantAdmin;
    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->tenant = Tenant::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Ville Test',
            'slug' => 'ville-test',
            'status' => 'active',
        ]);

        $this->tenantAdmin = User::create([
            'name' => 'Admin Mairie',
            'email' => 'admin@ville-test.fr',
            'password' => bcrypt('Password-123!'),
            'is_active' => true,
            'tenant_id' => $this->tenant->id,
        ]);
        $this->tenantAdmin->assignRole('admin');

        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@dailydesk.fr',
            'password' => bcrypt('Password-123!'),
            'is_active' => true,
            'tenant_id' => null,
        ]);
        $this->superAdmin->assignRole('super_admin');

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Régression : un admin mairie ne doit plus pouvoir atteindre le
     * dashboard central (vue tenant sans contexte tenancy = erreur 500).
     */
    public function test_tenant_admin_cannot_access_central_dashboard(): void
    {
        $response = $this->actingAs($this->tenantAdmin)->get('/central/dashboard');

        // Redirigé vers son propre dashboard, pas une erreur
        $response->assertRedirect("/{$this->tenant->slug}/dashboard")
            ->assertSessionHas('error');
    }

    public function test_tenant_admin_cannot_access_central_system(): void
    {
        $this->actingAs($this->tenantAdmin)
            ->get('/central/system')
            ->assertRedirect("/{$this->tenant->slug}/dashboard");
    }

    public function test_super_admin_can_access_central_dashboard(): void
    {
        $this->actingAs($this->superAdmin)
            ->get('/central/dashboard')
            ->assertOk();
    }

    public function test_impersonation_restore_route_still_reachable_for_guests(): void
    {
        // Token invalide → redirection login (et non 403 du verrou central)
        $this->get('/central/restore-super-admin?token=invalide')
            ->assertRedirect(route('login'));
    }
}
