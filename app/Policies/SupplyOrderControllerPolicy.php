<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SupplyOrder;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplyOrderControllerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any supply orders.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->type, ['employee', 'board']);
    }

    /**
     * Determine if the user can view a supply order.
     */
    public function view(User $user, SupplyOrder $supplyorder): bool
    {
       return in_array($user->type, ['employee', 'board']);
    }

    /**
     * Determine if the user can create supply orders.
     */
    public function create(User $user): bool
    {
        return in_array($user->type, ['employee', 'board']);
    }

    /**
     * Determine if the user can update a supply order.
     */
    public function update(User $user, SupplyOrder $supplyorder): bool
    {
        return in_array($user->type, ['employee', 'board']);
    }

    /**
     * Determine if the user can delete a supply order.
     */
    public function delete(User $user, SupplyOrder $supplyorder): bool
    {
        return in_array($user->type, ['employee', 'board']);
    }

    /**
     * Determine if the user can restore a supply order.
     */
    public function restore(User $user, SupplyOrder $supplyorder): bool
    {
        return false;
    }

    /**
     * Determine if the user can permanently delete a supply order.
     */
    public function forceDelete(User $user, SupplyOrder $supplyorder): bool
    {
        return false;
    }
}
