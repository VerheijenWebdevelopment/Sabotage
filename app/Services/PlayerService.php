<?php

namespace App\Services;

use Users;
use Games;
use Cards;

use App\Models\Card;
use App\Models\Game;
use App\Models\Player;
use App\Events\Game\PlayerToolBlocked;
use App\Events\Game\PlayerToolRecovered;
use App\Traits\ModelServiceGetters;
use App\Contracts\ModelServiceContract;

class PlayerService implements ModelServiceContract
{
    use ModelServiceGetters;

    private $model;
    private $records;
    private $preloadedRecords;

    public function __construct()
    {
        $this->model =  "App\Models\Player";
    }

    public function preload($instance)
    {
        $instance->user = Users::findPreloaded($instance->user_id);
        $instance->has_selected_role = $instance->hasSelectedRole;

        return $instance;
    }
    
    public function getAllForGame(Game $game)
    {
        $out = [];

        foreach ($this->getAllPreloaded() as $player)
        {
            if ($player->game_id == $game->id)
            {
                $out[] = $player;
            }
        }

        return collect($out);
    }

    public function getActivePlayer(User $user = null, $preload = true)
    {
        // If no user was provided grab the currently logged in user
        if (is_null($user)) $user = auth()->user();

        // Grab the user's currently active game
        $game = Games::getActiveGame($user);

        // If we found it
        if ($game)
        {
            // Loop through all available players
            foreach ($this->getAll() as $player)
            {
                // If the player belongs to the active game and the logged in user
                if ($player->game_id == $game->id and $player->user_id == $user->id)
                {
                    // Return (a preloaded version of) the player we found
                    return $preload ? $this->preload($player) : $player;
                }
            }
        }

        // Return false if something went wrong
        return false;
    }

    public function getActivePlayerRole(User $user = null)
    {
        // Grab the currently active player
        $player = $this->getActivePlayer($user);

        // If we found it and a role has been assigned to it
        if ($player and $player->role)
        {   
            // Return the role
            return $player->role;
        }

        // Otherwise return false
        return false;
    }

    public function removeCardFromHand(int $cardIndex, Player $player = null)
    {
        // If no player was provided grab the currently active player
        if (is_null($player)) $player = $this->getActivePlayer(false);
        
        // Validate the card's index
        if (!array_key_exists($cardIndex, $player->hand)) throw new Exception("Could not remove card from hand as index was invalid (".$cardIndex.")");

        // Remove the card from the player's hand
        $hand = $player->hand;
        unset($hand[$cardIndex]);

        // Update the player's hand
        $cleanPlayer = Player::find($player->id);
        $cleanPlayer->hand = array_values($hand);
        $cleanPlayer->save();

        return $cleanPlayer;
    }

    public function addCardToHand(Card $card, Player $player = null)
    {
        // Grab the active player if no player was provided
        if (is_null($player)) $player = $this->getActivePlayer();

        // Add the card to the player's hand
        $hand = $player->hand;
        $hand[] = $card->id;
        
        // Update the player's hand
        $cleanPlayer = Player::find($player->id);
        $cleanPlayer->hand = $hand;
        $cleanPlayer->save();
        
        return $cleanPlayer;
    }

    public function blockTool(Player $player, string $tool)
    {
        // Switch between the supported tools
        switch ($tool)
        {
            case "cart":
            case "light":
            case "pickaxe":
                
                // Update the flag
                if ($tool == "pickaxe" && $player->pickaxe_available) {
                    $player->pickaxe_available = false;
                } else if ($tool == "light" && $player->light_available) {
                    $player->light_available = false;
                } else if ($tool == "cart" && $player->cart_available) {
                    $player->cart_available = false;
                }

                // Save changes to player
                $player->save();

                // Broadcast event to other players
                broadcast(new PlayerToolBlocked($player, $tool))->toOthers();

            break;
            default:
                throw new Exception("Couldn't block unknown tool '".$tool."'");
            break;
        }
    }

    public function recoverTool(Player $player, string $tool)
    {
        // Switch between the supported tools
        switch ($tool)
        {
            case "cart":
            case "light":
            case "pickaxe":

                // Update the flag
                if ($tool == "pickaxe" && !$player->pickaxe_available) {
                    $player->pickaxe_available = true;
                } else if ($tool == "light" && !$player->light_available) {
                    $player->light_available = true;
                } else if ($tool == "cart" && !$player->cart_available) {
                    $player->cart_available = true;
                }

                // Save changes to the player
                $player->save();

                // Broadcast event to other player
                broadcast(new PlayerToolRecovered($player, $tool))->toOthers();

            break;
            default:
                throw new Exception("Couldn't recover unknown tool '".$tool."'");
            break;
        }
    }
}