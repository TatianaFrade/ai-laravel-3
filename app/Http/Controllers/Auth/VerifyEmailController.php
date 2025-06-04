<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Models\Card;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended('/dashboard');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            
            // Cria o cartão aqui, se não existir
            if (!$user->card) {
                Card::createForUser($user);
            }
        }

        return redirect()->intended('/dashboard')->with('verified', true);
    }
}
