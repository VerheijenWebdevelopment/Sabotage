<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get("/", "LandingController@getLanding")->name("landing");

Route::group(["middleware" => "guest"], function() {

    Route::get("login", "Auth\LoginController@getLogin")->name("login");
    Route::post("login", "Auth\LoginController@postLogin")->name("login.post");

    Route::get("register", "Auth\RegisterController@getRegister")->name("register");
    Route::post("register", "Auth\RegisterController@postRegister")->name("register.post");

    Route::get("recover-account", "Auth\RecoverAccountController@getRecoverAccount")->name("recover-account");
    Route::post("recover-account", "Auth\RecoverAccountController@postRecoverAccount")->name("recover-account.post");

    Route::get("reset-password/{email}/{code}", "Auth\ResetPasswordController@getResetPassword")->name("reset-password");
    Route::post("reset-password/{email}/{code}", "Auth\ResetPasswordController@postResetPassword")->name("reset-password.post");

});

Route::group(["middleware" => "auth"], function() {

    Route::get("logout", "Auth\LogoutController@getLogout")->name("logout");

    Route::get("lobby", "Game\LobbyController@getLobby")->name("lobby");

});