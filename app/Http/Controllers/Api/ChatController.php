<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Events\Chat\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Chat\SendMessageRequest;

class ChatController extends Controller
{
    public function postSendMessage(SendMessageRequest $request)
    {
        // Broadcast the MessageSent event to all other users
        broadcast(new MessageSent(auth()->user(), $request->message))->toOthers();

        // Return a JSON response
        return response()->json(["status" => "success"]);
    }
}