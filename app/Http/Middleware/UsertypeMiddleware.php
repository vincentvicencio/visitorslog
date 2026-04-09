<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsertypeMiddleware
{
    public function handle(Request $request, Closure $next, ...$allowedTypes)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // Make sure user_type_id column exists in users table
        if (!in_array($user->user_type_id, $allowedTypes)) {
            return redirect()->route('visitorslog')
                ->with('error', 'You are not authorized to access that page.');
        }

        return $next($request);
    }
}