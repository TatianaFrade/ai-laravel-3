<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ShippingCost;

class ShippingCostPolicy
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
    public function view(User $user, ShippingCost $cost): bool
    {
        return true;
    }

    /**
     * Só utilizadores 'Board' podem criar produtos.
     */
    public function create(ShippingCost $cost): bool
    {
        return true;
    }

    /**
     * Só utilizadores 'Board' podem atualizar produtos.
     */
    public function update(ShippingCost $cost): bool
    {
        return true;
    }

    /**
     * Só utilizadores 'Board' podem apagar produtos.
     */
    public function delete(ShippingCost $cost): bool
    {
        return true;
    }

    /**
     * Restauro não permitido.
     */
    public function restore(ShippingCost $cost): bool
    {
        return false;
    }

    /**
     * Eliminação permanente não permitida.
     */
    public function forceDelete(ShippingCost $cost): bool
    {
        return false;
    }
}
