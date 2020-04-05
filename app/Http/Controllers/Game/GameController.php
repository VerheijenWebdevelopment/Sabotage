<?php

namespace App\Http\Controllers\Game;

use Cards;
use Games;
use Roles;
use Players;

use App\Http\Controllers\Controller;

class GameController extends Controller
{
    public function getGame()
    {
        if (!Games::userHasActiveGame())
        {
            flash("You are currently not in an active game!")->success();
            return redirect()->route("lobby");
        }

        return view("pages.game.game", [
            "game" => Games::getActiveGame(),
            "player" => Players::getActivePlayer(),
            "playerRole" => Players::getActivePlayerRole(),
            "cards" => Cards::getAllPreloaded(),
            "roles" => Roles::getAll(),
        ]);
    }
}