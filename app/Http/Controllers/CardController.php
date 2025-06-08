<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Card;

class CardController extends Controller
{
 
    public function showUserCard()
    {
 
        $user = Auth::user();
         $card = Card::find($user->id); // evita 404

 
        if ($card) {
            $this->authorize('view', $card);
        }


        return view('card.show', compact('card'));
    }

}
