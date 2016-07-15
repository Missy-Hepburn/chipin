<?php
/**
 * Created by PhpStorm.
 * User: morty
 * Date: 12.07.16
 * Time: 14:09
 */

namespace App\Api\V1\Controllers;

use App\Api\V1\Controllers\BaseController as Controller;
use App\Api\V1\Transformers\FileTransformer;
use App\Models\File as FileEntry;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Input::hasFile('file'))
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException('"file" field is empty');

        if(!Input::file('file')->isValid())
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException('File is invalid');

        $file = \Request::file('file');
        $path = date('d.m.y').'/'.$file->getFilename();
        Storage::disk('public')->put($path,  \File::get($file));
        $entry = new FileEntry();
        $entry->name = $file->getClientOriginalName();
        $entry->path = $path;
        $entry->save();

        return $this->item($entry, new FileTransformer());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return FileEntry::findOrFail($id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = FileEntry::findOrFail($id);
        return $model->delete();
    }

}