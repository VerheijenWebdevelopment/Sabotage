<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameChatMessage extends Model
{
    protected $table = "game_chat_messages";
    protected $guarded = ["id", "created_at", "updated_at"];
    protected $fillable = [
        "game_id",
        "player_id",
        "message",
    ];

    //
    // Relationships
    //

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}