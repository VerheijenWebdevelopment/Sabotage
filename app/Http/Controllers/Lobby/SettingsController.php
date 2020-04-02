<?php

namespace App\Http\Controllers\Lobby;

use Users;
use Exception;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateProfileRequest;
use App\Http\Requests\Settings\ChangePasswordRequest;

class SettingsController extends Controller
{
    public function getOverview()
    {
        return view("pages.lobby.settings");
    }
    
    public function getProfile()
    {
        return view("pages.lobby.profile", [
            "user" => auth()->user(),
        ]);
    }

    public function getUpdateProfile()
    {
        return view("pages.lobby.update-profile", [
            "user" => Users::getCurrent(),
            "oldInput" => collect([
                "username" => old("username"),
                "name" => old("name"),
                "email" => old("email"),
            ])
        ]);
    }
    
    public function postUpdateProfile(UpdateProfileRequest $request)
    {
        // Update the user's profile
        Users::updateProfileFromRequest($request);

        // Redirect the user to their profile
        flash(__("settings.updated_profile"))->success();
        return redirect()->route("settings.profile");
    }

    public function getChangePassword()
    {
        return view("pages.lobby.change-password");
    }

    public function postChangePassword(ChangePasswordRequest $request)
    {
        try
        {
            // Change the user's password
            Users::changePasswordFromRequest($request);

            // Flash success message and redirect back to settings
            flash(__("settings.password_changed"))->success();
            return redirect()->route("settings");
        }
        catch (Exception $e)
        {   
            flash($e->getMessage())->error();
            return redirect()->back();
        }
    }
}