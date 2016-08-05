<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Route;


class CategoryRequest extends Request
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
        $id = Route::input('category') ? Route::input('category')->id : null;
        return [
            'name' => 'required|string|min:2|max:255|unique:categories,name,' . $id,
            'image' => 'sometimes|image',
        ];
    }

    public function attributes() {
        return array_map(function($item) {
            return '"' . $item . '"';
        }, [
            'name' => trans('web.category.name'),
            'image' => trans('web.category.image'),
        ]);
    }
}
