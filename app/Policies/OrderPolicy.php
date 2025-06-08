<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        return $user->type !== 'member' || $order->user_id === $user->id;
    }

    public function viewAny(User $user): bool
    {
        return in_array($user->type, ['board', 'employee', 'member']);
    }


    public function create(User $user): bool
    {
        return $user->type === 'board';
    }


    public function update(User $user): bool
    {
        return in_array($user->type, ['board', 'employee']);
    }


    public function delete(Order $order): bool
    {
        return true;
    }

    public function restore(Order $order): bool
    {
        return false;
    }

 
    public function forceDelete(Order $order): bool
    {
        return false;
    }
}
