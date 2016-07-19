<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Profile;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'fb_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'fb_token', 'profile_id'
    ];

    public function profile(){
        return $this->hasOne(Profile::class);
    }
}
