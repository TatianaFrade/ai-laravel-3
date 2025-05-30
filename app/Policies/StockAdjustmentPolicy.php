<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StockAdjustment;

class StockAdjustmentPolicy
{

    public function viewAny(User $user): bool
    {
        return in_array($user->type, ['employee', 'board']);
    }

    public function view(User $user, StockAdjustment $stockAdjustment): bool
    {
        return in_array($user->type, ['employee', 'board']);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user,StockAdjustment $stockadjustment): bool
    {
        return false;
    }

 
    public function delete(User $user,StockAdjustment $stockadjustment): bool
    {
        return false;
    }





    public function restore(StockAdjustment $stockadjustment): bool
    {
        return false;
    }
 
    public function forceDelete(StockAdjustment $stockadjustment): bool
    {
        return false;
    }
}
