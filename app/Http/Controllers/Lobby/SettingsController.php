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
            ]),
            "strings" => collect([
                "title" => __("settings.update_profile_title"),
                "username" => __("settings.update_profile_username"),
                "email" => __("settings.update_profile_email"),
                "avatar" => __("settings.update_profile_avatar"),
                "cancel" => __("settings.update_profile_cancel"),
                "submit" => __("settings.update_profile_submit"),
            ]),
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