<?php

namespace App\Http\Controllers;

use App\Models\User;

class EmailVerificationController extends Controller
{
    public function verify($token)
    {
        $user = User::where('confirmation_token', $token)->firstOrFail();
        $user->email_confirmed = true;
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Email confirmado! Agora você pode pagar a associação.');
    }
}
