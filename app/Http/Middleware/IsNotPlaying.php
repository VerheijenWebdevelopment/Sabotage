<?php

namespace App\Http\Middleware;

use Games;
use Closure;

class IsNotPlaying
{
    public function handle($request, Closure $next)
    {
        // If the user is in an active game
        if (Games::userHasActiveGame())
        {
            // Tell them they can't be
            flash(__("game.middleware_is_not_playing"))->error();

            // Redirect to the game view
            return redirect()->route("game");
        }
        
        return $next($request);
    }
}