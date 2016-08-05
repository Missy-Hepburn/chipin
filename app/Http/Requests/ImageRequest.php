<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Dingo\Api\Http\FormRequest;
use Illuminate\Support\Facades\Route;


class ImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'image' => 'required|image|max:10000',
        ];
    }

    public function attributes() {
        return array_map(function($item) {
            return '"' . $item . '"';
        }, [
            'image' => trans('web.image.name'),
        ]);
    }
}
