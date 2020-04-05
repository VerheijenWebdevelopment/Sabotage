<?php

use App\Broadcasting\ChatChannel;
use App\Broadcasting\GameChannel;
use App\Broadcasting\GameChatChannel;
use App\Broadcasting\LobbyChannel;
use App\Broadcasting\OnlineChannel;
use App\Broadcasting\PlayerChannel;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Online users channel
Broadcast::channel("online", OnlineChannel::class);

// Global chat channel
Broadcast::channel("chat", ChatChannel::class);

// Lobby channel
Broadcast::channel("lobby", LobbyChannel::class);

// Game channel
Broadcast::channel("game.{game}", GameChannel::class);

// Game's chat channel
Broadcast::channel("game-chat.{game}", GameChatChannel::class);