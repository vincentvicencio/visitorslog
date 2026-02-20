<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\RegisteredUser;
use Illuminate\Support\Facades\Auth;

class UsertypeMiddleware
{
    public function handle(Request $request, Closure $next, ...$allowedTypes)
    {
        // If user is not logged in
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Get the user type from the RegisteredUser table
        $userType = RegisteredUser::where('user_name', Auth::user()->emp_code)
            ->value('user_type');

        // If the user record no longer exists (deleted)
        if ($userType === null) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->with('error', 'Your account no longer exists.');
        }


        if (!in_array((int)$userType, array_map('intval', $allowedTypes))) {
            abort(404);
        }


        return $next($request);
    }
}