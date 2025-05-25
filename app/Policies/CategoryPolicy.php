<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Category;

class CategoryPolicy
{
    /**
     * Apenas utilizadores 'board' podem ver a lista de categorias.
     */
    public function viewAny(User $user): bool
    {
        return $user->type === 'board';
    }

    /**
     * Apenas utilizadores 'board' podem ver uma categoria.
     */
    public function view(User $user, Category $category): bool
    {
        return $user->type === 'board';
    }

    /**
     * Apenas utilizadores 'board' podem criar categorias.
     */
    public function create(User $user): bool
    {
        return $user->type === 'board';
    }

    /**
     * Apenas utilizadores 'board' podem atualizar categorias.
     */
    public function update(User $user, Category $category): bool
    {
        return $user->type === 'board';
    }

    /**
     * Apenas utilizadores 'board' podem eliminar categorias.
     */
    public function delete(User $user, Category $category): bool
    {
        return $user->type === 'board';
    }

    /**
     * Restauro não permitido.
     */
    public function restore(User $user, Category $category): bool
    {
        return false;
    }

    /**
     * Eliminação permanente não permitida.
     */
    public function forceDelete(User $user, Category $category): bool
    {
        return false;
    }
}


