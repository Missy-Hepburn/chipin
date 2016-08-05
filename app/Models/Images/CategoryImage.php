<?php

namespace App\Models\Images;

use App\Models\File;

class CategoryImage extends File {
    protected $table = 'files';

    public static function generatePath() {
        return 'category';
    }
}