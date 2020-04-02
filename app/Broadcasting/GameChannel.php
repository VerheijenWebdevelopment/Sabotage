<?php

namespace App\Broadcasting;

use Games;
use App\Models\User;
use App\Models\Game;

class GameChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\Models\User  $user
     * @return array|bool
     */
    public function join(User $user, Game $game)
    {
        
    }
}
