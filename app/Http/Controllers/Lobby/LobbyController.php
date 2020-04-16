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
        // Render the lobby page
        return view("pages.lobby.lobby", [
            "user" => auth()->user(),
            "games" => Games::getOpenAndOutstandingGames(),
            "strings" => collect([
                "game" => __("lobby.game"),
                "leave_game" => __("lobby.leave_game"),
                "delete_game" => __("lobby.delete_game"),
                "start_game" => __("lobby.start_game"),
                "join_game" => __("lobby.join_game"),
                "create_game" => __("lobby.create_game"),
                "open_outstanding_games" => __("lobby.open_outstanding_games"),
                "no_open_outstanding_games" => __("lobby.no_open_outstanding_games"),
                "list_id" => __("lobby.list_id"),
                "list_players" => __("lobby.list_players"),
                "list_status" => __("lobby.list_status"),
                "list_actions" => __("lobby.list_actions"),
                "waiting_for_players" => __("lobby.waiting_for_players"),
                "waiting_for_gm" => __("lobby.waiting_for_gm"),
                "game_started" => __("lobby.game_started"),
                "rounds" => __("lobby.rounds"),
                "configure_dialog_title" => __("lobby.configure_dialog_title"),
                "configure_dialog_num_rounds" => __("lobby.configure_dialog_num_rounds"),
                "configure_dialog_cancel" => __("lobby.configure_dialog_cancel"),
                "configure_dialog_submit" => __("lobby.configure_dialog_submit"),
            ])
        ]);
    }

    public function getLeaderboards()
    {
        // Grab all users and sort them by their highscore
        $users = Users::getAllPreloaded();
        $users = $users->sortByDesc(function($user) {
            return $user->highscore;
        })->values();

        // Render the leaderboards page
        return view("pages.lobby.leaderboards", [
            "users" => $users,
        ]);
    }
}