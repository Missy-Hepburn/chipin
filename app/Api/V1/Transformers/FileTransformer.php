<?php
/**
 * Created by PhpStorm.
 * User: morty
 * Date: 13.07.16
 * Time: 16:59
 */

namespace App\Api\V1\Transformers;

use Illuminate\Support\Facades\Storage;
use League\Fractal\TransformerAbstract;
use App\Models\File;

class FileTransformer extends TransformerAbstract{

    public function transform(File $file)
    {
        return [
            'id' => $file->id,
            'name' => $file->name,
            'url' => Storage::url($file->path),
            'updated_at' => $file->updated_at,
            'created_at' => $file->created_at,
        ];

    }

}