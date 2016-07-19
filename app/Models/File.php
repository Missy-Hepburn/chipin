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

class File extends Model{

    protected $fillable = [
        'name'
    ];

    protected $hidden = [
        'path'
    ];

    public function getPath() {
        return Storage::disk('public')->url($this->path);
    }

    public static function getStoragePath(){
        $dirPath = storage_path().'/app/public/'.date('d.m.y');
        if(!file_exists($dirPath))
            mkdir($dirPath);
        return $dirPath;
    }

    public static function genearateRandomName($length=10){
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }

    public static function createFromUrl($url){

        $pieces = explode('/', $url);
        $fName = array_pop($pieces);

        $path = date('d.m.y').'/'.self::genearateRandomName();
        Storage::disk('public')->put($path,  file_get_contents($url));
        $entry = new self();
        $entry->name = $fName;
        $entry->path = $path;
        $entry->save();

        return $entry;
    }

    public static function createFromRequest(UploadedFile $file) {
        $path = date('d.m.y') . '/' . $file->getFilename();

        Storage::disk('public')->put($path,  FileFacade::get($file));

        $entry = new File();
        $entry->name = $file->getClientOriginalName();
        $entry->path = $path;
        $entry->save();

        return $entry;
    }
}