<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }


    public function view(User $user, Order $order): bool
    {
        return true;
    }


    public function create(Order $order): bool
    {
        return true;
    }

 
    public function update(Order $order): bool
    {
        return true;
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
