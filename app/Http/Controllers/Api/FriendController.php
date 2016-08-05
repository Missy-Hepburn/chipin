<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

class FriendController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return Auth::user()->getFriends(env('API_PAGINATOR', 100));
    }

    /**
     * Sends new friend requests
     *
     * @param Request $request
     * @return User[]
     */
    public function store(Request $request) {
        $user = Auth::user();
        $to = $request->get('friends');

        if (empty($to)) throw new PreconditionFailedHttpException(trans('api.err.no-friends-id'));

        if (!is_array($to)) $to = [$to];

        $newFriends = [];
        foreach ($to as $id) {

            $potential = User::find($id);
            if (!$potential || $potential == $user) continue;

            if (!$user->isFriendWith($potential)) {
                $user->befriend($potential);
            }

            $newFriends[] = $potential;
        }

        if (empty($newFriends)) {
            throw new BadRequestHttpException(trans('api.err.no-friends'));
        }

        return $newFriends;
    }

    /**
     * GET /friends/invites
     * Retrieves all friend invites sent to me
     *
     * @return User[]
     */
    public function invites() {
        $user = Auth::user();

        return $user->getFriendInvites()->paginate(env('API_PAGINATOR', 100));
    }

    /**
     * GET /friends/requests
     * Returns all friend requests that I sent
     *
     * @return User[]
     */
    public function requests() {
        $user = Auth::user();

        return $user->getFriendRequests()->paginate(env('API_PAGINATOR', 100));
    }

    /**
     * PUT /friends/{user_id}
     * Accepts friend request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(User $user) {
        return Auth::user()->acceptFriendRequest($user);
    }

    /**
     * DELETE /friends/{user_id}
     * Removes user wth user_id from friends/from friend requests
     *
     * @param User $user
     */
    public function destroy(User $user) {
        return Auth::user()->unfriend($user);
    }


    /**
     * POST /friendList/{token}
     * Gets friends from facebook
     *
     * @param $token
     * @param \SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb
     * @return User[]
     */
    public function updateFromFb($token, \SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb) {
        $user = $this->auth->user();

        $fb->setDefaultAccessToken($token);

        $response = $fb->get('/me');
        $id = $response->getDecodedBody()['id'];

        if(empty($user->fb_id)) {
            $user->fb_id = $id;
            $user->save();
        } elseif($user->fb_id != $id) {
            throw new \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException('You supplied access token of different user');
        }

        $response = $fb->get('/me/friends');

        $newFriends = [];
        foreach($response->getGraphEdge() as $fbFriend){
            $potentialFriendUser = \App\User::where('fb_id', $fbFriend['id'])->where('active', true)->first();
            if (empty($potentialFriendUser)) //Inactive or deleted, skip
                continue;

            if ($user->isFriendWith($potentialFriendUser)
                || $user->hasFriendRequestFrom($potentialFriendUser)) //Is already friended, Maybe also unfriended.
                continue;

            $user->befriend($potentialFriendUser);
            //$potentialFriendUser->acceptFriendRequest($user); // Should we autofriend?

            $newFriends[] = $potentialFriendUser;
        }

        return $newFriends;
    }
}
