<?php

namespace App\Traits;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasOrganizations
{
    /**
     * Get all organizations that the user belongs to.
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get the user's current organization.
     */
    public function primaryOrganization(): ?Organization
    {
        return $this->currentOrganization;
    }

    /**
     * Check if the user is an admin of the given organization.
     */
    public function isAdminOf(Organization $organization): bool
    {
        return $this->organizations()
            ->where('organization_id', $organization->id)
            ->wherePivot('role', 'admin')
            ->exists();
    }

    /**
     * Check if the user is a member of the given organization.
     */
    public function isMemberOf(Organization $organization): bool
    {
        return $this->organizations()
            ->where('organization_id', $organization->id)
            ->exists();
    }

    /**
     * Set the given organization as the user's current organization.
     */
    public function setPrimaryOrganization(Organization $organization): bool
    {
        if (! $this->isMemberOf($organization)) {
            return false;
        }

        $this->update(['current_organization_id' => $organization->id]);

        return true;
    }

    /**
     * Ensure the user has a current organization.
     */
    public function ensurePrimaryOrganization(): void
    {
        // If user has no current organization but has existing organizations,
        // set the first one as current
        if (! $this->current_organization_id && $this->organizations()->exists()) {
            $this->update(['current_organization_id' => $this->organizations()->first()->id]);

            return;
        }

        // If user has no organizations at all, create a new one
        if (! $this->organizations()->exists()) {
            $organization = Organization::create([
                'name' => "Organisation de {$this->name}",
                'email' => $this->email,
                'owner_id' => $this->id,
            ]);

            $this->organizations()->attach($organization->id, ['role' => 'admin']);
            $this->update(['current_organization_id' => $organization->id]);
        }
    }
}
