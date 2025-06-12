<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{

    public function viewAny(User $user): bool
    {
        return in_array($user->type, ['employee', 'board']);
    }


    public function view(User $user)
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





    public function updateField(User $authUser, User $userToEdit, string $field): bool
    {
        if ($authUser->type !== 'board') {
            return false;
        }

        // Se estiver a criar um novo user (sem ID), assume que é employee
        $isCreating = !$userToEdit->exists;

        // Se a criar ou editar um employee, só permite estes campos
        if ($isCreating || $userToEdit->type === 'employee') {
            return in_array($field, ['email', 'password', 'name', 'gender', 'photo']);
        }

        // Ao editar um board ou member, permite tudo
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
