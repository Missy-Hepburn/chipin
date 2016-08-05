<?php

namespace App\Models;

use Illuminate\Http\UploadedFile;

trait ImageTrait {
    public function image() {
        return $this->belongsTo($this->imageClass);
    }

    public function setImage(UploadedFile $image) {
        $this->deleteImage()
            ->image()
            ->associate(call_user_func([$this->imageClass, 'createFromRequest'], $image));

        return $this;
    }

    public function deleteImage() {
        if ($this->image) {

            $model = call_user_func([$this->imageClass, 'find'], $this->image->id);
            if ($model) {
                $model->delete();
            }

            $this->image()->dissociate();
        }

        return $this;
    }


}