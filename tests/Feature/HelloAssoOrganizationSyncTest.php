<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\HelloAssoOrganizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HelloAssoOrganizationSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_sync_helloasso_organizations(): void
    {
        $user = User::factory()->create([
            'helloasso_id' => 'test-helloasso-id',
            'helloasso_token' => 'test-token',
        ]);

        $helloassoOrganizations = [
            [
                'id' => 'org-1',
                'name' => 'Association Test 1',
                'description' => 'Description de l\'association 1',
                'email' => 'contact@assoc1.fr',
                'phone' => '0123456789',
                'address' => '123 Rue Test',
                'city' => 'Paris',
                'postal_code' => '75001',
                'website' => 'https://assoc1.fr',
                'slug' => 'association-test-1',
            ],
            [
                'id' => 'org-2',
                'name' => 'Association Test 2',
                'description' => 'Description de l\'association 2',
                'email' => 'contact@assoc2.fr',
                'city' => 'Lyon',
                'slug' => 'association-test-2',
            ],
        ];

        $service = app(HelloAssoOrganizationService::class);
        $service->syncOrganizations($user, $helloassoOrganizations);

        // Vérifier que les organisations ont été créées
        $this->assertDatabaseHas('organizations', [
            'helloasso_id' => 'org-1',
            'name' => 'Association Test 1',
            'email' => 'contact@assoc1.fr',
            'city' => 'Paris',
        ]);

        $this->assertDatabaseHas('organizations', [
            'helloasso_id' => 'org-2',
            'name' => 'Association Test 2',
            'email' => 'contact@assoc2.fr',
            'city' => 'Lyon',
        ]);

        // Vérifier que l'utilisateur est attaché aux organisations
        $this->assertTrue($user->organizations()->where('helloasso_id', 'org-1')->exists());
        $this->assertTrue($user->organizations()->where('helloasso_id', 'org-2')->exists());

        // Vérifier que l'utilisateur a une organisation principale
        $this->assertNotNull($user->fresh()->current_organization_id);
    }

    public function test_can_update_existing_organization(): void
    {
        $user = User::factory()->create([
            'helloasso_id' => 'test-helloasso-id',
            'helloasso_token' => 'test-token',
        ]);

        // Créer une organisation existante
        $organization = \App\Models\Organization::factory()->create([
            'helloasso_id' => 'org-1',
            'name' => 'Ancien nom',
            'email' => 'ancien@email.fr',
        ]);

        $user->organizations()->attach($organization->id, ['role' => 'admin']);

        $helloassoOrganizations = [
            [
                'id' => 'org-1',
                'name' => 'Nouveau nom',
                'email' => 'nouveau@email.fr',
                'city' => 'Nouvelle ville',
                'slug' => 'nouveau-slug',
            ],
        ];

        $service = app(HelloAssoOrganizationService::class);
        $service->syncOrganizations($user, $helloassoOrganizations);

        // Vérifier que l'organisation a été mise à jour
        $this->assertDatabaseHas('organizations', [
            'helloasso_id' => 'org-1',
            'name' => 'Nouveau nom',
            'email' => 'nouveau@email.fr',
            'city' => 'Nouvelle ville',
        ]);

        // Vérifier que l'utilisateur est toujours attaché
        $this->assertTrue($user->organizations()->where('helloasso_id', 'org-1')->exists());
    }

    public function test_handles_empty_organizations_array(): void
    {
        $user = User::factory()->create([
            'helloasso_id' => 'test-helloasso-id',
            'helloasso_token' => 'test-token',
        ]);

        $service = app(HelloAssoOrganizationService::class);
        $service->syncOrganizations($user, []);

        // Vérifier que l'utilisateur a toujours une organisation (créée par ensurePrimaryOrganization)
        $this->assertNotNull($user->fresh()->current_organization_id);
        $this->assertTrue($user->organizations()->exists());
    }
}
