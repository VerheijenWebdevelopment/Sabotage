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
        "available_roles",
        "role_deck",
        "players_with_selected_roles",
        "board",
        "gold_location",
        "reached_gold_locations",
        "winning_teams",
        "revealed_players",
        "rewards",
    ];
    protected $hidden = [
        "deck",
        "role_deck",
        "gold_location",
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
        $this->attributes["deck"] = unserialize($value);
    }

    public function getAvailableRolesAttribute($value)
    {
        $this->attributes["available_roles"] = unserialize($value);
    }

    public function getRoleDeckAttribute($value)
    {
        $this->attributes["role_deck"] = unserialize($value);
    }

    public function getPlayersWithSelectedRolesAttribute($value)
    {
        $this->attributes["players_with_selected_roles"] = unserialize($value);
    }

    public function getBoardAttribute($value)
    {
        $this->attributes["board"] = unserialize($value);
    }

    public function getReachedGoldLocationsAttribute($value)
    {
        $this->attributes["reached_gold_locations"] = unserialize($value);
    }

    public function getWinningTeamsAttribute($value)
    {
        $this->attributes["winning_teams"] = unserialize($value);
    }

    public function getRevealedPlayersAttribute($value)
    {
        $this->attributes["revealed_players"] = unserialize($value);
    }

    public function getRewardsAttribute($value)
    {
        $this->attributes["rewards"] = unserialize($value);
    }

    //
    // Mutators
    //

    public function setDeckAttribute($value)
    {
        $this->attributes["deck"] = serialize($value);
    }

    public function setAvailableRolesAttribute($value)
    {
        $this->attributes["available_roles"] = serialize($value);
    }

    public function setRoleDeckAttribute($value)
    {
        $this->attributes["role_deck"] = serialize($value);
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

    public function setWinningTeamsAttribute($value)
    {
        $this->attributes["winning_teams"] = serialize($value);
    }

    public function setRevealedPlayersAttribute($value)
    {
        $this->attributes["revealed_players"] = serialize($value);
    }

    public function setRewardsAttribute($value)
    {
        $this->attributes["rewards"] = serialize($value);
    }
}