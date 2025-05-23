<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $authUser): bool
    {
        // Apenas utilizadores do tipo 'board' podem ver a lista de utilizadores
        return $authUser->type === 'board';
    }


    public function view(User $authUser, User $user)
    {
        // Admin (board) pode ver qualquer utilizador
        if ($authUser->type === 'board') {
            return true;
        }

        // O utilizador pode ver a si mesmo
        return $authUser->id === $user->id;
    }



    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->type === 'board'; // ou outro tipo que possa criar
    }


    /**
     * Determine whether the user can update the model.
     */
    public function update(User $authUser, User $userToUpdate)
    {

        if ($authUser->type === 'board') {
            // Não pode editar a si mesmo
            if ($authUser->id === $userToUpdate->id) return false;

            // Pode editar outros membros conforme regras definidas no controller
            return true;
        }

        return false;
    }


    public function delete(User $authUser, User $userToDelete): bool
    {
        // Apenas admin pode eliminar e apenas se o utilizador for Employee
        return $authUser->type === 'board' && $authUser->id !== $userToDelete->id;
    }


    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
