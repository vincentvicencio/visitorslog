<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SingleSessionMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if ($user) {
            $currentSessionId = session()->getId();
            // Check if this user has other sessions
            $otherSessions = DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('id', '!=', $currentSessionId)
                ->delete(); // Log out other sessions
            // Optionally, update the session record with user_id if not set
            DB::table('sessions')
                ->where('id', $currentSessionId)
                ->update(['user_id' => $user->id]);
        }
        return $next($request);
    }
}
