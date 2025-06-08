<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Product $product): bool
    {
        return true;
    }


    public function create(User $user): bool
    {
        return $user->type === 'board';
    }


    public function update(User $user, Product $product): bool
    {
        return $user->type === 'board' || $user->type === 'employee';
    }


    public function updateStock(User $user, Product $product): bool
    {
        return in_array($user->type, ['employee', 'board']);
    }




    public function delete(User $user, Product $product): bool
    {
        return $user->type === 'board';
    }



    public function restore(User $user, Product $product): bool
    {
        return false;
    }

    public function forceDelete(User $user, Product $product): bool
    {
        return false;
    }

  
    public function viewTable(User $user): bool
    {
        return in_array($user->type, ['board', 'employee']) && request('view') !== 'public';
    }


    public function viewFilter(User $user): bool
    {
         return in_array($user->type, ['board', 'employee']) && request('view') !== 'public';
    }


}
