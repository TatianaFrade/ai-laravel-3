<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SupplyOrder;

class SupplyOrderPolicy
{

    public function viewAny(User $user): bool
    {
        return in_array($user->type, ['employee', 'board']);
    }


    public function view(User $user, SupplyOrder $supplyorder): bool
    {
       return in_array($user->type, ['employee', 'board']);
    }

    public function create(User $user): bool
    {
        return in_array($user->type, ['employee', 'board']);
    }

    public function update(User $user,SupplyOrder $supplyorder): bool
    {
        return in_array($user->type, ['employee', 'board']);
    }

 
    public function delete(User $user,SupplyOrder $supplyorder): bool
    {
        return in_array($user->type, ['employee', 'board']);
    }





    public function restore(SupplyOrder $supplyorder): bool
    {
        return false;
    }
 
    public function forceDelete(SupplyOrder $supplyorder): bool
    {
        return false;
    }
}
