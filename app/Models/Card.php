<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $table = "cards";
    protected $guarded = ["id", "created_at", "updated_at"];
    protected $fillable = [
        "type",
        "action",
        "open_positions",
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