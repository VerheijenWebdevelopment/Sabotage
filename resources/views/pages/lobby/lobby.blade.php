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
                    create-api-endpoint="{{ route('api.games.create.post') }}"
                    delete-api-endpoint="{{ route('api.games.delete.post') }}"
                    join-api-endpoint="{{ route('api.games.join.post') }}"
                    leave-api-endpoint="{{ route('api.games.leave.post') }}"
                    start-api-endpoint="{{ route('api.games.start.post') }}">
                </game-overview>

                <!-- Chat messages -->
                <chat-messages
                    send-message-api-endpoint="{{ route('api.chat.send-message.post') }}">
                </chat-messages>

            </div>
            <div id="lobby-content__right">

                <!-- Online users -->
                <chat-online-users></chat-online-users>

            </div>
        </div>

    </div>
@stop