<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\User;
use Dingo\Api\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


class UpdateUserRequest extends FormRequest
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
        $id = Route::input('user') ? Route::input('user')->id : Auth::user()->id;

        return [
            'email' => get_class(Auth::user()) == User::class ? '' : 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|min:6',
            'first_name' => 'required|regex:/^[\-\'\s\w]+$/ui|min:2',
            'last_name' => 'required|regex:/^[\-\'\s\w]+$/ui|min:2',
            'nationality' => 'required|size:2|country_code',
            'country' => 'required|size:2|country_code',
            'birthday' => 'sometimes|date|after:28.11.1899',
            'address' => 'sometimes|regex:*^[' . preg_quote('.,\-:;/()#№"\'') . '\s\w]+$*ui',
            'occupation' => 'sometimes|regex:*^[\s\w[:punct:]]+$*ui',
            'income' => 'sometimes|regex:/^[\-\.,\s\w\$\p{Sc}£€]+$/ui',
            'image' => get_class(Auth::user()) == User::class ? '' : 'sometimes|image|max:10000'
        ];
    }

    public function attributes() {
        $items = [
            'email', 'first_name', 'last_name',
            'nationality', 'country', 'birthday',
            'address', 'occupation', 'income', 'image'];

        return array_combine($items, array_map(function($item) {
            return '"' . trans('web.user.' . $item) . '"';
        }, $items));
    }
}
