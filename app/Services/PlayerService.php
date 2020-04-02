<?php

namespace App\Services;

use Users;
use App\Models\Game;
use App\Models\Player;
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
}