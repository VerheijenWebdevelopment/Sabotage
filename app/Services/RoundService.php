<?php

namespace App\Services;

use Games;
use Board;
use Cards;
use Roles;

use App\Models\Game;
use App\Models\Round;
use App\Traits\ModelServiceGetters;
use App\Contracts\ModelServiceContract;

class RoundService implements ModelServiceContract
{
    use ModelServiceGetters;

    private $model;
    private $records;
    private $preloadedRecords;

    public function __construct()
    {
        $this->model =  "App\Models\Round";
    }

    public function preload($instance)
    {
        return $instance;
    }
    
    public function getCurrentRound(Game $game = null)
    {
        if (is_null($game)) $game = Games::getActiveGame();

        return $game->currentRound ? $this->preload($game->currentRound) : false;
    }

    public function createNewRound(Game $game)
    {
        // Grab the current round; if there is one available
        $currentRound = $game->currentRound;

        // Generate a deck of cards
        $deck = Cards::generateDeck($game);

        // Generate a deck of role cards
        $roleDeck = Roles::generateRoleDeck();

        // Create the new round
        $round = Round::create([
            "game_id" => $game->id,
            "round_number" => $currentRound ? $currentRound->round_number + 1 : 1,
            "turn_number" => 1,
            "phase" => "role_selection",
            "players_turn" => 1,
            "deck" => $deck,
            "num_cards_in_deck" => count($deck),
            "role_deck" => $roleDeck,
            "num_cards_in_role_deck" => count($roleDeck),
            "available_roles" => Roles::countGeneratedRoles($roleDeck),
            "players_with_selected_roles" => [],
            "board" => Board::generateBoard($game),
            "gold_location" => rand(1, 3),
            "reached_gold_locations" => [],
            "winning_teams" => [],
            "revealed_players" => [],
            "current" => true,
        ]);

        // Return the created round
        return $round;
    }

    public function dealCardsToPlayers(Round $round)
    {
        // Grab the game's deck (so we can modify it)
        $deck = $round->deck;

        // Loop through the amount of cards to give to each player
        for ($i = 0; $i < 6; $i++)
        {
            // Loop through all of the game's players
            foreach ($round->game->players as $player)
            {
                // Grab a card from the top of the deck and remove it at the same time
                $card_id = array_shift($deck);

                // Add the card to the player's hand
                $newHand = $player->hand;
                $newHand[] = $card_id;
                $player->hand = $newHand;
                $player->save();
            }
        }

        // Save the new deck on the game
        $round->deck = $deck;
        $round->num_cards_in_deck = count($deck);
        $round->save();

        // Return the updated game
        return $round;
    }
}