<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as Controller;
use App\Http\Requests\ImageRequest;
use App\Http\Requests\StoreGoalRequest;
use App\Http\Requests\UpdateGoalRequest;
use App\Models\Category;
use App\Models\Goal;
use App\Models\Invite;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class GoalController extends Controller {

    public function index(Request $request) {
        $list = Auth::user()->goals();

        if ($request->get('type')) {
            $type = $request->get('type');

            if (!in_array($type, Goal::getTypes())) {
                throw new BadRequestHttpException(trans('api.err.wrong-type', ['type' => $type]));
            }

            /* TODO: Think about collectives! */
            $list->where('type', $type);
        }

        $list->get();

        return $list->get();
    }

    public function show(Goal $goal) {
        $user = Auth::user();
        if ($user != $goal->user && empty(Invite::lookup($goal, $user))) {
            throw new UnauthorizedHttpException(trans('api.err.deny-goal'));
        }

        return $goal->addWith(['invites']);
    }

    public function store(StoreGoalRequest $request) {
        DB::beginTransaction();

        $goal = Goal::create($request->intersect([
            'name', 'due_date', 'amount', 'timer', 'type'
        ]));

        $goal->user()->associate(Auth::user());
        $goal->category()->associate(Category::find($request->get('category_id')));

        if (empty($goal->type)) {
            $goal->type = Goal::DEFAULT_TYPE;
        }

        if ($goal->type == Goal::TYPE_PERSONAL) {
            $goal->start_date = Carbon::now()->toDateTimeString();
        } else {
            $goal->start_date = $request->get('start_date');
        }

        if (empty($goal->timer)) {
            $goal->timer = Goal::DEFAULT_TIMER;
        }

        $goal->push();
        DB::commit();

        return $goal;
    }

    public function update(UpdateGoalRequest $request, Goal $goal) {
        /* Check if goal is own in validators */
        DB::beginTransaction();
        $updated = $goal->update($request->intersect(['name', 'category_id', 'due_date', 'amount', 'timer']));

        if (!$updated) {
            throw new PreconditionFailedHttpException(trans('api.err.cant-update'));
        }

        if (!$goal->isStarted() && $goal->type != Goal::TYPE_PERSONAL) {
            $goal = $goal->update($request->intersect(['start_date']));
        }

        $goal->push();
        DB::commit();

        return $goal;
    }

    public function image(ImageRequest $request, Goal $goal) {
        if (Auth::user()->id != $goal->user->id) {
            throw new UnauthorizedHttpException(trans('api.err.deny-goal-update'));
        }

        if (!$request->hasFile('image')) {
            throw new BadRequestHttpException(trans('api.err.no-image-request'));
        }

        $goal->setImage($request->file('image'));
        $goal->push();

        return $goal;
    }

    public function imageDestroy(Goal $goal) {
        if (Auth::user()->id != $goal->user->id) {
            throw new UnauthorizedHttpException(trans('api.err.deny-goal-update'));
        }

        $goal->deleteImage();
        $goal->push();

        return $goal;
    }

}