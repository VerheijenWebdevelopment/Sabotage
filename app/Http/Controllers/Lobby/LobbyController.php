<?php

namespace App\Http\Controllers\Lobby;

use Users;
use Games;
use Players;

use App\Http\Controllers\Controller;

class LobbyController extends Controller
{
    public function getLobby()
    {
        return view("pages.lobby.lobby", [
            "user" => auth()->user(),
            "games" => Games::getOpenAndOutstandingGames(),
        ]);
    }

    public function getLeaderboards()
    {
        return view("pages.lobby.leaderboards", [
            "users" => Users::getAllPreloaded(),
        ]);
    }
}