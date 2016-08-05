<?php

namespace App\Models\Images;

use App\Models\File;

class GoalImage extends File {
    protected $table = 'files';

    public static function generatePath() {
        return 'goals' . '/' . date('y.m.d');
    }
}