<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as Controller;
use App\Models\Invite;
use App\User;
use Illuminate\Http\Request;
use App\Models\Goal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;


class InviteController extends Controller {

    /**
     * GET /invite/
     * Gets all my invites
     *
     * @param Request $request
     * @return Goal[]
     */
    public function index(Request $request) {
        $user = Auth::user();

        $list = $user->invites();

        if (in_array($request->get('type'), Goal::getTypes())) {
            $list->where('type', $request->get('type'));
        }

        return $list->wherePivot('status', Invite::STATUS_PENDING)->get();
    }

    /**
     * POST /invite/{goal}
     * Accept or decline invite
     *
     * @param Goal $goal
     * @param Request $request
     * @return Goal
     */
    public function confirm(Goal $goal, Request $request) {
        $user = Auth::user();
        $status = $request->get('status');

        if (!$goal->isInvitable()) {
            throw new PreconditionFailedHttpException(trans('api.err.cant-update'));
        }

        if (!in_array($status, Invite::getUpdatableStatuses())) {
            throw new BadRequestHttpException(trans('api.err.wrong-invite-status'));
        }

        DB::beginTransaction();

        $invite = Invite::lookup($goal, $user);

        if (!$invite) {
            /* Nothing to confirm */
            throw new BadRequestHttpException(trans('api.err.cant-confirm'));
        }

        if ($invite->status == $status) {
            /* It's ok now */
            return $invite->reference;
        }

        if ($invite->status == Invite::STATUS_ACCEPTED) {
            /* Can't change if already accepted */
            throw new BadRequestHttpException(trans('api.err.cant-confirm'));
        }

        $result = $goal->processInvite($invite, [
            'status' => $status,
            'amount' => $request->get('amount')
        ]);

        DB::commit();

        return $result;
    }

    /**
     * POST /goal/{goal}/invite
     * Invite users to goal
     *
     * @param Goal $goal
     * @param Request $request
     * @return Goal $goal
     */
    public function store(Goal $goal, Request $request) {
        if ($goal->user->id != Auth::user()->id) {
            throw new UnauthorizedHttpException(trans('api.err.deny-goal'));
        }

        if (!$goal->isInvitable()) {
            throw new PreconditionFailedHttpException(trans('api.err.cant-update'));
        }

        DB::beginTransaction();

        $invites = $goal
            ->invites()
            ->wherePivotIn('status', [Invite::STATUS_DECLINED, Invite::STATUS_ACCEPTED])
            ->get();

        $list = $request->get('users');
        if (!is_array($list)) {
            $list = [$list];
        }

        if (count($list)) {
            foreach ($list as $item) {
                $user = User::find($item);

                if ($user && $user->isFriendWith(Auth::user())
                    && (!$invites->count() || !$invites->contains($user))) {
                    $invites->add($user);
                }
            }
        }

        $goal->invites()->sync($invites);

        DB::commit();

        return $goal->addWith('invites');
    }

    /**
     * GET /goal/{goal}/invite
     * Show goal invites
     *
     * @param Goal $goal
     * @param Request $request
     * @return mixed
     */
    public function show(Goal $goal, Request $request) {
        if ($goal->user != Auth::user()) {
            throw new UnauthorizedHttpException(trans('api.err.deny-goal'));
        }

        $list = $goal->invites();

        if (in_array($request->get('status'), Invite::getStatuses())) {
            $list->where('status', $request->get('status'));
        }

        return $list->get();
    }
}