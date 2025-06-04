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

     
        $card = $user->card;

 
        if (!$card) {
            return redirect()->back()->with('error', 'Nenhum cartão encontrado para este utilizador.');
        }


        return view('card.show', compact('card'));
    }
}
