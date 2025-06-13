<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductControllerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any products.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view a product.
     */
    public function view(User $user, Product $product): bool
    {
        return true;
    }

    /**
     * Determine if the user can create products.
     */
    public function create(User $user): bool
    {
        return $user->type === 'board';
    }

    /**
     * Determine if the user can update a product.
     */
    public function update(User $user, Product $product): bool
    {
        return $user->type === 'board' || $user->type === 'employee';
    }

    /**
     * Custom policy for updating product stock.
     */
    public function updateStock(User $user, Product $product): bool
    {
        return in_array($user->type, ['employee', 'board']);
    }

    /**
     * Custom method found in ProductController
     */
    public function viewShowCase(User $user): bool
    {
        return true;
    }
    
    /**
     * Custom method for viewing product table
     */
    public function viewTable(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can delete a product.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->type === 'board';
    }

    /**
     * Determine if the user can restore a product.
     */
    public function restore(User $user, Product $product): bool
    {
        return false;
    }

    /**
     * Determine if the user can permanently delete a product.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return false;
    }
}
