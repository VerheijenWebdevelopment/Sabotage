@extends("layouts.game")

@section("title")
    - Ingame
@stop

@section("content")

    <game
        :game="{{ $game->toJson() }}"
        :player="{{ $player->toJson() }}"
        :player-role="{{ $playerRole ? $playerRole->toJson() : json_encode($playerRole) }}"
        :hand="{{ json_encode($player->hand) }}"
        :roles="{{ $roles->toJson() }}"
        :cards="{{ $cards->toJson() }}"
        send-message-api-endpoint="{{ route('api.games.send-message.post') }}"
        perform-action-api-endpoint="{{ route('api.games.perform-action.post') }}"
        cart-icon-url="{{ asset('storage/images/icons/minecart.svg') }}"
        light-icon-url="{{ asset('storage/images/icons/lantern.svg') }}"
        pickaxe-icon-url="{{ asset('storage/images/icons/pickaxe.svg') }}"
        gold-icon-url="{{ asset('storage/images/icons/gold.svg') }}">
    </game>

@stop