<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PlayersFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "players";
    }
}