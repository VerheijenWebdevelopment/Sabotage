<?php

namespace App\Services;

use Cards;
use Roles;
use Players;
use Exception;

use App\Models\User;
use App\Models\Game;
use App\Models\Player;

use App\Traits\ModelServiceGetters;
use App\Contracts\ModelServiceContract;

use App\Events\Game\GameCreated;
use App\Events\Game\GameDeleted;
use App\Events\Game\GameStarted;
use App\Events\Game\PlayerLeftGame;
use App\Events\Game\PlayerJoinedGame;
use App\Events\Game\PlayerSelectedRole;
use App\Events\Game\TurnEnded;
use App\Events\Game\RoundEnded;
use App\Events\Game\GameEnded;

use App\Http\Requests\Api\Game\JoinGameRequest;
use App\Http\Requests\Api\Game\LeaveGameRequest;
use App\Http\Requests\Api\Game\CreateGameRequest;
use App\Http\Requests\Api\Game\DeleteGameRequest;
use App\Http\Requests\Api\Game\StartGameRequest;

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
    // Lobby operations
    //

    public function createFromRequest(CreateGameRequest $request)
    {
        $game = Game::create(["game_master_id" => auth()->user()->id]);
        
        $player = $this->join($game);

        broadcast(new GameCreated($player->user, $player, $game))->toOthers();
        
        return $this->findPreloaded($game->id);
    }

    public function deleteFromRequest(DeleteGameRequest $request)
    {
        $game = $this->find($request->game_id);

        broadcast(new GameDeleted(auth()->user(), $request->game_id))->toOthers();
        
        $game->delete();
    }

    public function joinFromRequest(JoinGameRequest $request)
    {
        $game = $this->find($request->game_id);

        $player = $this->join($game);

        broadcast(new PlayerJoinedGame($player->user, $player, $game))->toOthers();
        
        return $player;
    }

    public function join(Game $game, User $user = null)
    {
        if (is_null($user)) $user = auth()->user();

        $playerNumber = $game->players->count() + 1;

        $player = Player::create([
            "user_id" => $user->id,
            "game_id" => $game->id,
            "player_number" => $playerNumber,
        ]);
        
        return $player;
    }

    public function leaveFromRequest(LeaveGameRequest $request)
    {
        $game = $this->find($request->game_id);

        foreach ($game->players as $player)
        {
            if ($player->user_id == auth()->user()->id)
            {
                broadcast(new PlayerLeftGame($game, $player->id))->toOthers();
                
                $player->delete();

                break;
            }
        }
    }

    public function startFromRequest(StartGameRequest $request)
    {
        $game = $this->find($request->game_id);

        $game = $this->prepareGame($game);

        if ($game->status == "open")
        {
            $game->status = "ongoing";
            $game->save();
        }

        broadcast(new GameStarted(auth()->user(), $game))->toOthers();

        return $game;
    }

    public function prepareGame(Game $game)
    {
        // Compose the deck (TODO: based on settings)
        $game->deck = Cards::generateDeck($game);
        $game->num_cards_in_deck = count($game->deck);

        // Compose the available roles
        $game->roles = Roles::generateRoles($game);
        $game->available_roles = Roles::countGeneratedRoles($game->roles);

        // Set players with selected roles to an empty list
        $game->players_with_selected_roles = [];
        
        // Randomly determine the location of the gold
        $game->gold_location = rand(1, 3);

        // Generate a game board
        $game->board = Board::generateBoard($game);

        // Save changes to the game
        $game->save();

        // Deal the player's their cards
        $game = $this->dealCardsToPlayers($game);

        // Return updated game
        return $game;
    }

    private function dealCardsToPlayers(Game $game)
    {
        // Determine the amount of cards to deal to each player
        $cardsPerPlayer = $this->determineCardsPerPlayer($game);

        // Grab the game's deck (so we can modify it)
        $deck = $game->deck;

        // Loop through the amount of cards to deal to each player
        for ($i = 0; $i < $cardsPerPlayer; $i++)
        {
            // Loop through all of the game's players
            foreach ($game->players as $player)
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
        $game->deck = $deck;
        $game->num_cards_in_deck = count($game->deck);
        $game->save();

        // Return the updated game
        return $game;
    }

    private function determineCardsPerPlayer(Game $game)
    {
        switch ($game->players->count())
        {
            case 2:
            case 3:
            case 4:
            case 5:
                return 6;
            case 6:
            case 7:
                return 5;
            case 8:
            case 9:
            case 10:
            default:
                return 4;
        }
    }

    //
    // Game operations
    //

    public function performAction(Game $game, Player $player, string $action, string $data = null)
    {
        // Decode the data if we received some
        if (!is_null($data)) $data = (array) json_decode($data);

        // Switch between possible actions
        switch ($action)
        {
            // Player selected a role card
            case "selected_role_card":

                // Validate data
                if (is_null($data)) throw new Exception("Missing required data");
                if (!array_key_exists("index", $data)) throw new Exception("Missing selected card's index");

                // Perform the role card selection operation which returns the selected role
                $role = $this->performRoleCardSelected($game, $player, $data["index"]);

                // Return the selected role and the player's hand.
                // Which has been hidden until now to prevent other players from receiving eachother's hand.
                return [
                    "role" => $role,
                    "hand" => $player->hand,
                ];

            break;

            // Player folded a card
            case "fold_card":

                // Validate data
                if (is_null($data)) throw new Exception("Missing required data");
                if (!array_key_exists("index", $data)) throw new Exception("Missing selected card's index");

                // Perform the operation; returns a new card from the deck to give to the user
                $new_card = $this->performFoldCard($game, $player, $data["index"]);
                
                // Return the new card
                return ["new_card" => $new_card];

            break;

            // Player played an action card
            case "play_card":

                // Validate data
                if (is_null($data)) throw new Exception("Missing required data");

                

            break;
            
            // Throw an exception if the requested action is unknown to us
            default:
                throw new Exception("Can't perform unknown action '".$request->action."'");
            break;
        }
    }

    private function performRoleCardSelected(Game $game, Player $player, int $cardIndex)
    {
        // Grab the role that's associated with the card
        $role = Roles::find($game->roles[$cardIndex]);
        if (!$role) throw new Exception("Failed to retrieve the role associated to card with index ".$cardIndex);

        // Assign the player it's role & save changes
        $cleanPlayer = Player::find($player->id);
        $cleanPlayer->role_id = $role->id;
        $cleanPlayer->save();

        // Remove the option from the list of roles & make sure the values are re-indexed
        $roles = $game->roles;
        unset($roles[$cardIndex]);
        $newRoles = array_values($roles);
        
        // Update the game's pool of available roles
        $game->roles = $newRoles;

        // Update the game's list of players that have selected a role
        $newPlayersWithSelectedRoles = $game->players_with_selected_roles;
        $newPlayersWithSelectedRoles[] = $player->id;
        $game->players_with_selected_roles = $newPlayersWithSelectedRoles;

        // If the role selection phase has been completed, enter the game's main phase
        if (count($newPlayersWithSelectedRoles) == $game->players->count()) $game->phase = "main";

        // Save changes made to the game's state
        $game->save();

        // Broadcast event to inform all clients
        broadcast(new PlayerSelectedRole($game, $player))->toOthers();

        // End the player's turn
        $this->endTurn($game, $player);

        // Return the selected role
        return $role;
    }

    private function performFoldCard(Game $game, Player $player, int $cardIndex)
    {
        // Remove the card from the player's hand
        Players::removeCardFromHand($cardIndex, $player);

        // Draw a new card
        $card = $this->drawCard($game, $player);

        // End the player's turn
        $this->endTurn($game, $player);

        // Return (preloaded version of) the drawn card
        return Cards::preload($card);
    }

    private function performPlayCard(Game $game, Player $player, array $data)
    {

    }

    private function drawCard(Game $game, Player $player = null, $amount = 1)
    {
        if (is_null($player)) $player = Players::getActivePlayer();

        // Grab the deck and check if there are still cards on it
        $deck = $game->deck;
        if (count($deck) > 0) 
        {
            // Grab the top card and remove it from the deck at the same time
            $card_id = array_shift($deck);

            // Add the card to the user's hand
            $card = Cards::find($card_id);
            Players::addCardToHand($card, $player);

            // Save the new deck
            $game->deck = $deck;
            $game->save();

            // Return the drawn card
            return $card;
        }

        // Return false if we could not grab card from the deck
        return false;
    }

    private function endTurn(Game $game, Player $player = null)
    {
        // If no player was provided grab the active player
        if (is_null($player)) $player = Players::getActivePlayer();

        // Determine the number of the player who's next to have the turn
        $newPlayerNumber = $game->player_turn + 1;
        if ($newPlayerNumber > $game->players->count()) $newPlayerNumber = 1;

        // Update the game
        $game->player_turn = $newPlayerNumber;
        $game->turn++;
        $game->save();

        // If the round ended
        if ($this->roundEnded($game))
        {
            // If the game ended
            if ($this->gameEnded($game))
            {
                // Broadcast event to inform all clients
                broadcast(new GameEnded($game));
            }
            // If a new round is starting
            else
            {
                // Broadcast event to inform all clients
                broadcast(new RoundEnded($game));
            }
        }
        // If the game did not end yet
        else
        {
            // Broadcast event to inform all clients
            broadcast(new TurnEnded($game));
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
        return count($game->deck) == 0 && $this->playersOutOfCards($game);
    }

    public function gameEnded(Game $game)
    {
        return $game->round == 3;
    }
}