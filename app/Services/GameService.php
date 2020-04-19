<?php

namespace App\Services;

use Cards;
use Roles;
use Board;
use Rounds;
use Players;
use Exception;
use GameMessages;

use App\Models\User;
use App\Models\Card;
use App\Models\Game;
use App\Models\Player;
use App\Models\GameChatMessage;

use App\Traits\ModelServiceGetters;
use App\Contracts\ModelServiceContract;

use App\Events\Game\PlayerSelectedRole;
use App\Events\Game\PlayerToolBlocked;
use App\Events\Game\PlayerToolRecovered;
use App\Events\Game\PlayerFreed;
use App\Events\Game\PlayerImprisoned;
use App\Events\Game\PlayerIsThief;
use App\Events\Game\PlayerNoLongerThief;
use App\Events\Game\PlayerCheckedGoldLocation;
use App\Events\Game\PlayerPlacedTunnel;
use App\Events\Game\PlayerCollapsedTunnel;
use App\Events\Game\TurnEnded;
use App\Events\Game\RoundEnded;
use App\Events\Game\GameEnded;
use App\Events\Game\GoldLocationRevealed;
use App\Events\Game\PlayerIsReadyForNextRound;
use App\Events\Game\PlayerWasAwardedGold;
use App\Events\Game\NewRoundStarted;

use App\Http\Requests\Api\Game\SendGameMessageRequest;

class GameService implements ModelServiceContract
{
    use ModelServiceGetters;

    private $model;
    private $records;
    private $preloadedRecords;

    public function __construct()
    {
        $this->model =  "App\Models\Game";
    }

    public function preload($instance)
    {
        $instance->players = Players::getAllForGame($instance);
        $instance->num_available_roles = $instance->numberOfAvailableRoles;
        $instance->round = Rounds::findPreloaded($instance->current_round_id);
        $instance->messages = GameMessages::getAllForGame($instance);

        return $instance;
    }

    //
    // Getters
    //

    public function findByUuid($uuid)
    {
        foreach ($this->getAll() as $game)
        {
            if ($game->uuid == $uuid)
            {
                return $game;
            }
        }

        return $game;
    }

    public function findPreloadedByUuid($uuid)
    {
        foreach ($this->getAllPreloaded() as $game)
        {
            if ($game->uuid == $uuid)
            {
                return $game;
            }
        }

        return $game;
    }

    public function getOpenAndOutstandingGames()
    {
        $out = [];

        foreach ($this->getAllPreloaded() as $game)
        {
            if ($game->status != "completed")
            {
                $out[] = $game;
            }
        }

        return collect($out);
    }

    public function getActiveGame(User $user = null)
    {
        if (is_null($user)) $user = auth()->user();

        foreach ($this->getAllPreloaded() as $game)
        {
            if ($game->status === "ongoing")
            {
                foreach ($game->players as $player)
                {
                    if ($player->user_id == $user->id)
                    {
                        return $game;
                    }
                }
            }
        }

        return false;
    }

    //
    // Setters
    //

    public function setStatus(Game $game, $status)
    {
        $game->status = $status;
        $game->save();

        return $game;
    }

    public function setPhase(Game $game, $phase)
    {
        $game->phase = $phase;
        $game->save();

        return $game;
    }

    //
    // Lobby operations
    //

    public function join(Game $game, User $user = null)
    {
        // If no user was provided grab the currently logged in user
        if (is_null($user)) $user = auth()->user();

        // Determine the player's number
        $playerNumber = $game->players->count() + 1;

        // Create the player
        $player = Player::create([
            "user_id" => $user->id,
            "game_id" => $game->id,
            "player_number" => $playerNumber,
        ]);
            
        // Return the created player
        return $player;
    }

    public function prepareGame(Game $game)
    {
        // Create the (next) round of the game we'll be going to play in
        $round = Rounds::createNewRound($game);

        // Set the round as the game's current round
        $game->current_round_id = $round->id;
        $game->save();
        
        // Deal the player's their cards
        $round = Rounds::dealCardsToPlayers($round);

        // Return updated game
        return $game;
    }

    //
    // Game operations
    //

