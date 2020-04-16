<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class LobbyFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "lobby";
    }
}