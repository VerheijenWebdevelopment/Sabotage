<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $table = "players";
    protected $guarded = ["id", "created_at", "updated_at"];
    protected $fillable = [
        "user_id",
        "game_id",
        "role_id",
        "player_number",
        "score",
        "hand",
        "cart_available",
        "light_available",
        "pickaxe_available",
    ];
    protected $hidden = [
        "role",
        "hand",
    ];
    protected $casts = [
        "cart_available",
        "light_available",
        "pickaxe_available",
    ];

    //
    // Relationships
    //

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function messages()
    {
        return $this->hasMany(GameChatMessage::class);
    }

    public function wonGames()
    {
        return $this->belongsToMany(Game::class, "winners", "player_id", "game_id");
    }

    //
    // Accessors 
    //

    public function getHasSelectedRoleAttribute()
    {
        return !is_null($this->role_id);
    }

    public function getHandAttribute($value)
    {
        return unserialize($value);
    }

    //
    // Mutators
    //

    public function setHandAttribute($value)
    {
        $this->attributes["hand"] = serialize($value);
    }
}