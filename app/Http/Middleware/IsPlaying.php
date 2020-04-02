<?php

namespace App\Http\Middleware;

use Games;
use Closure;

class IsPlaying
{
    public function handle($request, Closure $next)
    {
        // If the user is not in an active game
        if (!Games::userHasActiveGame())
        {
            // Tell them they should be
            flash(__("game.middleware_is_playing"))->error();

            // Redirect back to the lobby
            return redirect()->route("lobby");
        }
    
        return $next($request);
    }
}