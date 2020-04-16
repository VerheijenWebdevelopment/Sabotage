<?php

namespace App\Events\Lobby;

use App\Models\User;
use App\Models\Game;
use App\Models\Player;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PlayerJoinedGame implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $game;
    public $player;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Player $player, Game $game)
    {
        $this->user = $user;
        $this->game = $game;
        $this->player = $player;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('lobby');
    }
}
