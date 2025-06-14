<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserControllerPolicy
{
    use HandlesAuthorization;


    public function viewAny(User $user): bool
    {
        return in_array($user->type, ['employee', 'board']);
    }

 
    public function view(User $user, User $model): bool
    {
        return in_array($user->type, ['employee', 'board']);
    }

 
    public function create(User $user): bool
    {
        return $user->type === 'board';
    }

    public function update(User $user, User $userToUpdate): bool
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



    public function updateField(User $authUser, User $userToEdit, string $field): bool
    {
   
        if ($authUser->type !== 'board') {
            return false;
        }
        
        $isCreating = !$userToEdit->exists;
        
      
        if ($isCreating || $userToEdit->type === 'employee') {
            return in_array($field, [
                'name', 
                'email', 
                'password', 
                'gender', 
                'photo', 
                'type' 
            ]);
        }
        
   
        return true;
    }

   
    public function restore(User $authUser, User $userToRestore): bool
    {
        return $authUser->type === 'board' && $authUser->id !== $userToRestore->id;
    }

  
    public function forceDelete(User $user, User $userToDelete): bool
    {
        return $user->type === 'board' && $userToDelete->type === 'employee';
    }
}
