<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Profile extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'nationality',
        'country', 'birthday',
        'address', 'occupation', 'income'
    ];

    protected $hidden = ['photo_id'];

    public function name() {
        $names = [];
        if (!empty($this->first_name)) $names[] = $this->first_name;
        if (!empty($this->last_name)) $names[] = $this->last_name;

        return implode(' ', $names);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photo(){
        return $this->belongsTo(File::class);
    }

    public function friends(){
        return $this->hasMany(Friend::class);
    }
}
