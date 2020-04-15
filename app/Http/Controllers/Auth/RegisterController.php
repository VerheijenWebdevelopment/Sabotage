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
            "avatars" => collect([
                "storage/images/users/avatars/default.png",
                "storage/images/users/avatars/default_2.png",
                "storage/images/users/avatars/default_3.png",
                "storage/images/users/avatars/default_4.png",
                "storage/images/users/avatars/default_5.png",
                "storage/images/users/avatars/default_6.png",
                "storage/images/users/avatars/default_7.png",
                "storage/images/users/avatars/default_8.png",
                "storage/images/users/avatars/default_9.png",
                "storage/images/users/avatars/default_10.png",
                "storage/images/users/avatars/default_11.png",
                "storage/images/users/avatars/default_12.png",
                "storage/images/users/avatars/default_13.png",
                "storage/images/users/avatars/default_14.png",
            ]),
            "strings" => collect([
                "title" => __("auth.register_title"),
                "username" => __("auth.register_username"),
                "email" => __("auth.register_email"),
                "password" => __("auth.register_password"),
                "confirm_password" => __("auth.register_confirm_password"),
                "avatar" => __("auth.register_avatar"),
                "submit" => __("auth.register_submit"),
                "login" => __("auth.register_go_to_login"),
            ]),
            "oldInput" => collect([
                "username" => old("username"),
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
        flash(__('auth.registered', ['name' => $user->username]))->success();
        return redirect()->route("lobby");
    }
}