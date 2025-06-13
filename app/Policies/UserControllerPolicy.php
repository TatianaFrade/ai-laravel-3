<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserControllerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->type, ['employee', 'board']);
    }

    /**
     * Determine if the user can view a specific user.
     */
    public function view(User $user, User $model): bool
    {
        return in_array($user->type, ['employee', 'board']);
    }

    /**
     * Determine if the user can create users.
     */
    public function create(User $user): bool
    {
        return $user->type === 'board';
    }

    /**
     * Determine if the user can update a user.
     */
    public function update(User $user, User $userToUpdate): bool
    {
        if ($user->id === $userToUpdate->id) {
            return false;
        }
        return $user->type === 'board';
    }

    /**
     * Determine if the user can delete a user.
     */
    public function delete(User $authUser, User $userToDelete): bool
    {
        return $authUser->type === 'board' && $authUser->id !== $userToDelete->id;
    }

    /**
     * Determine if the user can view blocked status.
     */
    public function viewBlockedStatus(User $authUser): bool
    {
        return $authUser->type === 'board';
    }

    /**
     * Determine if the user can update specific fields of a user.
     */
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

    /**
     * Determine if the user can restore a deleted user.
     */
    public function restore(User $authUser, User $userToRestore): bool
    {
        return $authUser->type === 'board' && $authUser->id !== $userToRestore->id;
    }

    /**
     * Determine if the user can permanently delete a user.
     */
    public function forceDelete(User $user, User $userToDelete): bool
    {
        return $user->type === 'board' && $userToDelete->type === 'employee';
    }
}
