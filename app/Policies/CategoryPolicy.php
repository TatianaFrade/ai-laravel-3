<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;

class CategoryPolicy
{
    /**
     * Todos os utilizadores podem ver a lista de produtos.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Todos os utilizadores podem ver um produto.
     */
    public function view(User $user, Category $category): bool
    {
        return true;
    }

    /**
     * Só utilizadores 'Board' podem criar produtos.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Só utilizadores 'Board' podem atualizar produtos.
     */
    public function update(User $user, Category $category): bool
    {
        return true;
    }

    /**
     * Só utilizadores 'Board' podem apagar produtos.
     */
    public function delete(User $user, Category $category): bool
    {
        return true;
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
