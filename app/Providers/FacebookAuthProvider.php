<?php
namespace App\Providers;

use App\User;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Route;
use Dingo\Api\Auth\Provider\Authorization;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class FacebookAuthProvider extends Authorization  {

    public function authenticate(Request $request, Route $route) {
        $token = $this->parseToken($request);

        if (empty($token)) {
            throw new BadRequestHttpException($this->getAuthorizationMethod());
        }

        try {
            $facebookUser = Socialite::with('facebook')->userFromToken($token);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($this->getAuthorizationMethod());
        }

        $user = User::find($facebookUser->getId());

        if(empty($user))
            $user = User::where('email', $facebookUser->getEmail())->first();

        if(empty($user))
            // If authentication then fails we must throw the UnauthorizedHttpException.
            throw new UnauthorizedHttpException($this->getAuthorizationMethod());

        return $user;
    }

    public function getAuthorizationMethod() {
        return 'bearer';
    }

    public function parseToken($request) {
        $str = $request->get('token');

        return str_replace($this->getAuthorizationMethod() . ' ', '', $str);
    }
}