    public function performAction(Game $game, Player $player, string $action, string $data = null)
    {
        // Decode the data if we received some
        if (!is_null($data)) $data = (array) json_decode($data);

        // Data we're returning
        $output = [];

        // Switch between possible actions
        switch ($action)
        {
            // -----------------------------------------------------
            // Role selection phase
            // -----------------------------------------------------

            // Player selected a role card
            case "selected_role_card":

                // Validate data
                if (is_null($data)) throw new Exception("Missing required data");
                if (!array_key_exists("index", $data)) throw new Exception("Missing selected card's index");

                // Perform the role card selection operation which returns the selected role
                $role = $this->performRoleCardSelected($game, $player, $data["index"]);

                // Return the selected role and the player's hand.
                // Which has been hidden until now to prevent other players from receiving eachother's hand.
                $output = [
                    "role" => $role,
                    "hand" => $player->hand,
                ];

            break;

            // -----------------------------------------------------
            // Game phase
            // -----------------------------------------------------

            // Player folded a card
            case "fold_card":

                // Validate data
                if (is_null($data)) throw new Exception("Missing required data");
                if (!array_key_exists("index", $data)) throw new Exception("Missing selected card's index");

                // Perform the operation; returns a new card from the deck to give to the user
                $new_card = $this->performFoldCard($game, $player, $data["index"]);
                
                // Return the new card as output
                $output["new_card"] = $new_card;

            break;

            // Player folded multiple cards
            case "fold_cards":

                // Validate data
                if (is_null($data)) throw new Exception("Missing required data");
                if (!array_key_exists("indices", $data)) throw new Exception("Missing selected card indices");

                // Perform the operation
                $new_cards = $this->performFoldCards($game, $player, $data["indices"]);

                // Return the new cards as output
                $output["new_cards"] = $new_cards;

            break;

            // Player folded 2 cards and want to unblock a tool            
            case "fold_cards_unblock":

                // Validate data
                if (is_null($data)) throw new Exception("Missing required data");
                if (!array_key_exists("indices", $data)) throw new Exception("Missing selected card indices");
                if (!array_key_exists("tool", $data)) throw new Exception("Missing tool to unblock");

                // Perform the operation
                $new_card = $this->performFoldCardsAndUnblock($game, $player, $data["indices"], $data["tool"]);

                // Return the new card as output
                $output["new_card"] = $new_card;

            break;

            // Player played an action card
            case "play_card":

                // Validate data
                if (is_null($data)) throw new Exception("Missing required data");
                if (!array_key_exists("index", $data)) throw new Exception("Missing selected card's index");

                // Perform the operation; returns a new card from the deck
                $output = $this->performPlayCard($game, $player, $data);

            break;
            
            // -----------------------------------------------------
            // Reward phase
            // -----------------------------------------------------

            // Player has selected gold reward card
            case "select_gold_reward_card":

                // Validate data
                if (is_null($data)) throw new Exception("Missing required data");
                if (!array_key_exists("index", $data)) throw new Exception("Missing reward card index");

                // Perform the operation; returns the selected reward card
                $output = $this->performSelectGoldRewardCard($game, $player, $data["index"]);

            break;
            
            // Player is ready for the next round
            case "flag_ready":

                // Perform operation
                $game = $this->flagPlayerAsReadyForNextRound($game, $player);
                
                // Skip the end turn & end round logic
                return [];

            break;

            // Throw an exception if the requested action is unknown to us
            default:
                throw new Exception("Can't perform unknown action '".$action."'");
            break;
        }

        // If the round has ended
        if ($this->roundEnded($game))
        {
            $this->endRound($game, $player);
        }
        // If the round has not ended yet
        else
        {
            // End the player's turn (and give the next player the turn)
            $this->endTurn($game, $player);
        }

        // Return the output from the action we've performed
        return $output;
    }

    private function performRoleCardSelected(Game $game, Player $player, int $cardIndex)
    {
        // Grab the role that's associated with the card
        $role = Roles::find($game->currentRound->role_deck[$cardIndex]);
        if (!$role) throw new Exception("Failed to retrieve the role associated to card with index ".$cardIndex);

        // Assign the player it's role & save changes
        $cleanPlayer = Player::find($player->id);
        $cleanPlayer->role_id = $role->id;
        $cleanPlayer->save();

        // Remove the option from the list of roles & make sure the values are re-indexed
        $roleDeck = $game->currentRound->role_deck;
        unset($roleDeck[$cardIndex]);
        $newRoleDeck = array_values($roleDeck);
        
        // Update the round's deck of available role cards
        $game->currentRound->role_deck = $newRoleDeck;

        // Update the game's list of players that have selected a role
        $newPlayersWithSelectedRoles = $game->currentRound->players_with_selected_roles;
        $newPlayersWithSelectedRoles[] = $player->id;
        $game->currentRound->players_with_selected_roles = $newPlayersWithSelectedRoles;

        // If the role selection phase has been completed, enter the round's main phase
        if (count($newPlayersWithSelectedRoles) == $game->players->count()) $game->currentRound->phase = "main";

        // Save changes made to the game's state
        $game->currentRound->save();

        // Broadcast event to inform all clients
        broadcast(new PlayerSelectedRole($game, $player))->toOthers();

        // Return the selected role
        return $role;
    }

