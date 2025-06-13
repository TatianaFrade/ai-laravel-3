<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Category;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryControllerPolicy
{
    use HandlesAuthorization;

    public function viewAny(?User $user): bool
    {
        return $user->type === 'board';
    }

    public function view(?User $user, Category $category): bool
    {
        return $user->type === 'board';
    }

    public function create(User $user): bool
    {
        return $user->type === 'board';
    }

    public function update(User $user, Category $category): bool
    {
        return $user->type === 'board';
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->type === 'board';
    }

    public function restore(User $user, Category $category): bool
    {
        return $user->type === 'board';
    }

    public function forceDelete(User $user, Category $category): bool
    {
        return $user->type === 'board';
    }
}
