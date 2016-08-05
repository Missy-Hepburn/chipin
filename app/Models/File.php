<?php
/**
 * Created by PhpStorm.
 * User: morty
 * Date: 12.07.16
 * Time: 12:54
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use \Illuminate\Support\Facades\File as FileFacade;

class File extends Model {

    protected $fillable = [
        'name'
    ];

    protected $hidden = [
        'path'
    ];

    protected static function boot() {
        parent::boot();

        static::deleting(function($file){
            Storage::disk('public')->delete($file->path);
        });
    }

    public function getPath() {
        return Storage::disk('public')->url($this->path);
    }

    public static function generatePath() {
        return date('y.m.d');
    }

    public static function generateRandomName($length = 10){
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }

    public static function createFromUrl($url){
        $path = static::generatePath() . '/' . self::generateRandomName();
        Storage::disk('public')->put($path,  file_get_contents($url));

        $entry = new File();
        $entry->name = array_pop(explode('/', $url));
        $entry->path = $path;
        $entry->save();

        return $entry;
    }

    public static function createFromRequest(UploadedFile $file) {
        $path = static::generatePath() . '/'
            . $file->getFilename() . '.' . $file->getClientOriginalExtension();

        Storage::disk('public')->put($path,  FileFacade::get($file));

        $entry = new File();
        $entry->name = $file->getClientOriginalName();
        $entry->path = $path;
        $entry->save();

        return $entry;
    }

    public function toArray() {
        return $this->getPath();
    }

}