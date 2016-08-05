<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\Goal;
use Carbon\Carbon;
use Dingo\Api\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;


class UpdateGoalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->id == $this->container['request']->goal->user->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $diff = 5;
        $goal = $this->container['request']->goal;
        $today = Carbon::now()->addMinutes(-$diff);
        $type = empty($this->container['request']->input('type')) ? Goal::TYPE_PERSONAL : $this->container['request']->input('type');
        $dateRangeCheck = ($type == Goal::TYPE_PERSONAL) ? '' : 'start_date';

        return [
            'name' => 'required|regex:/^[\s\w]+$/ui|min:3|max:255|unique:goals,name,'
                . $goal->id . ',id,user_id,' . Auth::user()->id . ',parent_id,null',
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|between:1.00,1000000000.00',
            'start_date' => ($type !== Goal::TYPE_PERSONAL) ? 'bail|required|date|after:' . $today : '',
            'due_date' => 'required|date|bail|date_range_check:' . $dateRangeCheck . ',1,' . $goal->start_date . ',start_date',
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

    protected function failedAuthorization() {
        throw new UnauthorizedHttpException(trans('api.err.deny-goal-update'));
    }
}
