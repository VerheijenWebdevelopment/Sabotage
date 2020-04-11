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
        $users = Users::getAllPreloaded();
        $users = $users->sortByDesc(function($user) {
            return $user->highscore;
        })->values();

        return view("pages.lobby.leaderboards", [
            "users" => $users,
        ]);
    }
}