    private function performFoldCard(Game $game, Player $player, int $cardIndex)
    {
        // Remove the card from the player's hand
        $player = Players::removeCardFromHand($cardIndex, $player);

        // Draw a new card
        $card = $this->drawCard($game, $player);

        // Return (preloaded version of) the drawn card
        return $card ? Cards::preload($card) : false;
    }

    private function performFoldCards(Game $game, Player $player, array $indices)
    {
        // Remove the cards from the player's hand
        $player = Players::removeCardsFromHand($indices, $player);

        // Draw new cards
        $cards = $this->drawCards($game, $player, count($indices));

        // Return drawn cards
        return $cards;
    }

    private function performFoldCardsAndUnblock(Game $game, Player $player, array $indices, string $tool)
    {

    }

    private function performPlayCard(Game $game, Player $player, array $data)
    {
        // Data we're outputting
        $output = [];

        // Grab the card the user wants to play
        $card = Cards::find($player->hand[$data["index"]]);

        // Remove card from player's hand
        $player = Players::removeCardFromHand($data["index"], $player);

        // If we're dealing with an action card
        if ($card->type == "action")
        {
            // Switch between possible action cards
            switch ($card->name)
            {
                // Single sabotage cards
                case "sabotage_pickaxe":
                case "sabotage_light":
                case "sabotage_cart":
                    if (!array_key_exists("player_id", $data)) throw new Exception("Missing target player's ID");
                    $this->playSabotageCard($game, $player, $card, $data);
                break;

                // Multi sabotage cards
                case "sabotage_pickaxe_cart":
                case "sabotage_pickaxe_light":
                case "sabotage_light_cart":
                    if (!array_key_exists("player_id", $data)) throw new Exception("Missing target player's ID");
                    if (!array_key_exists("tool", $data)) throw new Exception("Missing target tool");
                    $this->playSabotageCard($game, $player, $card, $data);
                break;

                // Recover cards
                case "recover_pickaxe":
                case "recover_light":
                case "recover_cart":
                    if (!array_key_exists("player_id", $data)) throw new Exception("Missing target player's ID");
                    $this->playRecoverCard($game, $player, $card, $data);
                break;
                
                // Multi recover cards
                case "recover_pickaxe_cart":
                case "recover_pickaxe_light":
                case "recover_light_cart":
                    if (!array_key_exists("player_id", $data)) throw new Exception("Missing target player's ID");
                    if (!array_key_exists("tool", $data)) throw new Exception("Missing target tool");
                    $this->playRecoverCard($game, $player, $card, $data);
                break;

                // Enlighten card
                case "enlighten":
                    if (!array_key_exists("gold_location", $data)) throw new Exception("Missing target gold location");
                    $output["contains_gold"] = $this->playEnlightenCard($game, $player, $data["gold_location"]);
                break;
                
                // Collapse card
                case "collapse":
                    if (!array_key_exists("target_coordinates", $data)) throw new Exception("Missing target tile coordinates");
                    $output["board"] = $this->playCollapseCard($game, $player, $card, $data);
                break;

                // Thief card
                case "thief":
                    if (!array_key_exists("player_id", $data)) throw new Exception("Missing target player's ID");
                    $this->playThiefCard($game, $player, $card, $data);
                break;

                // Dont touch card
                case "dont_touch":
                    if (!array_key_exists("player_id", $data)) throw new Exception("Missing target player's ID");
                    $this->playDontTouchCard($game, $player, $card, $data);
                break;

                // Imprison card
                case "imprison":
                    if (!array_key_exists("player_id", $data)) throw new Exception("Missing target player's ID");
                    $this->playImprisonCard($game, $player, $card, $data);
                break;

                // Free card
                case "free":
                    if (!array_key_exists("player_id", $data)) throw new Exception("Missing target player's ID");
                    $this->playFreeCard($game, $player, $card, $data);
                break;
            }
        }
        // If we're dealing with a tunnel card
        else
        {
            // Validate the request
            if (!array_key_exists("target_coordinates", $data)) throw new Exception("Missing target tile coordinates");
            if (!array_key_exists("inverted", $data)) throw new Exception("Missing inverted property");

            // Play the card
            $game = $this->playTunnelCard($game, $player, $card, $data);

            // Add the updated board to the output
            $output["board"] = $game->board;
        }

        // Draw a new card
        $newCard = $this->drawCard($game, $player);
        
        // Add the drawn card to the output
        $output["new_card"] = $newCard ? Cards::preload($newCard) : false;

        // Return the output
        return $output;
    }

