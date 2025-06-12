<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckIfUserBlocked
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->blocked) {
            Auth::logout();
            
            return redirect()->route('login')
                ->with('status', 'Your account has been blocked. Please contact support.');
        }

        return $next($request);
    }
}
