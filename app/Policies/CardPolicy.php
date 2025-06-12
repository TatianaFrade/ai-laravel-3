<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Card;

class CardPolicy
{
    /**
     * Determina se o utilizador pode ver qualquer cartão (ex: lista).
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->type, ['board', 'employee']);
    }

    /**
     * Determina se o utilizador pode ver um cartão específico.
     */
    public function view(User $user, Card $card): bool
    {
        return $user->id === $card->id;
    }

    /**
     * Determina se o utilizador pode criar cartões.
     */    public function create(User $user): bool
    {
        return in_array($user->type, ['board', 'member']);
    }

    /**
     * Determina se o utilizador pode atualizar um cartão.
     */    public function update(User $user, Card $card): bool
    {
        return $user->type === 'board' || ($user->type === 'member' && $user->id === $card->id);
    }

    /**
     * Determina se o utilizador pode apagar um cartão.
     */
    public function delete(User $user, Card $card): bool
    {
        return false;
    }

    public function restore(User $user, Card $card): bool
    {
        return false;
    }

    public function forceDelete(User $user, Card $card): bool
    {
        return false;
    }
}
