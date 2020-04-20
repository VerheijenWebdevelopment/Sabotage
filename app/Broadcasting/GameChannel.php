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
        // Only allow logged in users that own a player in the given game to join this channel
        if (auth()->check())
        {
            foreach ($game->players as $player)
            {
                if ($player->user_id == $user->id)
                {
                    return true;
                }
            }
        }

        return false;
    }
}
