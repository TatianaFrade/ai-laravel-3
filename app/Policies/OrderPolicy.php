<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;

class OrderPolicy
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
    public function view(User $user, Order $order): bool
    {
        return true;
    }

    /**
     * Só utilizadores 'Board' podem criar produtos.
     */
    public function create(Order $order): bool
    {
        return true;
    }

    /**
     * Só utilizadores 'Board' podem atualizar produtos.
     */
    public function update(Order $order): bool
    {
        return true;
    }

    /**
     * Só utilizadores 'Board' podem apagar produtos.
     */
    public function delete(Order $order): bool
    {
        return true;
    }

    /**
     * Restauro não permitido.
     */
    public function restore(Order $order): bool
    {
        return false;
    }

    /**
     * Eliminação permanente não permitida.
     */
    public function forceDelete(Order $order): bool
    {
        return false;
    }
}
