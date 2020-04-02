<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class RolesFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return "roles";
    }
}