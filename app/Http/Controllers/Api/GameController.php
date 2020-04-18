<?php

namespace App\Http\Controllers\Api;

use Games;
use Players;
use Exception;
use GameMessages;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Game\PerformActionRequest;
use App\Http\Requests\Api\Game\SendGameMessageRequest;

class GameController extends Controller
{
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
        // Grab the active game for the logged in user making this request
        $game = Games::find($request->game_id);

        // Grab the player of the logged in user making this request
        $player = Players::getActivePlayer();

        // Send the message
        $message = GameMessages::sendMessage($request->message, $game, $player);

        // Return success + the created message
        return response()->json([
            "status" => "success",
            "message" => $message,
        ]);
    }
}