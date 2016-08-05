<?php

namespace App\Models;

use App\Models\Images\ProfileImage;
use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Support\Facades\Auth;

class Profile extends Model
{
    use ImageTrait;

    protected $fillable = [
        'first_name', 'last_name', 'nationality',
        'country', 'birthday', 'image_id',
        'address', 'occupation', 'income'
    ];

    protected $hidden = ['user_id', 'image_id', 'user'];
    protected $imageClass = ProfileImage::class;

    protected $with = ['image'];

    public function name() {
        $names = [];
        if (!empty($this->first_name)) $names[] = $this->first_name;
        if (!empty($this->last_name)) $names[] = $this->last_name;

        return implode(' ', $names);
    }

    public function getNameAttribute() {
        return $this->name();
    }

    public function setCountryAttribute($value) {
        $this->attributes['country'] = strtoupper($value);
    }
    public function setNationalityAttribute($value) {
        $this->attributes['nationality'] = strtoupper($value);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function toArray() {
        if (Auth::user() && $this->user->id == Auth::user()->id) {
            return parent::toArray();
        }

        $this->setVisible(['first_name','last_name','image']);
        return parent::toArray();
    }
}
