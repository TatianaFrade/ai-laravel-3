<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Operation;
use Illuminate\Auth\Access\HandlesAuthorization;

class OperationControllerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any operations.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view an operation.
     */
    public function view(User $user, Operation $operation): bool
    {
        return true;
    }

    /**
     * Determine if the user can create operations.
     */
    public function create(User $user): bool
    {
        return $user->type === 'board';
    }

    /**
     * Determine if the user can update an operation.
     */
    public function update(User $user, Operation $operation): bool
    {
        return $user->type === 'board';
    }

    /**
     * Determine if the user can delete an operation.
     */
    public function delete(User $user, Operation $operation): bool
    {
        return $user->type === 'board';
    }

    /**
     * Determine if the user can restore an operation.
     */
    public function restore(User $user, Operation $operation): bool
    {
        return false;
    }

    /**
     * Determine if the user can permanently delete an operation.
     */
    public function forceDelete(User $user, Operation $operation): bool
    {
        return false;
    }
}
