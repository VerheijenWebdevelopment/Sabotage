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
        $game = Lobby::createFromRequest($request);

        return response()->json([
            "status" => "success",
            "game" => Games::preload($game)
        ]);
    }

    public function postDeleteGame(DeleteGameRequest $request)
    {
        Lobby::deleteFromRequest($request);

        return response()->json(["status" => "success"]);
    }

    public function postJoinGame(JoinGameRequest $request)
    {
        $player = Lobby::joinFromRequest($request);

        return response()->json([
            "status" => "success",
            "player" => Players::preload($player),
        ]);
    }

    public function postLeaveGame(LeaveGameRequest $request)
    {
        Lobby::leaveFromRequest($request);

        return response()->json(["status" => "success"]);
    }

    public function postStartGame(StartGameRequest $request)
    {
        $game = Lobby::startFromRequest($request);

        return response()->json([
            "status" => "success",
            "game_id" => $game->id,
        ]);
    }

    public function postUpdateSettings(UpdateSettingsRequest $request)
    {
        $game = Lobby::updateSettingsFromRequest($request);

        return response()->json(["status" => "success"]);
    }
}