    private function playThiefCard(Game $game, Player $player, Card $card)
    {
        // Activate thief status on current player
        $player->thief_activated = true;
        $player->save();

        // Broadcast event to other players
        broadcast(new PlayerIsThief($game, $player));
    }

    private function playDontTouchCard(Game $game, Player $player, Card $card, array $data)
    {
        // Grab the player we're targetting
        $targetPlayer = Players::find($data["player_id"]);
        if (!$targetPlayer) throw new Exception("Received target player's ID is invalid");
        
        // Remove thief status from target player
        $targetPlayer->thief_activated = false;
        $targetPlayer->save();

        // Broadcast event to other players
        broadcast(new PlayerNoLongerThief($game, $player, $targetPlayer))->toOthers();
    }

    private function playImprisonCard(Game $game, Player $player, Card $card, array $data)
    {
        // Grab the player we're targetting
        $targetPlayer = Players::find($data["player_id"]);
        if (!$targetPlayer) throw new Exception("Received target player's ID is invalid");

        // Place the target player in jail
        $targetPlayer->in_jail = true;
        $targetPlayer->save();

        // Broadcast event to other players
        broadcast(new PlayerImprisoned($game, $player, $targetPlayer))->toOthers();
    }

    private function playFreeCard(Game $game, Player $player, Card $card, array $data)
    {
        // Grab the player we're targetting
        $targetPlayer = Player::find($data["player_id"]);
        if (!$targetPlayer) throw new Exception("Received target player's ID is invalid");
        
        // Free the target player from jail
        $targetPlayer->in_jail = false;
        $targetPlayer->save();

        // Broadcast event to other player
        broadcast(new PlayerFreed($game, $player, $targetPlayer));
    }

    private function playSabotageCard(Game $game, Player $player, Card $card, array $data)
    {
        // Grab the player we're targetting
        $targetPlayer = Players::find($data["player_id"]);
        if (!$targetPlayer) throw new Exception("Received target player's ID is invalid");

        // Switch between the possible variants of the sabotage card
        switch ($card->name)
        {
            case "sabotage_pickaxe":
                $tool = "pickaxe";
                $targetPlayer->pickaxe_available = false;
            break;
            case "sabotage_light":
                $tool = "light";
                $targetPlayer->light_available = false;
            break;
            case "sabotage_cart":
                $tool = "cart";
                $targetPlayer->cart_available = false;
            break;
            case "sabotage_pickaxe_light":
                if ($data["tool"] == "pickaxe") {
                    $tool = "pickaxe";
                    $targetPlayer->pickaxe_available = false;
                } else {
                    $tool = "light";
                    $targetPlayer->light_available = false;
                }
            break;
            case "sabotage_pickaxe_cart":
                if ($data["tool"] == "pickaxe") {
                    $tool = "pickaxe";
                    $targetPlayer->pickaxe_available = false;
                } else {
                    $tool = "cart";
                    $targetPlayer->cart_available = false;
                }
            break;
            case "sabotage_light_cart":
                if ($data["tool"] == "light") {
                    $tool = "light";
                    $targetPlayer->light_available = false;
                } else {
                    $tool = "cart";
                    $targetPlayer->cart_available = false;
                }
            break;
        }

        // Save the updated flag on the target player
        $targetPlayer->save();

        // Broadcast event to the other players
        broadcast(new PlayerToolBlocked($game, $player, $targetPlayer, $tool))->toOthers();
    }

