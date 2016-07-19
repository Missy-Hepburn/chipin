<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Profile extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'nationality',
        'country', 'birthday', 'photo_id',
        'address', 'occupation', 'income'
    ];

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

    public function setPhoto(UploadedFile $photo) {
        $this->deletePhoto();

        $this->photo()->associate(File::createFromRequest($photo));

        return $this;
    }

    public function deletePhoto() {
        if ($this->photo) {
            $model = File::find($this->photo->id);
            if ($model) {
                $this->photo()->dissociate($model);
                $model->delete();
            }
        }

        return $this;
    }
}
