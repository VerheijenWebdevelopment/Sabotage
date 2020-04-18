<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Player;
use App\Models\GameMessage;
use App\Traits\ModelServiceGetters;
use App\Events\Game\GameMessageSent;
use App\Contracts\ModelServiceContract;

class GameMessageService implements ModelServiceContract
{
    use ModelServiceGetters;

    private $model;
    private $records;
    private $preloadedRecords;

    public function __construct()
    {
        $this->model =  "App\Models\GameMessage";
    }

    public function preload($instance)
    {
        $instance->author = $this->loadAuthor($instance);

        return $instance;
    }

    private function loadAuthor(GameMessage $message)
    {
        if (!is_null($message->player))
        {
            if ($message->player->user_id == auth()->user()->id)
            {
                return "you";
            }
            else
            {
                return $message->player->user->username;
            }
        }
        else
        {
            return "system";
        }
    }
    
    public function getAllForGame(Game $game)
    {
        $out = [];

        // Grab  all of the game's messages
        foreach ($this->getAllPreloaded() as $message)
        {
            if ($message->game_id == $game->id)
            {
                $out[] = $message;
            }
        }

        // Convert array to a Collection
        $out = collect($out);

        // Return messages
        return $out;
    }

    public function sendMessage($message, Game $game, Player $player)
    {
        // Create and return a new GameMessage
        $gameMessage = GameMessage::create([
            "game_id" => $game->id,
            "player_id" => $player->id,
            "message" => $message,
        ]);

        // Broadcast event to all other players
        broadcast(new GameMessageSent($gameMessage))->toOthers();

        // Return the created message
        return $gameMessage;
    }

    public function sendSystemMessage($message, Game $game, $broadcast = true)
    {
        // Create the game message
        $gameMessage = GameMessage::create([
            "game_id" => $game->id,
            "message" => $message,
        ]);

        // Broadcast event to all players
        if ($broadcast) broadcast(new GameMessageSent($gameMessage));

        // Return the created system message
        return $gameMessage;
    }
}