    private function playRecoverCard(Game $game, Player $player, Card $card, array $data)
    {
        // Grab the player we're targetting
        $targetPlayer = Player::find($data["player_id"]);
        if (!$targetPlayer) throw new Exception("Received target player's ID is invalid");

        // Switch between the possible variants of the sabotage card
        switch ($card->name)
        {
            case "recover_pickaxe":
                $tool = "pickaxe";
                $targetPlayer->pickaxe_available = true;
            break;
            case "recover_light":
                $tool = "light";
                $targetPlayer->light_available = true;
            break;
            case "recover_cart":
                $tool = "cart";
                $targetPlayer->cart_available = true;
            break;
            case "recover_pickaxe_light":
                if ($data["tool"] == "pickaxe") {
                    $tool = "pickaxe";
                    $targetPlayer->pickaxe_available = true;
                } else {
                    $tool = "light";
                    $targetPlayer->light_available = true;
                }
            break;
            case "recover_pickaxe_cart":
                if ($data["tool"] == "pickaxe") {
                    $tool = "pickaxe";
                    $targetPlayer->pickaxe_available = true;
                } else {
                    $tool = "cart";
                    $targetPlayer->cart_available = true;
                }
            break;
            case "recover_light_cart":
                if ($data["tool"] == "light") {
                    $tool = "light";
                    $targetPlayer->light_available = true;
                } else {
                    $tool = "cart";
                    $targetPlayer->cart_available = true;
                }
            break;
        }

        // Save the updated flag on the target player
        $targetPlayer->save();

        // Broadcast event to the other players
        broadcast(new PlayerToolRecovered($game, $player, $targetPlayer, $tool))->toOthers();
    }

    private function playEnlightenCard(Game $game, Player $player, int $goldLocation)
    {
        // Inform other players whats going on
        broadcast(new PlayerCheckedGoldLocation($game, $player, $goldLocation))->toOthers();

        // Return whether or not the target gold location contains gold or not
        return $game->gold_location == $goldLocation;
    }

    private function playCollapseCard(Game $game, Player $player, Card $card, array $data)
    {
        // Grab the game board
        $board = $game->board;

        // Set the target tile to null to destroy any placed tunnel
        $board[$data["target_coordinates"]->y][$data["target_coordinates"]->x] = null;
        
        // Update the game's board
        $game->board = $board;
        $game->save();

        // Broadcast event to all other players
        broadcast(new PlayerCollapsedTunnel($game, $player, (array) $data["target_coordinates"]))->toOthers();

        // Return the updated board
        return $board;
    }

    private function playTunnelCard(Game $game, Player $player, Card $card, array $data)
    {
        // Place the card on the board
        $board = Board::placeCard($game, $card, $data["inverted"], $data["target_coordinates"]->x, $data["target_coordinates"]->y);

        // Broadcast event to all other players to inform them of the update
        broadcast(new PlayerPlacedTunnel($game, $player, $card, (array) $data["target_coordinates"], $data["inverted"]))->toOthers();

        // Check if we've reached a gold location
        $reached_gold_locations = Board::goldLocationsReached($board);
        if (count($reached_gold_locations))
        {
            // Loop through all of the reached gold locations
            foreach ($reached_gold_locations as $gold_location)
            {
                // If the gold location had not been revealed yet
                if (!array_key_exists($gold_location, $game->reached_gold_locations))
                {
                    // Replace the gold location card in question with the revealed gold location card (coal/gold card)
                    $game = Board::revealGoldLocation($game, $gold_location);

                    // Broadcast the event to everyone (including the current user) so we can update the frontend
                    broadcast(new GoldLocationRevealed($game, $gold_location));
                }
            }
        }

        // Return the updated game
        return $game;
    }

