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

class PlayerHandChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $game;
    public $player;
    public $hand;
    public $other_player;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Game $game, Player $player, array $hand, Player $otherPlayer)
    {
        $this->game = $game;
        $this->player = $player;
        $this->hand = $hand;
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
