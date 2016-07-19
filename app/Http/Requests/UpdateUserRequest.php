<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Route;


class UpdateUserRequest extends Request
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
        $id = Route::input('user') ? Route::input('user')->id : null;
        return [
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'first_name' => 'required|min:2',
            'last_name' => 'required|min:2',
            'nationality' => 'required|size:2',
            'country' => 'required|size:2',
            'birthday' => 'date|after:28.11.1899',
        ];
    }

    public function attributes() {
        return array_map(function($item) {
            return '"' . $item . '"';
        }, [
            'email' => trans('web.user.email'),
            'first_name' => trans('web.user.first-name'),
            'last_name' => trans('web.user.last-name'),
            'nationality' => trans('web.user.nationality'),
            'country' => trans('web.user.country'),
            'birthday' => trans('web.user.birthday'),
        ]);
    }
}
