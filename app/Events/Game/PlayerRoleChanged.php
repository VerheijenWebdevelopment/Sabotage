<?php

namespace App\Events\Game;

use App\Models\Game;
use App\Models\Player;
use App\Models\Role;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PlayerRoleChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $game;
    public $player;
    public $role;
    public $other_player;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Game $game, Player $player, Role $role, Player $otherPlayer)
    {
        $this->game = $game;
        $this->player = $player;
        $this->role = $role;
        $this->other_player = $otherPlayer;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('player.'.$this->player->id);
    }
}
