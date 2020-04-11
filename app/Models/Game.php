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
        "turn",
        "phase",
        "player_turn",
        "gold_location",
        "deck",
        "num_cards_in_deck",
        "roles",
        "available_roles",
        "players_with_selected_roles",
        "board",
        "reached_gold_locations",
        "reward_deck",
        "num_cards_reward_deck",
        "revealed_players",
        "saboteur_reward",
        "winning_team",
    ];
    protected $hidden = [
        "deck",
        "roles",
        "gold_location",
        "reward_deck",
        "saboteur_reward",
    ];

    //
    // Relationships
    //

    public function winners()
    {
        return $this->belongsToMany(Player::class, "winners", "game_id", "player_id");
    }

    public function gameMaster()
    {
        return $this->belongsTo(User::class, "game_master_id", "id");
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function messages()
    {
        return $this->hasMany(GameChatMessage::class);
    }

    //
    // Accessors
    //

    public function getDeckAttribute($value)
    {
        return unserialize($value);
    }

    public function getRolesAttribute($value)
    {
        return unserialize($value);
    }

    public function getAvailableRolesAttribute($value)
    {
        return unserialize($value);
    }

    public function getNumberOfAvailableRolesAttribute()
    {
        return !is_null($this->roles) && $this->roles ? count($this->roles) : 0;
    }

    public function getPlayersWithSelectedRolesAttribute($value)
    {
        return unserialize($value);
    }

    public function getBoardAttribute($value)
    {
        return unserialize($value);
    }

    public function getReachedGoldLocationsAttribute($value)
    {
        return unserialize($value);
    }

    public function getRewardDeckAttribute($value)
    {
        return unserialize($value);
    }

    public function getRevealedPlayersAttribute($value)
    {
        return unserialize($value);
    }
    
    //
    // Mutators
    //

    public function setDeckAttribute($value)
    {
        $this->attributes["deck"] = serialize($value);
    }

    public function setRolesAttribute($value)
    {
        $this->attributes["roles"] = serialize($value);
    }

    public function setAvailableRolesAttribute($value)
    {
        $this->attributes["available_roles"] = serialize($value);
    }

    public function setPlayersWithSelectedRolesAttribute($value)
    {
        $this->attributes["players_with_selected_roles"] = serialize($value);
    }

    public function setBoardAttribute($value)
    {
        $this->attributes["board"] = serialize($value);
    }

    public function setReachedGoldLocationsAttribute($value)
    {
        $this->attributes["reached_gold_locations"] = serialize($value);
    }

    public function setRewardDeckAttribute($value)
    {
        $this->attributes["reward_deck"] = serialize($value);
    }

    public function setRevealedPlayersAttribute($value)
    {
        $this->attributes["revealed_players"] = serialize($value);
    }
}