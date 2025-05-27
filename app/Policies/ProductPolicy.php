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

    /**
     * Todos os utilizadores podem ver um produto.
     */
    public function view(User $user, Product $product): bool
    {
        return true;
    }

    /**
     * Só utilizadores 'Board' podem criar produtos.
     */
    public function create(User $user): bool
    {
        return $user->type === 'board';
    }

    /**
     * Só utilizadores 'Board' podem atualizar produtos.
     */
    public function update(User $user, Product $product): bool
    {
        return $user->type === 'board';
    }

    public function updateStock(User $user, Product $product): bool
    {
        return in_array($user->type, ['employee', 'board']);
    }


    /**
     * Só utilizadores 'Board' podem apagar produtos.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->type === 'board';
    }

    /**
     * Restauro não permitido.
     */
    public function restore(User $user, Product $product): bool
    {
        return false;
    }

    /**
     * Eliminação permanente não permitida.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return false;
    }
}
