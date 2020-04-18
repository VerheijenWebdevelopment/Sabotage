<?php

namespace App\Services;

use Games;
use GameMessages;
use App\Models\Game;
use App\Events\Lobby\GameCreated;
use App\Events\Lobby\GameDeleted;
use App\Events\Lobby\GameStarted;
use App\Events\Lobby\PlayerLeftGame;
use App\Events\Lobby\PlayerJoinedGame;
use App\Events\Lobby\GameSettingsUpdated;
use App\Http\Requests\Api\Lobby\JoinGameRequest;
use App\Http\Requests\Api\Lobby\StartGameRequest;
use App\Http\Requests\Api\Lobby\LeaveGameRequest;
use App\Http\Requests\Api\Lobby\CreateGameRequest;
use App\Http\Requests\Api\Lobby\DeleteGameRequest;
use App\Http\Requests\Api\Lobby\UpdateSettingsRequest;

class LobbyService
{
    public function createFromRequest(CreateGameRequest $request)
    {
        // Create the game
        $game = Game::create([
            "game_master_id" => auth()->user()->id,
            "settings" => [
                "num_rounds" => 3,
            ]
        ]);
        
        // Make the current player (who created it) join the game
        $player = Games::join($game);

        // Inform everyone in the lobby a game was created by the current player
        broadcast(new GameCreated($player->user, $player, $game))->toOthers();
        
        // Return a preloaded version of the game using the findPreloaded method this 
        // way a fresh instance of the game is returned with all relationships in place
        return Games::findPreloaded($game->id);
    }

    public function deleteFromRequest(DeleteGameRequest $request)
    {
        // Grab the game we want to delete
        $game = Games::find($request->game_id);

        // Delete the fucker
        $game->delete();
        
        // Inform all players in the lobby the game was deleted
        broadcast(new GameDeleted(auth()->user(), $request->game_id))->toOthers();
    }

    public function joinFromRequest(JoinGameRequest $request)
    {
        // Grab the game we want to join
        $game = Games::find($request->game_id);

        // Make the current player join the game
        $player = Games::join($game);

        // Inform all other players that the current player has joined the game
        broadcast(new PlayerJoinedGame($player->user, $player, $game))->toOthers();

        // Return the current player
        return $player;
    }

    public function updateSettingsFromRequest(UpdateSettingsRequest $request)
    {
        // Grab the game we want to update the settings of
        $game = Games::find($request->game_id);

        // Grab it's settings so we can mutate it
        $settings = (array) $game->settings;

        // Update the settings
        $settings["num_rounds"] = $request->num_rounds;

        // Save the new settings on the game
        $game->settings = $settings;
        $game->save();

        // Inform all other users the game's settings were updated
        broadcast(new GameSettingsUpdated($game, $settings))->toOthers();

        // Return the updated game
        return $game;
    }
    
    public function startFromRequest(StartGameRequest $request)
    {
        // Grab the game we want to start
        $game = Games::find($request->game_id);

        // Perform preperations
        $game = Games::prepareGame($game);

        // Update game's status
        $game = Games::setStatus($game, "ongoing");

        // Send a welcome (system) message to all players
        GameMessages::sendSystemMessage("Het spel is begonnen! Dat is niet mijn tunnel vriend.", $game, false);

        // Inform all other users the game has started
        broadcast(new GameStarted(auth()->user(), $game))->toOthers();

        // Return the updated game
        return $game;
    }

    public function leaveFromRequest(LeaveGameRequest $request)
    {
        // Grab the game we want to leave
        $game = Games::find($request->game_id);

        // Loop through all of the game's players
        foreach ($game->players as $player)
        {   
            // If we find a user belonging to the currently logged in user
            if ($player->user_id == auth()->user()->id)
            {
                // Tell everyone the player left the game before deleting the fucker (and
                // thereby making the data we need for this event unavailable.)
                broadcast(new PlayerLeftGame($game, $player->id))->toOthers();
                
                // Delete the player from existence
                $player->delete();

                // Stop looping since we found our target
                break;
            }
        }
    }
}