<?php

namespace App\Http\Controllers\Api;

use Games;
use Players;
use Exception;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Game\JoinGameRequest;
use App\Http\Requests\Api\Game\StartGameRequest;
use App\Http\Requests\Api\Game\LeaveGameRequest;
use App\Http\Requests\Api\Game\CreateGameRequest;
use App\Http\Requests\Api\Game\DeleteGameRequest;
use App\Http\Requests\Api\Game\PerformActionRequest;
use App\Http\Requests\Api\Game\SendGameMessageRequest;

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
        $game = Games::startFromRequest($request);

        return response()->json([
            "status" => "success",
            "game_id" => $game->id,
        ]);
    }

    public function postPerformAction(PerformActionRequest $request)
    {
        // Grab the game we're supposed to perform an action on
        $game = Games::find($request->game_id);

        // Grab the currently logged in user's active player
        $player = Players::getActivePlayer();

        // try
        // {
            // Ensure the player is a player in the active game
            if (!Games::playerIsInGame($player, $game)) throw new Exception("Can't perform an action in a game you are not a part of.");

            // Perform the action through the Games service
            $data = Games::performAction($game, $player, $request->action, $request->data);

            // Return success response
            return response()->json([
                "status" => "success",
                "data" => $data,
            ]);
        // }
        // catch (Exception $e)
        // {
        //     return response()->json([
        //         "status" => "error",
        //         "error" => $e->getMessage(),
        //     ]);
        // }
    }

    public function postSendMessage(SendGameMessageRequest $request)
    {
        Games::sendMessageFromRequest($request);
        
        return response()->json(["status" => "success"]);
    }
}