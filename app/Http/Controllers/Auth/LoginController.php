<?php

namespace App\Http\Controllers\Auth;

use Auth;
use Users;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;

class LoginController extends Controller
{
    public function getLogin()
    {
        // Render the login page
        return view("pages.auth.login", [
            "oldInput" => collect([
                "email" => old("email"),
            ])
        ]);
    }

    public function postLogin(LoginRequest $request)
    {
        // Grab the user
        $user = Users::findByEmail($request->email);
        
        // Convert remember_me value from a string to a boolean
        $remember_me = $request->remember_me == "true" ? true : false;
        
        // Attempt to login the user using the given credentials
        if (!Auth::attempt(["email" => $request->email, "password" => $request->password], $remember_me))
        {
            // Return back to login with an error message
            flash(__("auth.login_invalid_password"))->error();
            return redirect()->route("login");
        }
        
        // Redirect the user to lobby
        return redirect()->route("lobby");
    }
}