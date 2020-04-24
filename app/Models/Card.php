<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $table = "cards";
    protected $guarded = ["id", "created_at", "updated_at"];
    protected $fillable = [
        "type",
        "text",
        "description",
        "action",
        "open_positions",
        "has_crystal",
        "crystal_location",
        "has_ladder",
        "ladder_location",
        "has_door",
        "door_color",
        "door_position",
        "image_url",
    ];

    //
    // Accessors & mutators
    //

    public function getOpenPositionsAttribute($value)
    {
        return unserialize($value);
    }

    public function setOpenPositionsAttribute($value)
    {
        $this->attributes["open_positions"] = serialize($value);
    }
}