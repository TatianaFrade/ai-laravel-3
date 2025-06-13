<?php

namespace App\Policies;

use App\Models\Card;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CardControllerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any cards (e.g., listing).
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->type, ['board', 'employee']);
    }

    /**
     * Determine if the user can view a specific card.
     */
    public function view(User $user, Card $card): bool
    {
        return $user->id === $card->id;
    }

    /**
     * Determine if the user can create cards.
     */
    public function create(User $user): bool
    {
        return in_array($user->type, ['board', 'member']);
    }

    /**
     * Determine if the user can update a card.
     */
    public function update(User $user, Card $card): bool
    {
        return $user->type === 'board' || ($user->type === 'member' && $user->id === $card->id);
    }

    /**
     * Determine if the user can delete a card.
     */
    public function delete(User $user, Card $card): bool
    {
        return false;
    }

    /**
     * Determine if the user can restore a card.
     */
    public function restore(User $user, Card $card): bool
    {
        return false;
    }

    /**
     * Determine if the user can force delete a card.
     */
    public function forceDelete(User $user, Card $card): bool
    {
        return false;
    }
}
