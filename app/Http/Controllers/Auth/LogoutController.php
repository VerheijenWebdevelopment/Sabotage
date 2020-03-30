<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\Http\Controllers\Controller;

class LogoutController extends Controller
{
    public function getLogout()
    {
        // Logout the user
        Auth::logout();
        
        // Redirect to the login page with a success message
        flash(__("auth.logged_out"))->success();
        return redirect()->route("login");
    }
}