    private function performSelectGoldRewardCard(Game $game, Player $player, int $cardIndex)
    {
        // Grab the reward card that's associated with the selected card
        $rewardCard = Cards::find($game->reward_deck[$cardIndex]);
        if (!$rewardCard) throw new Exception("Failed to retrieve the selected reward card");

        // Remove the card from the deck
        $newDeck = $game->reward_deck;
        unset($newDeck[$cardIndex]);

        // Save the new deck
        $game = Game::find($game->id);
        $game->reward_deck = array_values($newDeck);
        $game->num_cards_reward_deck = count($newDeck);
        $game->save();

        // Determine the reward based on the selected card
        $reward = 0;
        switch ($rewardCard->name)
        {
            case "reward_one_gold":
                $reward = 1;
            break;
            case "reward_two_gold":
                $reward = 2;
            break;
            case "reward_three_gold":
                $reward = 3;
            break;
            case "reward_four_gold":
                $reward = 4;
            break;
        }

        // Award the player their reward
        $player = Player::find($player->id);
        $player->score = $player->score + $reward;
        $player->save();

        // Update the revealed players entry for the player
        $revealedPlayers = $game->revealed_players;
        for ($i = 0; $i < count($revealedPlayers); $i++)
        {
            if ($revealedPlayers[$i]["player"]->id == $player->id)
            {
                $revealedPlayers[$i]["gold_awarded"] = $reward;
                $revealedPlayers[$i]["gold_reward_card"] = $rewardCard;
                $revealedPlayers[$i]["selected_reward"] = true;
                break;
            }
        }
        $game->revealed_players = $revealedPlayers;
        $game->save();

        // Broadcast event to all other players what the foshizzle just happened
        broadcast(new PlayerWasAwardedGold($game, $player, $reward, $rewardCard))->toOthers();

        // Determine the number of gold diggers that are playing and the number of gold diggers that have picked their reward
        $num_diggers = 0;
        $num_diggers_rewarded = 0;
        foreach ($revealedPlayers as $revealedPlayer)
        {
            if ($revealedPlayer["player"]->role->name == "digger") 
            {
                $num_diggers += 1;
                if ($revealedPlayer["selected_reward"]) $num_diggers_rewarded += 1;
            }
        }

        // If all golddiggers have now selected their reward and this is the third round; end the game
        if ($num_diggers == $num_diggers_rewarded && $game->round == 3)
        {
            $this->endGame($game);
        }

        // Return the data
        return [
            "reward_card" => $rewardCard,
            "reward" => $reward,
        ];
    }

    private function drawCard(Game $game, Player $player = null)
    {
        // Grab current player if none was provided
        if (is_null($player)) $player = Players::getActivePlayer();

        // Grab fresh instance of the player
        $player = Player::find($player->id);

        // Grab the deck and check if there are still cards on it
        $deck = $game->currentRound->deck;
        if (count($deck) > 0) 
        {
            // Grab the top card and remove it from the deck at the same time
            $card_id = array_shift($deck);

            // Add the card to the user's hand
            $card = Cards::find($card_id);
            Players::addCardToHand($card, $player);
            
            // Update the current round's deck
            $game->currentRound->deck = $deck;
            $game->currentRound->num_cards_in_deck = count($deck);
            $game->currentRound->save();

            // Return the draw card
            return $card;
        }

        // If we failed to draw any cards
        return false;
    }

    private function drawCards(Game $game, Player $player, $amount)
    {
        $out = [];

        $originalHand = $player->hand;

        // Loop for the amount of cards we're supposed to draw
        for ($i = 0; $i < $amount; $i++)
        {
            // Attempt to draw a card & add it to the output if we succeed
            $card = $this->drawCard($game, $player);
            if ($card)
            {
                $out[] = Cards::preload($card);
            }
        }

        // Return drawn cards
        return $out;
    }

    private function endTurn(Game $game, Player $player = null)
    {
        // If no player was provided grab the active player
        if (is_null($player)) $player = Players::getActivePlayer();

        // Grab latest version of the game
        $game = Game::find($game->id);

        // Determine the number of the player who's next to have the turn
        $newPlayerNumber = $this->determineNextPlayerToHaveTurn($game);

        // Update the game
        $game->currentRound->players_turn = $newPlayerNumber;
        $game->currentRound->turn_number++;
        $game->currentRound->save();

        // Broadcast event to inform all clients
        broadcast(new TurnEnded($game));
    }

    private function determineNextPlayerToHaveTurn(Game $game)
    {
        // If we're in the game phase
        if ($game->currentRound->phase == "role_selection" || $game->currentRound->phase == "main")
        {
            // Simply go clockwise; so + 1
            $newPlayerNumber = $game->currentRound->players_turn + 1;

            // If we've reached a ghost; go back to player one
            if ($newPlayerNumber > $game->players->count()) $newPlayerNumber = 1;

            // Return the new player's number
            return $newPlayerNumber;
        }
        // If we're in the rewards phase and the winning team are the goldiggers (otherwise rewards are instant and there are no turns)
        else if ($game->currentRound->phase == "rewards" && $game->currentRound->winning_team == "golddiggers")
        {
            // Generate an array of players keyed by their player number for easy peasy access
            $players = [];
            foreach ($game->players as $player) $players[$player->player_number] = $player;

            // No we go counter clockwise and we only give the turn to golddiggers; she taaaake my mooooney
            return $this->findNextGoldDigger($game->currentRound->players_turn, $players);
        }

        // If we're in another phase; i'm pretty sure we're fucked bigtime; let's just return the first player
        return 1;
    }

