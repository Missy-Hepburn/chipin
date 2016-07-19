<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Controllers\BaseController as Controller;
use App\Models\Friend;
use Illuminate\Http\Request;
use App\Models\Profile;

class FriendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = $this->auth->user();

        return $user->profile->friends;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $this->auth->user();

        $friend_profile = Profile::findOrFail($request->get('friend_profile_id'));

        $friend = new Friend();
        $friend->profile = $user->profile;
        $friend->friend_profile = $friend_profile;
        $friend->push();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->auth->user();

        return $user->profile->friends()
            ->where('id', $id)
            ->firstOrFail();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = $this->auth->user();

        $friend = $user->profile->friends()
            ->where('id', $id)
            ->firstOrFail();

        $friend_profile = Profile::findOrFail($request->get('friend_profile_id'));

        $friend->friend_profile = $friend_profile;
        $friend->push();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = $this->auth->user();

        $friend = $user->profile->friends()
            ->where('id', $id)
            ->firstOrFail();

        $friend->active = false;

        $friend->save();
    }

    public function updateFromFb($token, \SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
    {
        $user = $this->auth->user();

        $fb->setDefaultAccessToken($token);

        $response = $fb->get('/me');
        $id = $response->getDecodedBody()['id'];

        if(empty($user->fb_id)) {
            $user->fb_id = $id;
            $user->save();
        }else
            if($user->fb_id != $id)
                throw new \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException('You supplied access token of different user');

        $response = $fb->get('/me/friends');

        $newFriends = [];

        foreach($response->getGraphEdge() as $fbFriend){
            $potentialFriendUser = \App\User::where('fb_id', $fbFriend['id'])->where('active', true)->first();
            if(empty($potentialFriendUser)) //Inactive or deleted, skip
                continue;

            $friend = Friend::where('profile_id', $user->profile->id)
            ->where('friend_profile_id', $potentialFriendUser->profile->id)
            ->first();

            if(!empty($friend))//Is already friended, Maybe also unfriended.
                continue;

            $friend = new Friend();
            $friend->profile()->associate($user->profile);
            $friend->friend_profile()->associate($potentialFriendUser->profile);
            $friend->active = true;
            $friend->push();

            $newFriends[] = $friend;
        }

        return $this->response->array($newFriends);
    }
}
