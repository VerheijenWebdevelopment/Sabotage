<?php

namespace App\Events\Game;

use App\Models\Game;
use App\Models\Player;
use App\Models\Card;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PlayerPlacedTunnel implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $game;
    public $player;
    public $card;
    public $coordinates;
    public $inverted;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Game $game, Player $player, Card $card, array $coordinates, bool $inverted)
    {
        $this->game = $game;
        $this->player = $player;
        $this->card = $card;
        $this->coordinates = $coordinates;
        $this->inverted = $inverted;
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
