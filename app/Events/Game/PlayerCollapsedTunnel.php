<?php

namespace App\Events\Game;

use App\Models\Game;
use App\Models\Player;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PlayerCollapsedTunnel implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $game;
    public $player;
    public $coordinates;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Game $game, Player $player, array $coordinates)
    {
        $this->game = $game;
        $this->player = $player;
        $this->coordinates = $coordinates;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('game.'.$this->game->id);
    }
}
