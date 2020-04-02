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
            <!-- ID -->
            <div class="detail">
                <div class="key">@lang("settings.profile_id")</div>
                <div class="val">#{{ $user->id }}</div>
            </div>
            <!-- Username --> 
            <div class="detail">
                <div class="key">@lang("settings.profile_username")</div>
                <div class="val">{{ $user->username }}</div>
            </div>
            <!-- Name --> 
            <div class="detail">
                <div class="key">@lang("settings.profile_name")</div>
                <div class="val">{{ $user->name }}</div>
            </div>
            <!-- Email -->
            <div class="detail">
                <div class="key">@lang("settings.profile_email")</div>
                <div class="val">{{ $user->email }}</div>
            </div>
            <!-- Highscore -->
            <div class="detail">
                <div class="key">@lang("settings.profile_highscore")</div>
                <div class="val">{{ $user->highscore }} gold collected</div>
            </div>
            <!-- Games played -->
            <div class="detail">
                <div class="key">@lang("settings.profile_games_played")</div>
                <div class="val">{{ $user->games_played }} {{ $user->games_played == 1 ? 'game' : 'games' }} played</div>
            </div>
            <!-- Created at -->
            <div class="detail">
                <div class="key">@lang("settings.profile_created_at")</div>
                <div class="val">{{ $user->created_at->format("d-m-Y") }}</div>
            </div>
        </div>

        <!-- Actions -->
        <div id="profile-actions">
            <v-btn depressed color="warning" href="{{ route('settings.update-profile') }}">
                @lang("settings.profile_edit")
            </v-btn>
        </div>

    </div>

@stop