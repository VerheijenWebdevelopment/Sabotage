<?php

namespace App\Services;

use Str;
use Hash;
use Uploader;
use Exception;

use App\Models\User;

use App\Traits\ModelServiceGetters;
use App\Contracts\ModelServiceContract;

use App\Jobs\Auth\SendRecoverAccountEmail;

use App\Events\User\UserUpdatedProfile;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\RecoverAccountRequest;
use App\Http\Requests\Settings\UpdateProfileRequest;
use App\Http\Requests\Settings\ChangePasswordRequest;

/*
|--------------------------------------------------------------------------
| User Service
|--------------------------------------------------------------------------
*/

class UserService implements ModelServiceContract
{
    use ModelServiceGetters;

    private $model;
    private $records;
    private $preloadedRecords;

    public function __construct()
    {
        $this->model =  "App\Models\User";
    }

    public function preload($instance)
    {
        if (!is_null($instance->avatar_url)) $instance->avatar_url = asset($instance->avatar_url);

        return $instance;
    }

    public function findByEmail($email)
    {
        return User::where("email", $email)->first();
    }

    public function sendRecoverEmail(RecoverAccountRequest $request)
    {
        $user = $this->findByEmail($request->email);

        if ($user)
        {
            $user->recovery_code = Str::uuid();
            $user->save();

            SendRecoverAccountEmail::dispatch($user)->onQueue("emails");
        }
    }

    public function createFromRegisterRequest(RegisterRequest $request)
    {
        return User::create([
            "username" => $request->username,
            "email" => $request->email,
            "password" => bcrypt($request->password),
            "avatar_url" => $request->avatar,
        ]);
    }

    public function emailExists($email)
    {
        return $this->findByEmail($email) ? true : false;
    }

    public function recoveryCodeIsValid($email, $code)
    {
        $user = $this->findByEmail($email);

        if ($user and $user->recovery_code == $code)
        {
            return true;
        }

        return false;
    }
    
    public function resetPassword(ResetPasswordRequest $request, User $user)
    {
        $user->password = bcrypt($request->password);
        $user->save();

        return $user;
    }

    public function updateProfileFromRequest(UpdateProfileRequest $request)
    {
        // Grab logged in user
        $user = auth()->user();
        
        // Update the fields that are always present
        $user->username = $request->username;
        $user->email = $request->email;
        $user->avatar_url = $request->avatar;

        // Save changes to the user
        $user->save();

        // Broadcast event
        broadcast(new UserUpdatedProfile($user))->toOthers();

        // Return the updated user
        return $user;
    }

    public function changePasswordFromRequest(ChangePasswordRequest $request)
    {
        // Grab the currently logged in user
        $user = auth()->user();

        // Throw an exception if the current password is invalid
        if (!Hash::check($request->password, $user->password)) throw new Exception(__("settings.password_invalid"));

        // Hash & save the new password on the user
        $user->password = bcrypt($request->new_password);
        $user->save();
    }

    public function getCurrent()
    {
        return $this->preload(auth()->user());
    }
}