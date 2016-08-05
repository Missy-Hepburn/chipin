<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\Goal;
use Carbon\Carbon;
use Dingo\Api\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


class StoreGoalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $diff = 5;
        $today = Carbon::now()->addMinutes(-$diff);
        $type = empty($this->container['request']->input('type')) ? Goal::TYPE_PERSONAL : $this->container['request']->input('type');
        $dateRangeCheck = ($type == Goal::TYPE_PERSONAL) ? 'now' : 'start_date';

        return [
            'name' => 'required|regex:/^[\s\w]+$/ui|min:3|max:255|unique:goals,name,'
                . 'NULL,id,user_id,' . Auth::user()->id . ',parent_id,null',
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|between:1.00,1000000000.00',
            'start_date' => ($type !== Goal::TYPE_PERSONAL) ? 'bail|required|date|after:' . $today : '',
            'due_date' => 'required|date|bail|date_range_check:' . $dateRangeCheck . ',1',
            'type' => 'sometimes|in:' . implode(',', Goal::getTypes()),
            'timer' => 'sometimes|in:' . implode(',', Goal::getTimers()),
        ];
    }

    public function attributes() {
        $items = [
            'name', 'category_id', 'type',
            'start_date', 'due_date',
            'amount', 'timer'
        ];

        return array_combine($items, array_map(function($item) {
            return '"' . trans('web.goal.' . $item) . '"';
        }, $items));

    }
}
