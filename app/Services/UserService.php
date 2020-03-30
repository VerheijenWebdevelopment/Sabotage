<?php

namespace App\Services;

/*
|--------------------------------------------------------------------------
| User Service
|--------------------------------------------------------------------------
*/

use Str;
use App\Models\User;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\RecoverAccountRequest;
use App\Http\Jobs\Auth\SendRecoverAccountEmail;

class UserService
{
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
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password),
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
}