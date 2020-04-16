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
        "current_round_id",
        "status",
        "settings",
    ];

    //
    // Relationships
    //

    public function gameMaster()
    {
        return $this->belongsTo(User::class, "game_master_id", "id");
    }
    
    public function rounds()
    {
        return $this->hasMany(Round::class);
    }

    public function currentRound()
    {
        return $this->belongsTo(Round::class, "current_round_id", "id");
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function messages()
    {
        return $this->hasMany(GameChatMessage::class);
    }

    public function winners()
    {
        return $this->belongsToMany(Player::class, "winners", "game_id", "player_id");
    }

    //
    // Accessors
    //
    
    public function getSettingsAttribute($value)
    {
        return json_decode(unserialize($value));
    }
    
    //
    // Mutators
    //

    public function setSettingsAttribute($value)
    {
        $this->attributes["settings"] = serialize(json_encode($value));
    }
}