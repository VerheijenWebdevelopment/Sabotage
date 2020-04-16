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

class GameSettingsUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $game;
    public $settings;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Game $game, array $settings)
    {
        $this->game = $game;
        $this->settings = $settings;
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
