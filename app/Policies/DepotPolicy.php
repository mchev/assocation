<?php

namespace App\Policies;

use App\Models\Depot;
use App\Models\User;

class DepotPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Depot $depot): bool
    {
        return $user->currentOrganization->id === $depot->organization_id;
    }

    public function create(User $user, Organization $organization): bool
    {
        return $user->isAdminOf($organization);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Depot $depot): bool
    {
        return $user->currentOrganization->id === $depot->organization_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Depot $depot): bool
    {
        return $user->currentOrganization->id === $depot->organization_id;
    }
}
