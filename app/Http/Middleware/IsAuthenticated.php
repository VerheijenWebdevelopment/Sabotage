<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class IsAuthenticated
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check())
        {
            flash(__('auth.middleware_login_required'))->error();
            return redirect()->route("login");
        }
    
        return $next($request);
    }
}