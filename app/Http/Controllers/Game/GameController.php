<?php

namespace App\Http\Controllers\Game;

use Cards;
use Games;
use Roles;
use Rounds;
use Players;

use App\Http\Controllers\Controller;

class GameController extends Controller
{
    public function getGame()
    {
        // If the user is not in an active game; redirect them back to the lobby
        if (!Games::userHasActiveGame())
        {
            flash("You are currently not in an active game!")->success();
            return redirect()->route("lobby");
        }

        // Render the game page
        return view("pages.game.game", [
            "game" => Games::getActiveGame(),
            "round" => Rounds::getCurrentRound(),
            "player" => Players::getActivePlayer(),
            "playerRole" => Players::getActivePlayerRole(),
            "cards" => Cards::getAllPreloaded(),
            "roles" => Roles::getAll(),
            "apiEndpoints" => collect([
                "send_message" => route("api.games.send-message.post"),
                "perform_action" => route("api.games.perform-action.post"),
            ]),
            "icons" => collect([
                "cart" => asset("storage/images/icons/minecart.svg"),
                "light" => asset("storage/images/icons/lantern.svg"),
                "pickaxe" => asset("storage/images/icons/pickaxe.svg"),
                "gold" => asset("storage/images/icons/gold.svg"),
                "gold_bars" => asset("storage/images/icons/gold_bars.svg"),
                "coal" => asset("storage/images/icons/coal.svg"),
                "prison" => asset("storage/images/icons/prison.svg"),
                "thief" => asset("storage/images/icons/thief.svg"),
            ]),
        ]);
    }
}