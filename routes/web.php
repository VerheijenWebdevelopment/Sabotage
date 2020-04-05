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

    Route::group(["prefix" => "lobby", "middleware" => "idle"], function() {
        Route::get("/", "Lobby\LobbyController@getLobby")->name("lobby");
        Route::get("leaderboards", "Lobby\LobbyController@getLeaderboards")->name("lobby.leaderboards");
        Route::group(["prefix" => "settings"], function() {
            Route::get("/", "Lobby\SettingsController@getOverview")->name("settings");
            Route::get("profile", "Lobby\SettingsController@getProfile")->name("settings.profile");
            Route::get("update-profile", "Lobby\SettingsController@getUpdateProfile")->name("settings.update-profile");
            Route::post("update-profile", "Lobby\SettingsController@postUpdateProfile")->name("settings.update-profile.post");
            Route::get("change-password", "Lobby\SettingsController@getChangePassword")->name("settings.change-password");
            Route::post("change-password", "Lobby\SettingsController@postChangePassword")->name("settings.change-password.post");
        });
    });
    
    Route::group(["prefix" => "game", "middleware" => "playing"], function() {
        Route::get("/", "Game\GameController@getGame")->name("game");
    });

});

Route::group(["prefix" => "api"], function() {

    Route::post("send-message", "Api\ChatController@postSendMessage")->name("api.chat.send-message.post");
    
    Route::group(["prefix" => "games"], function() {
        Route::post("create", "Api\GameController@postCreateGame")->name("api.games.create.post");
        Route::post("delete", "Api\GameController@postDeleteGame")->name("api.games.delete.post");
        Route::post("join", "Api\GameController@postJoinGame")->name("api.games.join.post");
        Route::post("leave", "Api\GameController@postLeaveGame")->name("api.games.leave.post");
        Route::post("start", "Api\GameController@postStartGame")->name("api.games.start.post");
        Route::post("perform-action", "Api\GameController@postPerformAction")->name("api.games.perform-action.post");
        Route::post("send-message", "Api\GameController@postSendMessage")->name("api.games.send-message.post");
    });

    Route::group(["prefix" => "cards"], function() {
        Route::get("generate-image/{id}", "Api\CardController@getGenerateImage")->name("api.cards.generate-image");
    });

});

Route::group(["prefix" => "test"], function() {

    Route::get("card-generation", "TestController@getTestCardGeneration")->name("test.card-generation");
    Route::get("array-manip", "TestController@getTestArrayManip")->name("test.array-manip");

});