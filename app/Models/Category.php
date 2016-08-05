<?php

namespace App\Models;

use App\Models\Goal;
use App\Models\Images\CategoryImage;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use ImageTrait;

    protected $fillable = [
        'name', 'image_id'
    ];

    protected $with = [
        'image'
    ];

    protected $hidden = [
      'image_id', 'created_at'
    ];

    protected $imageClass = CategoryImage::class;

    protected static function boot() {
        parent::boot();

        static::deleting(function (Category $category) {
            return $category->countGoals() == 0;
        });
    }

    public function goals() {
        return $this->hasMany(Goal::class);
    }

    public function countGoals() {
        return $this->goals()->count();
    }
}
