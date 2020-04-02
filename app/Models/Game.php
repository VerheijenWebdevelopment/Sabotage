<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $table = "games";
    protected $guarded = ["id", "created_at", "updated_at"];
    protected $fillable = [
        "game_master_id",
        "status",
        "turn",
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
}