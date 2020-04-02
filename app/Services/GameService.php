<?php

namespace App\Services;

use Players;
use App\Models\Game;
use App\Models\Player;
use App\Traits\ModelServiceGetters;
use App\Contracts\ModelServiceContract;
use App\Events\Game\GameCreated;
use App\Events\Game\GameDeleted;
use App\Events\Game\PlayerLeftGame;
use App\Events\Game\PlayerJoinedGame;
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

        return $instance;
    }

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

    public function createFromRequest(CreateGameRequest $request)
    {
        $game = Game::create([
            "game_master_id" => auth()->user()->id,
        ]);
        
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

    public function startFromRequest(StartGameRequest $request)
    {

    }
}