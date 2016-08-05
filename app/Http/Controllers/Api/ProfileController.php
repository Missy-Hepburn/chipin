<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as Controller;
use App\Http\Requests\ImageRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Profile;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProfileController extends Controller{

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show() {
        return User::find(Auth::user()->id);
    }

    /**
     * * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @return \Illuminate\Auth\GenericUser|\Illuminate\Database\Eloquent\Model
     */
    public function update(UpdateUserRequest $request)
    {
        $user = Auth::user();

        if(!empty($user->profile)){
            $user->profile->fill($request->all());
            $user->push();
        }else{
            $profile = new Profile($request->all());
            $user->profile()->save($profile);
        }

        return $user;
    }

    /**
     * @return User
     */
    public function destroy() {
        $user = Auth::user();
        $user->active = false;
        $user->save();

        return $user;
    }

    /**
     * POST /profile/search
     * Searches users by first-,last- names
     *
     * @param Request $request
     * @return mixed
     */
    public function search(Request $request){
        $firstName = $request->get('first_name');
        $lastName = $request->get('last_name');

        if (empty($firstName) && empty($lastName)) {
            throw new BadRequestHttpException(trans('api.err.no-search-data'));
        }

        $firstNameStr = '%' . $firstName . '%';
        $lastNameStr = '%' . $lastName . '%';

        $users = User::whereHas('profile',
            function ($query) use ($firstNameStr, $lastNameStr) {
                $query
                    ->where("first_name", 'like', $firstNameStr)
                    ->orWhere("last_name", 'like', $lastNameStr);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(env('API_PAGINATOR', 100));

        return $users;
    }

    /**
     * POST /profile/image
     * Uploads new user image
     *
     * @param ImageRequest $request
     * @return User
     */
    public function image(ImageRequest $request) {
        $user = Auth::user();

        if (!$request->hasFile('image')) {
            throw new BadRequestHttpException(trans('api.err.no-image-request'));
        }

        $user->profile->setImage($request->file('image'));
        $user->push();

        return $user;
    }

    /**
     * DELETE /profile/image
     * Deletes user image
     *
     * @return User
     */
    public function imageDestroy() {
        $user = Auth::user();
        $user->profile->deleteImage();
        $user->push();

        return $user;
    }
}