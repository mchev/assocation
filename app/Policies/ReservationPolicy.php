<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReservationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->user_id ||
               ($reservation->lenderOrganization && ($user->isAdminOf($reservation->lenderOrganization) || $user->isMemberOf($reservation->lenderOrganization))) ||
               ($reservation->borrowerOrganization && ($user->isAdminOf($reservation->borrowerOrganization) || $user->isMemberOf($reservation->borrowerOrganization)));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Organization $organization): bool
    {
        return $user->isMemberOf($organization);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->user_id ||
               ($reservation->lenderOrganization && $user->isAdminOf($reservation->lenderOrganization)) ||
               ($reservation->borrowerOrganization && $user->isAdminOf($reservation->borrowerOrganization));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->user_id ||
               ($reservation->lenderOrganization && $user->isAdminOf($reservation->lenderOrganization)) ||
               ($reservation->borrowerOrganization && $user->isAdminOf($reservation->borrowerOrganization));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Reservation $reservation): bool
    {
        return ($reservation->lenderOrganization && $user->isAdminOf($reservation->lenderOrganization)) ||
               ($reservation->borrowerOrganization && $user->isAdminOf($reservation->borrowerOrganization));
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Reservation $reservation): bool
    {
        return ($reservation->lenderOrganization && $user->isAdminOf($reservation->lenderOrganization)) ||
               ($reservation->borrowerOrganization && $user->isAdminOf($reservation->borrowerOrganization));
    }
}
