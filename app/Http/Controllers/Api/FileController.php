<?php
/**
 * Created by PhpStorm.
 * User: morty
 * Date: 12.07.16
 * Time: 14:09
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as Controller;
use App\Http\Transformers\FileTransformer;
use App\Models\File as FileEntry;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

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

        $entry = FileEntry::createFromRequest(\Request::file('file'));

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
        return $this->item(FileEntry::findOrFail($id), new FileTransformer());
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
        $model->delete();
    }

}