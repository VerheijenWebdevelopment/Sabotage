<?php

namespace App\Http\Controllers\Auth;

use Users;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RecoverAccountRequest;

class RecoverAccountController extends Controller
{
    public function getRecoverAccount()
    {
        // Render the recover password page
        return view("pages.auth.recover");
    }

    public function postRecoverAccount(RecoverAccountRequest $request)
    {
        // Send recover account email to the user
        Users::sendRecoverEmail($request);
    
        // Render the recover mail sent page
        return view("pages.auth.recover-mail-sent", [
            "email" => $request->email,
        ]);
    }
}