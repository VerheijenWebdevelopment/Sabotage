<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class GamesFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "games";
    }
}