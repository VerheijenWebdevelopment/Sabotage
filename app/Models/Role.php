<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = "roles";
    protected $guarded = ["id", "created_at", "updated_at"];
    protected $fillable = [
        "name",
        "label",
        "description",
        "color",
    ];

    //
    // Relationships
    //

    public function players()
    {
        return $this->hasMany(Player::class);
    }
}