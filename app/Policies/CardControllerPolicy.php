<?php

namespace App\Policies;

use App\Models\Card;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CardControllerPolicy
{
    use HandlesAuthorization;

  
    public function viewAny(User $user): bool
    {
        return in_array($user->type, ['board', 'employee']);
    }

    
    public function view(User $user, Card $card): bool
    {
        return $user->id === $card->id;
    }

   
    public function create(User $user): bool
    {
        return in_array($user->type, ['board', 'member', 'employee']);
    }

   
    public function update(User $user, Card $card): bool
    {
        return $user->type === 'board' || ($user->type === 'member' && $user->id === $card->id);
    }

   
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
