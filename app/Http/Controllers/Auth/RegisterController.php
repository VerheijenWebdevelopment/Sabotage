<?php

namespace App\Http\Controllers\Auth;

use Auth;
use Users;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;

class RegisterController extends Controller
{
    public function getRegister()
    {
        // Render the register page
        return view("pages.auth.register", [
            "oldInput" => collect([
                "username" => old("username"),
                "name" => old("name"),
                "email" => old("email"),
            ])
        ]);
    }

    public function postRegister(RegisterRequest $request)
    {
        // Create the user's account
        $user = Users::createFromRegisterRequest($request);
        
        // Login the account
        Auth::login($user);
        
        // Redirect to the homepage with a success message
        flash(__('auth.registered', ['name' => $user->name]))->success();
        return redirect()->route("lobby");
    }
}