<?php

namespace Tests\Feature;

use App\Models\Child;
use App\Models\Family;
use App\Models\ParentModel;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FamilyManagementTest extends TestCase
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

    private function actingAsAdmin(): self
    {
        return $this->actingAs($this->admin);
    }

    /**
     * Régression : une checkbox cochée sans attribut value envoie "on",
     * que la règle de validation boolean doit accepter (normalisation).
     */
    public function test_family_can_be_created_with_checked_checkboxes_sending_on(): void
    {
        $response = $this->actingAsAdmin()->post("/{$this->tenant->slug}/families", [
            'family_name' => 'Famille Dupont',
            'address' => '1 rue de la Paix',
            'postal_code' => '31000',
            'city' => 'Toulouse',
            'phone' => '0561234567',
            'parents' => [
                [
                    'first_name' => 'Jean',
                    'last_name' => 'Dupont',
                    'relationship' => 'father',
                    'is_primary_contact' => 'on',
                    'can_pickup' => 'on',
                    'is_legal_guardian' => 'on',
                ],
            ],
        ]);

        $family = Family::where('family_name', 'Famille Dupont')->first();

        $this->assertNotNull($family, 'La famille doit être créée');
        $response->assertRedirect("/{$this->tenant->slug}/families/{$family->id}");

        $parent = $family->parents()->first();
        $this->assertTrue((bool) $parent->is_primary_contact);
        $this->assertTrue((bool) $parent->can_pickup);
        $this->assertTrue((bool) $parent->is_legal_guardian);
    }

    /**
     * Régression : le formulaire envoie une ligne parent vide par défaut,
     * elle doit être ignorée sans faire échouer la validation.
     */
    public function test_family_can_be_created_without_parent(): void
    {
        $response = $this->actingAsAdmin()->post("/{$this->tenant->slug}/families", [
            'family_name' => 'Famille Sans Parent',
            'address' => '1 rue de la Paix',
            'postal_code' => '31000',
            'city' => 'Toulouse',
            'phone' => '0561234567',
            'parents' => [
                [
                    'first_name' => '',
                    'last_name' => '',
                    'relationship' => 'mother',
                ],
            ],
        ]);

        $family = Family::where('family_name', 'Famille Sans Parent')->first();

        $this->assertNotNull($family, 'La famille doit être créée malgré la ligne parent vide');
        $response->assertRedirect();
        $this->assertSame(0, $family->parents()->count());
    }

    public function test_family_update_keeps_existing_parent_when_empty_row_added(): void
    {
        $family = Family::create([
            'family_name' => 'Famille Martin',
            'address' => '2 avenue Test',
            'postal_code' => '31000',
            'city' => 'Toulouse',
            'phone' => '0561234567',
            'is_active' => true,
            'tenant_id' => $this->tenant->id,
        ]);
        $parent = ParentModel::create([
            'family_id' => $family->id,
            'tenant_id' => $this->tenant->id,
            'first_name' => 'Marie',
            'last_name' => 'Martin',
            'relationship' => 'mother',
            'is_primary_contact' => true,
        ]);

        $response = $this->actingAsAdmin()->put("/{$this->tenant->slug}/families/{$family->id}", [
            'family_name' => 'Famille Martin',
            'address' => '3 avenue Modifiée',
            'postal_code' => '31000',
            'city' => 'Toulouse',
            'phone' => '0561234567',
            'parents' => [
                [
                    'id' => $parent->id,
                    'first_name' => 'Marie',
                    'last_name' => 'Martin',
                    'relationship' => 'mother',
                    'is_primary_contact' => 'on',
                ],
                [
                    'first_name' => '',
                    'last_name' => '',
                    'relationship' => 'father',
                ],
            ],
        ]);

        $response->assertRedirect();
        $family->refresh();
        $this->assertSame('3 avenue Modifiée', $family->address);
        $this->assertSame(1, $family->parents()->count(), 'Le parent existant doit être conservé, la ligne vide ignorée');
        $this->assertTrue((bool) $family->parents()->first()->is_primary_contact);
    }

    public function test_rgpd_export_returns_complete_json(): void
    {
        $family = Family::create([
            'family_name' => 'Famille Export',
            'address' => '1 rue Test',
            'postal_code' => '31000',
            'city' => 'Toulouse',
            'phone' => '0561234567',
            'is_active' => true,
            'tenant_id' => $this->tenant->id,
        ]);
        $child = Child::create([
            'family_id' => $family->id,
            'first_name' => 'Léo',
            'last_name' => 'Export',
            'birth_date' => '2019-05-10',
            'gender' => 'M',
            'tenant_id' => $this->tenant->id,
        ]);
        ParentModel::create([
            'family_id' => $family->id,
            'tenant_id' => $this->tenant->id,
            'first_name' => 'Paul',
            'last_name' => 'Export',
            'relationship' => 'father',
            'is_primary_contact' => true,
        ]);

        $response = $this->actingAsAdmin()->get("/{$this->tenant->slug}/families/{$family->id}/export-rgpd");

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/json');
        $json = $response->json();
        $this->assertSame('Famille Export', $json['famille']['family_name']);
        $this->assertCount(1, $json['parents']);
        $this->assertCount(1, $json['enfants']);
        $this->assertSame('Léo', $json['enfants'][0]['enfant']['first_name']);
    }

    public function test_rgpd_purge_requires_exact_family_name_and_deletes_everything(): void
    {
        $family = Family::create([
            'family_name' => 'Famille Purge',
            'address' => '1 rue Test',
            'postal_code' => '31000',
            'city' => 'Toulouse',
            'phone' => '0561234567',
            'is_active' => true,
            'tenant_id' => $this->tenant->id,
        ]);
        $child = Child::create([
            'family_id' => $family->id,
            'first_name' => 'Léo',
            'last_name' => 'Purge',
            'birth_date' => '2019-05-10',
            'gender' => 'M',
            'tenant_id' => $this->tenant->id,
        ]);
        ParentModel::create([
            'family_id' => $family->id,
            'first_name' => 'Paul',
            'last_name' => 'Purge',
            'relationship' => 'father',
            'is_primary_contact' => true,
        ]);

        // Mauvaise confirmation : refus, rien n'est supprimé
        $this->actingAsAdmin()
            ->post("/{$this->tenant->slug}/families/{$family->id}/purge-rgpd", ['confirm' => 'mauvais nom'])
            ->assertRedirect("/{$this->tenant->slug}/families/{$family->id}");
        $this->assertNotNull(Family::find($family->id));

        // Bonne confirmation : purge définitive complète
        $this->actingAsAdmin()
            ->post("/{$this->tenant->slug}/families/{$family->id}/purge-rgpd", ['confirm' => 'Famille Purge'])
            ->assertRedirect("/{$this->tenant->slug}/families");

        $this->assertNull(Family::withTrashed()->find($family->id), 'La famille doit être effacée même de la corbeille');
        $this->assertSame(0, ParentModel::withTrashed()->where('family_id', $family->id)->count());
        $this->assertSame(0, Child::withTrashed()->where('family_id', $family->id)->count());
    }
}
