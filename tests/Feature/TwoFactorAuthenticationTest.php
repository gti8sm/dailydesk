<?php

namespace Tests\Feature;

use App\Mail\TwoFactorCodeMail;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TwoFactorAuthenticationTest extends TestCase
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
            'two_factor_enabled' => true,
        ]);
        $this->admin->assignRole('admin');

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Mail::fake();
    }

    public function test_login_with_2fa_sends_code_and_does_not_authenticate(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@ville-test.fr',
            'credential' => 'Password-123!',
        ]);

        // Redirigé vers le formulaire de code, non authentifié
        $response->assertRedirect(route('login.twofactor'));
        $this->assertGuest();

        Mail::assertSent(TwoFactorCodeMail::class, 1);
    }

    public function test_user_with_2fa_can_login_with_valid_code(): void
    {
        $this->post('/login', [
            'email' => 'admin@ville-test.fr',
            'credential' => 'Password-123!',
        ]);

        $code = null;
        Mail::assertSent(function (TwoFactorCodeMail $mail) use (&$code) {
            $code = $mail->code;

            return true;
        });

        $this->assertMatchesRegularExpression('/^\d{6}$/', $code);

        $this->post('/login/2fa/verify', ['code' => $code])
            ->assertRedirect("/{$this->tenant->slug}/dashboard");

        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_wrong_code_is_rejected(): void
    {
        $this->post('/login', [
            'email' => 'admin@ville-test.fr',
            'credential' => 'Password-123!',
        ]);

        $this->post('/login/2fa/verify', ['code' => '000000'])
            ->assertSessionHasErrors('code');

        $this->assertGuest();
    }

    public function test_expired_code_redirects_to_login(): void
    {
        $this->post('/login', [
            'email' => 'admin@ville-test.fr',
            'credential' => 'Password-123!',
        ]);

        // Simule l'expiration du code
        \Cache::forget('2fa_code_' . $this->admin->id);

        $this->post('/login/2fa/verify', ['code' => '123456'])
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_user_without_2fa_logs_in_directly(): void
    {
        $this->admin->update(['two_factor_enabled' => false]);

        $this->post('/login', [
            'email' => 'admin@ville-test.fr',
            'credential' => 'Password-123!',
        ]);

        Mail::assertNothingSent();
        $this->assertAuthenticatedAs($this->admin);
    }
}
