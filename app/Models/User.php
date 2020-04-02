<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Sluggable;
    use Notifiable;

    protected $fillable = [
        'slug',
        'name',
        'email',
        'password',
        'username',
        'recovery_code',
        'is_admin',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'is_admin' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    //
    // Slug
    //
    
    public function sluggable()
    {
        return ['slug' => ['source' => 'username']];
    }

    //
    // Relationships
    //

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function startedGames()
    {
        return $this->hasMany(Game::class, "id", "game_master_id");
    }
}
