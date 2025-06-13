<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderControllerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any orders.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->type, ['board', 'employee', 'member']);
    }

    /**
     * Determine if the user can view an order.
     */
    public function view(User $user, Order $order): bool
    {
        return $user->type !== 'member' || $order->user_id === $user->id;
    }

    /**
     * Determine if the user can create orders.
     */
    public function create(User $user): bool
    {
        return $user->type === 'board';
    }

    /**
     * Determine if the user can update an order.
     */
    public function update(User $user, Order $order): bool
    {
        return in_array($user->type, ['board', 'employee']);
    }

    /**
     * Determine if the user can delete an order.
     */
    public function delete(User $user, Order $order): bool
    {
        return true;
    }

    /**
     * Determine if the user can restore an order.
     */
    public function restore(User $user, Order $order): bool
    {
        return false;
    }

    /**
     * Determine if the user can permanently delete an order.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        return false;
    }
}
