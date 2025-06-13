<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ShippingCost;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShippingCostControllerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any shipping costs.
     */
    public function viewAny(User $user): bool
    {
        return $user->type === 'board';
    }

    /**
     * Determine if the user can view a shipping cost.
     */
    public function view(User $user, ShippingCost $cost): bool
    {
        return $user->type === 'board';
    }

    /**
     * Determine if the user can create shipping costs.
     */
    public function create(User $user): bool
    {
        return $user->type === 'board';
    }

    /**
     * Determine if the user can update a shipping cost.
     */
    public function update(User $user, ShippingCost $cost): bool
    {
        return $user->type === 'board';
    }

    /**
     * Determine if the user can delete a shipping cost.
     */
    public function delete(User $user, ShippingCost $cost): bool
    {
        return $user->type === 'board';
    }

    /**
     * Determine if the user can restore a shipping cost.
     */
    public function restore(User $user, ShippingCost $cost): bool
    {
        return false;
    }

    /**
     * Determine if the user can permanently delete a shipping cost.
     */
    public function forceDelete(User $user, ShippingCost $cost): bool
    {
        return false;
    }
}
