<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class HelloAssoOrganizationService
{
    /**
     * Synchronize HelloAsso organizations with local organizations.
     */
    public function syncOrganizations(User $user, array $helloassoOrganizations): void
    {
        foreach ($helloassoOrganizations as $helloassoOrg) {
            $this->syncOrganization($user, $helloassoOrg);
        }

        // S'assurer que l'utilisateur a au moins une organisation
        $user->ensurePrimaryOrganization();
    }

    /**
     * Sync a single HelloAsso organization.
     */
    protected function syncOrganization(User $user, array $helloassoOrg): void
    {
        try {
            // Créer ou mettre à jour l'organisation locale
            $organization = Organization::updateOrCreate([
                'helloasso_id' => $helloassoOrg['id'] ?? null,
            ], [
                'name' => $helloassoOrg['name'] ?? 'Organisation HelloAsso',
                'description' => $helloassoOrg['description'] ?? null,
                'email' => $helloassoOrg['email'] ?? null,
                'phone' => $helloassoOrg['phone'] ?? null,
                'address' => $helloassoOrg['address'] ?? null,
                'city' => $helloassoOrg['city'] ?? null,
                'postal_code' => $helloassoOrg['postal_code'] ?? null,
                'website' => $helloassoOrg['website'] ?? null,
                'helloasso_id' => $helloassoOrg['id'] ?? null,
                'helloasso_name' => $helloassoOrg['name'] ?? null,
                'helloasso_slug' => $helloassoOrg['slug'] ?? null,
                'is_active' => true,
                'owner_id' => $user->id,
            ]);

            // Attacher l'utilisateur à l'organisation s'il n'y est pas déjà
            if (! $user->organizations()->where('organization_id', $organization->id)->exists()) {
                $user->organizations()->attach($organization->id, [
                    'role' => $this->determineUserRole($helloassoOrg),
                ]);
            }

            // Si c'est la première organisation, la définir comme organisation principale
            if (! $user->current_organization_id) {
                $user->update(['current_organization_id' => $organization->id]);
            }

            Log::info('HelloAsso organization synced', [
                'user_id' => $user->id,
                'organization_id' => $organization->id,
                'helloasso_id' => $helloassoOrg['id'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to sync HelloAsso organization', [
                'user_id' => $user->id,
                'helloasso_org' => $helloassoOrg,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Determine the user role based on HelloAsso organization data.
     */
    protected function determineUserRole(array $helloassoOrg): string
    {
        // Par défaut, l'utilisateur est admin de ses propres organisations
        // Vous pouvez ajuster cette logique selon les données HelloAsso
        // Par exemple, vérifier les permissions ou rôles retournés par l'API
        return 'admin';
    }

    /**
     * Get organization by HelloAsso ID.
     */
    public function findByHelloAssoId(string $helloassoId): ?Organization
    {
        return Organization::where('helloasso_id', $helloassoId)->first();
    }

    /**
     * Check if an organization exists by HelloAsso ID.
     */
    public function existsByHelloAssoId(string $helloassoId): bool
    {
        return Organization::where('helloasso_id', $helloassoId)->exists();
    }
}
