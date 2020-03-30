<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class IsGuest
{
    public function handle($request, Closure $next)
    {
        if (Auth::check())
        {
            flash(__('auth.middleware_guest_required'))->error();
            return redirect()->route("lobby");
        }
    
        return $next($request);
    }
}