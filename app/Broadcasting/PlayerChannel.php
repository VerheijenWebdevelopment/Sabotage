<?php

namespace App\Broadcasting;

use App\Models\User;
use App\Models\Player;

class PlayerChannel
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
    public function join(User $user, Player $player)
    {
        // Only allow the owner of the player to enter this channel
        return $player->user->id == $user->id;
    }
}