    private function findNextGoldDigger($currentPlayerNumber, $players)
    {
        // Calculate the new player's number.. -1... hmmzmzxm...
        $newPlayerNumber = $currentPlayerNumber - 1;
        if ($newPlayerNumber == 0) $newPlayerNumber = count($players);

        // If this player is a golddigger
        if ($players[$newPlayerNumber]->role->name == "digger")
        {
            // We found the fucker; return the new player's number
            return $newPlayerNumber;
        }
        // If this player is a saboteur; cheeky cheeky
        else
        {
            // Proceed to recursively try to find the next saboteur until we arrive back at the current one if there's only one
            return $this->findNextGoldDigger($newPlayerNumber, $players);
        }
    }

    private function endRound(Game $game, Player $player)
    {
        // Enter the 'rewards' phase
        $game->phase = "rewards";

        // Determine which team has won
        $gold_found = array_key_exists($game->gold_location, $game->reached_gold_locations);
        $winningTeam = $gold_found ? "golddiggers" : "saboteurs";
        $game->winning_team = $winningTeam;

        // If the golddiggers won; they will get to choose a reward card and the player
        // who found the gold gets to choose; so set the turn to that player's number
        if ($winningTeam == "golddiggers")
        {
            // If the player that finished the board (current player) is a golddigger; give them the turn
            if ($player->role->name == "digger")
            {
                $game->players_turn = $player->player_number;
            }
            // If the player that finished had another role; first golddigger we find gets the turn
            else
            {
                foreach ($game->players as $player) {
                    if ($player->role->name == "digger") {
                        $game->players_turn = $player->player_number;
                        break;
                    }
                }
            }
        }

        // Generate reward cards
        $rewardCards = $winningTeam == "saboteurs" ? [] : Cards::generateRewardCards($game);
        $game->reward_deck = $rewardCards;
        $game->num_cards_reward_deck = count($rewardCards);

        // Generate saboteur gold reward
        $saboteurReward = $winningTeam == "golddiggers" ? 0 : $this->determineSaboteurGoldReward($game);
        $game->saboteur_reward = $saboteurReward;
        
        // Generate revealed players array
        $revealedPlayers = [];
        foreach ($game->players as $player)
        {
            // If the saboteurs won & the player is a saboteur; award them their gold instantly
            if ($winningTeam == "saboteurs" && $player->role->name == "saboteur")
            {
                $newScore = $player->score + $saboteurReward;
                $player->score = $newScore;
                $player->save();
            }

            // Add entry to revealed players
            $revealedPlayers[] = [
                "player" => $player,
                "role" => $player->role,
                "winner" => ($winningTeam == "saboteurs" && $player->role->name == "saboteur") || ($winningTeam == "golddiggers" && $player->role->name == "digger"),
                "ready" => false,
                "selected_reward" => false,
                "gold_awarded" => ($winningTeam == "saboteurs" && $player->role->name == "saboteur") ? $saboteurReward : 0,
                "gold_reward_card" => null,
            ];
        }
        $game->revealed_players = $revealedPlayers;

        // Save changes we made to the game
        $game->save();

        // If the saboteurs won and we're in the last round
        if ($winningTeam == "saboteurs" && $game->round == 3)
        {
            $this->endGame($game);
        }
        // Otherwise; broadcast the round ended event
        else
        {
            // Compose the data we want to send to the client (all updates we've done basically except for the deck)
            $data = [
                "winning_team" => $winningTeam,
                "saboteur_reward" => $saboteurReward,
                "revealed_players" => $revealedPlayers,
                "num_cards_reward_deck" => count($rewardCards),
            ];

            // Broadcast event to all players (including the one who initiated this process)
            broadcast(new RoundEnded($game, $data));
        }
    }

