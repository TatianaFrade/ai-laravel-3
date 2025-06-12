<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MembershipFee;

class MembershipFeePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MembershipFee $membershipFee): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->type === 'board';
    }

    public function update(User $user, MembershipFee $membershipFee): bool
    {
        return $user->type === 'board' && request('view') !== 'public';
    }

    public function pay(User $user, MembershipFee $membershipFee): bool
    {
        return $user->type === 'member';
    }

    public function delete(User $user, MembershipFee $membershipFee): bool
    {
        return false;
    }

    public function restore(User $user, MembershipFee $membershipFee): bool
    {
        return false;
    }

    public function forceDelete(User $user, MembershipFee $membershipFee): bool
    {
        return false;
    }
}
