@extends("layouts.lobby")

@section("title")
    - Lobby
@stop

@section("content")
    <div id="lobby">

        <!-- Header -->
        <div id="lobby-header">
            <h1 id="lobby-header__title">Highscores</h1>
        </div>

        <!-- Content -->
        <div id="lobby-content">
            <div id="lobby-content__left">

                <!-- Leaderboards -->
                <div id="leaderboards-wrapper">
                    <leaderboards :users="{{ $users->toJson() }}"></leaderboards>
                </div>

            </div>
        </div>

    </div>
@stop