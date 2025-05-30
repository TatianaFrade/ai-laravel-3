<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MembershipFee;

class MembershipFeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->type === 'board';
    }

    public function update(User $user, MembershipFee $fee): bool
    {
        return $user->type === 'board';
    }




    
    public function create(User $user): bool
    {
        return false;   
    }

    public function delete(User $user, MembershipFee $fee): bool
    {
        return false; 
    }

    public function restore(User $user, MembershipFee $fee): bool
    {
        return false;
    }

    public function forceDelete(User $user, MembershipFee $fee): bool
    {
        return false;
    }
}
