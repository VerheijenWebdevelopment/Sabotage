<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Round extends Model
{
    protected $table = "rounds";
    protected $guarded = ["id", "created_at", "updated_at"];
    protected $fillable = [
        "game_id",
        "round_number",
        "turn_number",
        "phase",
        "players_turn",
        "deck",
        "num_cards_in_deck",
        "role_deck",
        "num_cards_in_role_deck",
        "available_roles",
        "players_with_selected_roles",
        "board",
        "gold_location",
        "reached_gold_locations",
        "winning_teams",
        "revealed_players",
        "current",
    ];
    protected $hidden = [
        "deck",
        "role_deck",
        "gold_location",
    ];
    protected $casts = [
        "current" => "boolean",
    ];

    //
    // Relationships
    //

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    //
    // Accessors
    //

    public function getDeckAttribute($value)
    {
        return json_decode(unserialize($value));
    }

    public function getRoleDeckAttribute($value)
    {
        return json_decode(unserialize($value));
    }

    public function getAvailableRolesAttribute($value)
    {
        return json_decode(unserialize($value));
    }

    public function getPlayersWithSelectedRolesAttribute($value)
    {
        return json_decode(unserialize($value));
    }

    public function getBoardAttribute($value)
    {
        return json_decode(unserialize($value));
    }

    public function getReachedGoldLocationsAttribute($value)
    {
        return json_decode(unserialize($value));
    }

    public function getWinningTeamsAttribute($value)
    {
        return json_decode(unserialize($value));
    }

    public function getRevealedPlayersAttribute($value)
    {
        return json_decode(unserialize($value));
    }

    public function getRewardsAttribute($value)
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

    public function setRoleDeckAttribute($value)
    {
        $this->attributes["role_deck"] = serialize(json_encode($value));
    }

    public function setAvailableRolesAttribute($value)
    {
        $this->attributes["available_roles"] = serialize(json_encode($value));
    }

    public function setPlayersWithSelectedRolesAttribute($value)
    {
        $this->attributes["players_with_selected_roles"] = serialize(json_encode($value));
    }

    public function setBoardAttribute($value)
    {
        $this->attributes["board"] = serialize(json_encode($value));
    }

    public function setReachedGoldLocationsAttribute($value)
    {
        $this->attributes["reached_gold_locations"] = serialize(json_encode($value));
    }

    public function setWinningTeamsAttribute($value)
    {
        $this->attributes["winning_teams"] = serialize(json_encode($value));
    }

    public function setRevealedPlayersAttribute($value)
    {
        $this->attributes["revealed_players"] = serialize(json_encode($value));
    }

    public function setRewardsAttribute($value)
    {
        $this->attributes["rewards"] = serialize(json_encode($value));
    }
}