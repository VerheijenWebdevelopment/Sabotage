<?php

namespace App\Http\Controllers\Game;

use Games;
use Players;
use App\Http\Controllers\Controller;

class LobbyController extends Controller
{
    public function getLobby()
    {
        return view("pages.game.lobby", [
            "user" => auth()->user(),
            "games" => Games::getOpenAndOutstandingGames(),
        ]);
    }

    public function getLeaderboards()
    {
        return view("pages.game.leaderboards", [

        ]);
    }
}