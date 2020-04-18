<?php

namespace App\Http\Controllers\Api;

use Lobby;
use Games;
use Players;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Lobby\JoinGameRequest;
use App\Http\Requests\Api\Lobby\StartGameRequest;
use App\Http\Requests\Api\Lobby\LeaveGameRequest;
use App\Http\Requests\Api\Lobby\CreateGameRequest;
use App\Http\Requests\Api\Lobby\DeleteGameRequest;
use App\Http\Requests\Api\Lobby\UpdateSettingsRequest;

class LobbyController extends Controller
{
    public function postCreateGame(CreateGameRequest $request)
    {
        // Create the game
        $game = Lobby::createFromRequest($request);

        // Return success + the created game
        return response()->json([
            "status" => "success",
            "game" => Games::preload($game)
        ]);
    }

    public function postDeleteGame(DeleteGameRequest $request)
    {
        // Delete the game
        Lobby::deleteFromRequest($request);

        // Return success
        return response()->json(["status" => "success"]);
    }

    public function postJoinGame(JoinGameRequest $request)
    {
        // Join the game
        $player = Lobby::joinFromRequest($request);

        // Return success
        return response()->json([
            "status" => "success",
            "player" => Players::preload($player),
        ]);
    }

    public function postLeaveGame(LeaveGameRequest $request)
    {
        // Leave the game
        Lobby::leaveFromRequest($request);

        // Return success
        return response()->json(["status" => "success"]);
    }

    public function postStartGame(StartGameRequest $request)
    {
        // Start the game
        $game = Lobby::startFromRequest($request);

        // Return success + the created game
        return response()->json([
            "status" => "success",
            "game_id" => $game->id,
        ]);
    }

    public function postUpdateSettings(UpdateSettingsRequest $request)
    {
        // Update the game's settings
        $game = Lobby::updateSettingsFromRequest($request);

        // Return success
        return response()->json(["status" => "success"]);
    }
}