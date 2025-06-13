<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StockAdjustment;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockAdjustmentControllerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any stock adjustments.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->type, ['employee', 'board']);
    }

    /**
     * Determine if the user can view a stock adjustment.
     */
    public function view(User $user, StockAdjustment $stockAdjustment): bool
    {
        return in_array($user->type, ['employee', 'board']);
    }

    /**
     * Determine if the user can create stock adjustments.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine if the user can update a stock adjustment.
     */
    public function update(User $user, StockAdjustment $stockadjustment): bool
    {
        return false;
    }

    /**
     * Determine if the user can delete a stock adjustment.
     */
    public function delete(User $user, StockAdjustment $stockadjustment): bool
    {
        return false;
    }

    /**
     * Determine if the user can restore a stock adjustment.
     */
    public function restore(User $user, StockAdjustment $stockadjustment): bool
    {
        return false;
    }

    /**
     * Determine if the user can permanently delete a stock adjustment.
     */
    public function forceDelete(User $user, StockAdjustment $stockadjustment): bool
    {
        return false;
    }
}
