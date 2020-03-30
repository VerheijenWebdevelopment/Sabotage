@extends("layouts.game")

@section("title")
    - Lobby
@stop

@section("content")
    <div id="lobby">

        <!-- Header -->
        <div id="lobby-header">
            <h1 id="lobby-header__title">Lobby</h1>
        </div>

        <!-- Content -->
        <div id="lobby-content">
            <div id="lobby-content__left">
                
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