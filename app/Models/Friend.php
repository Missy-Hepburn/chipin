<?php
/**
 * Created by PhpStorm.
 * User: morty
 * Date: 14.07.16
 * Time: 11:39
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model{

    protected $fillable = [
        'profile_id', 'friend_profile_id'
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function friend_profile()
    {
        return $this->belongsTo(Profile::class);
    }

}