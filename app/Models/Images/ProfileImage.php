<?php

namespace App\Models\Images;

use App\Models\File;

class ProfileImage extends File {
    protected $table = 'files';

    public static function generatePath() {
        return 'profile' . '/' . date('y.m.d');
    }
}