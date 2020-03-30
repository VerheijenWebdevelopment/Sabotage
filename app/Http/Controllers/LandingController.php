<?php

namespace App\Http\Controllers;

use Auth;

class LandingController extends Controller
{
    public function getLanding()
    {
        if (Auth::check())
        {
            return redirect()->route("lobby");
        }
        else
        {
            return redirect()->route("login");
        }
    }
}