    private function determineSaboteurGoldReward(Game $game)
    {
        // Determine the amount of saboteurs were in play
        $numSaboteurs = 0;
        foreach ($game->players as $player)
        {
            if ($player->role->name == "saboteur") $numSaboteurs++;
        }

        // Return the amount of gold to award according to the rules..
        switch ($numSaboteurs)
        {
            case 1:
                return 4;
            case 2:
            case 3:
                return 3;
            default:
                return 2;
        }
    }

    private function flagPlayerAsReadyForNextRound(Game $game, Player $player)
    {
        // Update the game's revealed players array
        $revealedPlayers = $game->revealed_players;
        for ($i = 0; $i < count($revealedPlayers); $i++) {
            if ($revealedPlayers[$i]["player"]->id == $player->id) {
                $revealedPlayers[$i]["ready"] = true;
                break;
            }
        }

        // Save the updated array on the game
        $game->revealed_players = $revealedPlayers;
        $game->save();

        // Broadcast to all other players that the player is ready
        broadcast(new PlayerIsReadyForNextRound($game, $player))->toOthers();

        // Determine how many players are ready
        $numPlayersReady = 0;
        foreach ($game->revealed_players as $revealedPlayer)
        {
            if ($revealedPlayer["ready"])
            {
                $numPlayersReady += 1;
            }
        }
        
        // If all players are ready 
        if ($game->players->count() == $numPlayersReady)
        {
            // Start a new round
            $this->startNewRound($game);
        }

        // Return the updated game
        return $game;
    }

    private function startNewRound(Game $game)
    {
        // Reset the players
        foreach ($game->players as $player)
        {
            $player->role_id = null;
            $player->cart_available = true;
            $player->light_available = true;
            $player->pickaxe_available = true;
            $player->in_jail = false;
            $player->thief_activated = false;
            $player->hand = [];
            $player->save();
        }

        // Re-prepare the game for the next round
        $game = $this->prepareGame($game);

        // Up the round count by one
        $game->round = $game->round + 1;
        $game->players_turn = 1;
        $game->save();

        // Broadcast event to all players
        broadcast(new NewRoundStarted($game));
    }

    private function endGame(Game $game)
    {
        // Update the game's status
        $game->status = "completed";
        
        // Determine the game's winners
        $highestScore = 0;
        foreach ($game->players as $player)
        {
            if ($player->score > $highestScore) $highestScore = $player->score;
        }
        $winners = [];
        foreach ($game->players as $player)
        {
            if ($player->score == $highestScore) $winners[] = $player->id;
        }

        // Create the winner entries 
        $game->winners()->attach($winners);

        // Save changes to game
        $game->save();

        // Broadcast event to all players
        broadcast(new GameEnded($game, $winners));

        // Update all player's user highscores
        foreach ($game->players as $player)
        {
            if ($player->score > 0)
            {
                $highscore = $player->user->highscore + $player->score;
                $gamesPlayed = $player->user->games_played + 1;
                $player->user->highscore = $highscore;
                $player->user->games_played = $gamesPlayed;
                $player->user->save();
            }
        }
    }

    //
    // Checks
    //

    public function userIsPlayerInGame(Game $game)
    {
        $user = auth()->user();

        foreach ($game->players as $player)
        {
            if ($player->user->id == $user->id)
            {
                return true;
            }
        }

        return false;
    }

    public function playerIsInGame(Player $player, Game $game)
    {
        foreach (Players::getAllForGame($game) as $p)
        {
            if ($p->id == $player->id) return true;
        }

        return false;
    }
    
    public function userHasActiveGame(User $user = null)
    {
        if (is_null($user)) $user = auth()->user();

        return $this->getActiveGame($user) ? true : false;
    }

    public function playersOutOfCards(Game $game)
    {
        foreach ($game->players as $player)
        {
            if (count($player->hand) > 0) return false;
        }

        return true;
    }

    public function roundEnded(Game $game)
    {
        // Grab latest version of the game
        $game = Game::find($game->id);

        // If we're in the game phase
        if ($game->currentRound->phase == "main")
        {
            // Round ends when there are no cards left in the deck & player's hands or the gold has been found
            return (count($game->currentRound->deck) == 0 && $this->playersOutOfCards($game)) || array_key_exists($game->currentRound->gold_location, $game->currentRound->reached_gold_locations);
        }
        
        // Return false
        return false;
    }

    public function gameEnded(Game $game)
    {
        return $game->currentRound->round_number == 3;
    }
}