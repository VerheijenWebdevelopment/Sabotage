@extends("layouts.lobby")

@section("title")
    - Lobby
@stop

@section("content")
    <div id="lobby">

        <!-- Header -->
        <div id="lobby-header">
            <h1 id="lobby-header__title">@lang("lobby.title")</h1>
        </div>

        <!-- Feedback -->
        @include("partials.feedback")

        <!-- Content -->
        <div id="lobby-content">
            <div id="lobby-content__left">
                
                <!-- Open & ongoing games -->
                <game-overview
                    :user="{{ $user->toJson() }}"
                    :games="{{ $games->toJson() }}"
                    game-href="{{ route('game') }}"
                    create-api-endpoint="{{ route('api.lobby.create.post') }}"
                    delete-api-endpoint="{{ route('api.lobby.delete.post') }}"
                    join-api-endpoint="{{ route('api.lobby.join.post') }}"
                    leave-api-endpoint="{{ route('api.lobby.leave.post') }}"
                    start-api-endpoint="{{ route('api.lobby.start.post') }}"
                    update-settings-api-endpoint="{{ route('api.lobby.update-settings.post') }}"
                    :strings="{{ $strings->toJson() }}">
                </game-overview>

            </div>
            <div id="lobby-content__right">

                <!-- Online users -->
                <chat-online-users
                    title-text="@lang('lobby.online_users')"
                    no-records-text="@lang('lobby.no_online_users')">
                </chat-online-users>

            </div>
        </div>

    </div>
@stop