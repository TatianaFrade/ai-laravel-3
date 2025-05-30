<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ShippingCost;

class ShippingCostPolicy
{

    public function viewAny(User $user): bool
    {
        return $user->type === 'board';
    }


    public function view(User $user, ShippingCost $cost): bool
    {
        return $user->type === 'board';
    }

    public function create(User $user): bool
    {
        return $user->type === 'board';
    }

    public function update(User $user,ShippingCost $cost): bool
    {
        return $user->type === 'board';
    }

 
    public function delete(User $user,ShippingCost $cost): bool
    {
        return $user->type === 'board';
    }





    public function restore(ShippingCost $cost): bool
    {
        return false;
    }
 
    public function forceDelete(ShippingCost $cost): bool
    {
        return false;
    }
}
