<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $admin;

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

        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@ville-test.fr',
            'login' => 'admin.test',
            'password' => bcrypt('Password-123!'),
            'is_active' => true,
            'tenant_id' => $this->tenant->id,
        ]);
        $this->admin->assignRole('admin');

        // Spatie met les permissions en cache — purge après seed (requis en tests)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@ville-test.fr',
            'credential' => 'mauvais-mot-de-passe',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_admin_can_login_and_is_redirected_to_tenant_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@ville-test.fr',
            'credential' => 'Password-123!',
        ]);

        $response->assertRedirect("/{$this->tenant->slug}/dashboard");
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $this->admin->update(['is_active' => false]);

        $response = $this->post('/login', [
            'email' => 'admin@ville-test.fr',
            'credential' => 'Password-123!',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }
}
