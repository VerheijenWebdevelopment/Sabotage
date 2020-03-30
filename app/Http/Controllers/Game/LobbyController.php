<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;

class LobbyController extends Controller
{
    public function getLobby()
    {
        return view("pages.game.lobby", []);
    }
}