@extends("layouts.lobby")

@section("title")
    - Lobby
@stop

@section("content")

    <!-- Header -->
    <div id="page-header">
        <h1 id="page-header__title">
            @lang("settings.profile_title")
        </h1>
    </div>

    <!-- Profile -->
    <div id="profile">

        <!-- Avatar -->
        <div id="profile-avatar" class="elevation-1" style="background-image: url({{ asset($user->avatar_url) }})"></div>

        <!-- Details -->
        <div id="profile-details" class="details elevation-1">
            <!-- Username --> 
            <div class="detail">
                <div class="key">@lang("settings.profile_username")</div>
                <div class="val">{{ $user->username }}</div>
            </div>
            <!-- Email -->
            <div class="detail">
                <div class="key">@lang("settings.profile_email")</div>
                <div class="val">{{ $user->email }}</div>
            </div>
            <!-- Highscore -->
            <div class="detail">
                <div class="key">@lang("settings.profile_highscore")</div>
                <div class="val">{{ $user->highscore }} @lang("settings.profile_gold_collected")</div>
            </div>
            <!-- Games played -->
            <div class="detail">
                <div class="key">@lang("settings.profile_games_played")</div>
                <div class="val">{{ $user->games_played }} {{ $user->games_played == 1 ? __("settings.profile_game") : __("settings.profile_games") }} @lang("settings.profile_played")</div>
            </div>
            <!-- Created at -->
            <div class="detail">
                <div class="key">@lang("settings.profile_created_at")</div>
                <div class="val">{{ $user->created_at->format("d-m-Y") }}</div>
            </div>
        </div>

        <!-- Actions -->
        <div id="profile-actions">
            <v-btn color="warning" href="{{ route('settings.update-profile') }}">
                <i class="far fa-edit"></i>
                @lang("settings.profile_edit")
            </v-btn>
            <v-btn color="warning" href="{{ route('settings.change-password') }}">
                <i class="fas fa-key"></i>
                @lang("settings.profile_change_password")
            </v-btn>
        </div>

    </div>

@stop