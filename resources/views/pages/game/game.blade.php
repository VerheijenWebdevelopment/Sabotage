@extends("layouts.game")

@section("title")
    - Ingame
@stop

@section("content")

    <game
        :game="{{ $game->toJson() }}"
        :round="{{ $round->toJson() }}"
        :player="{{ $player->toJson() }}"
        :player-role="{{ $playerRole ? $playerRole->toJson() : json_encode($playerRole) }}"
        :hand="{{ json_encode($player->hand) }}"
        :roles="{{ $roles->toJson() }}"
        :cards="{{ $cards->toJson() }}"
        :icons="{{ $icons->toJson() }}"
        :api-endpoints="{{ $apiEndpoints->toJson() }}"
        lobby-href="{{ route('lobby') }}">
    </game>

@stop