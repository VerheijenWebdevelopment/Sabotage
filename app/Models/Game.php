<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Game Model
 * 
 * Statuses are hardcoded; possibilities:
 *  - open
 *  - ongoing
 *  - completed
 */

class Game extends Model
{
    protected $table = "games";
    protected $guarded = ["id", "created_at", "updated_at"];
    protected $fillable = [
        "game_master_id",
        "status",
        "round",
        "player_turn_index",
        "gold_location_index",
        "board",
        "deck",
    ];

    //
    // Relationships
    //

    public function gameMaster()
    {
        return $this->belongsTo(User::class, "game_master_id", "id");
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    //
    // Accessors
    //

    public function getBoardAttribute($value)
    {
        return json_decode(unserialize($value));
    }

    public function getDeckAttribute($value)
    {
        return json_decode(unserialize($value));
    }

    //
    // Mutators
    //

    public function setDeckAttribute($value)
    {
        $this->attributes["deck"] = serialize(json_encode($value));
    }

    public function setBoardAttribute($value)
    {
        $this->attributes["board"] = serialize(json_encode($value));
    }
}