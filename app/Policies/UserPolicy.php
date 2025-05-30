<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{

    public function viewAny(User $user): bool
    {
        return in_array($user->type, ['employee', 'board']);
    }


    public function view( User $user)
    {
        return in_array($user->type, ['employee', 'board']);
    }


    public function create(User $user): bool
    {
        return $user->type === 'board';
    }


    public function update(User $user, User $userToUpdate)
    {
        if ($user->id === $userToUpdate->id) {
        return false;  
        }
        return $user->type === 'board';
    }


    public function delete(User $authUser, User $userToDelete): bool
    {
        return $authUser->type === 'board' && $authUser->id !== $userToDelete->id;
    }

    public function viewBlockedStatus(User $authUser): bool
    {
        return $authUser->type === 'board';
    }





    
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, User $userToDelete): bool
    {
        return $user->type === 'board' && $userToDelete->type === 'employee';
    }
}
