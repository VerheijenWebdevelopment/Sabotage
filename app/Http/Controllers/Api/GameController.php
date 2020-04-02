<?php

namespace App\Http\Controllers\Api;

use Games;
use Players;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Game\JoinGameRequest;
use App\Http\Requests\Api\Game\LeaveGameRequest;
use App\Http\Requests\Api\Game\CreateGameRequest;
use App\Http\Requests\Api\Game\DeleteGameRequest;

class GameController extends Controller
{
    public function postCreateGame(CreateGameRequest $request)
    {
        $game = Games::createFromRequest($request);

        return response()->json([
            "status" => "success",
            "game" => Games::preload($game)
        ]);
    }

    public function postDeleteGame(DeleteGameRequest $request)
    {
        Games::deleteFromRequest($request);

        return response()->json(["status" => "success"]);
    }

    public function postJoinGame(JoinGameRequest $request)
    {
        $player = Games::joinFromRequest($request);

        return response()->json([
            "status" => "success",
            "player" => Players::preload($player),
        ]);
    }

    public function postLeaveGame(LeaveGameRequest $request)
    {
        Games::leaveFromRequest($request);

        return response()->json(["status" => "success"]);
    }

    public function postStartGame(StartGameRequest $request)
    {
        Games::startFromRequest($request);
        
        return response()->json(["status" => "success"]);
    }
}