<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Controllers\BaseController as Controller;
use App\Models\Profile;
use Dingo\Api\Provider\DingoServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Auth;
use App\User;
use App\Services\ActivationService;
use Validator;
use \SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
use \Dingo\Api\Exception\ResourceException;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;

/**
 * Class AppController
 * @package App\Api\V1\Controllers
 */
class AuthController extends Controller {

    public function __construct(Mailer $mailer) {
        $this->mailer = $mailer;
    }

    public function base() {
        return $this->response->errorForbidden();
    }

    public function authenticate(Request $request)
    {

        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        if(!Auth::attempt($credentials))
            return response()->json(['error' => 'invalid_credentials'], 401);

        if(!Auth::user()->active)
            return response()->json(['error' => 'user_not_activated'], 401);

        try {
            $token = JWTAuth::fromUser(Auth::user());
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token'));
    }

    public function authenticateFacebook()
    {
        $user = $this->auth->user();
        if(empty($user))
            return response()->json(['error' => 'invalid_credentials'], 401);

        try {
            $token = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token'));
    }

    public function register(Request $request, ActivationService $activationService){

        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            throw new ResourceException('Could not register user.', $validator->errors());
        }

        $user = $this->create($request->all());

        $activationService->sendCongratMail($user, true);

        return $this->response->array($user->toArray());
    }

    public function registerFacebook(Request $request, LaravelFacebookSdk $fb){

        $reqData = $request->all();

        $fb->setDefaultAccessToken($request->get('token'));

        $response = $fb->get('/me?fields=id,first_name,last_name,email,hometown,location,birthday,picture');
        $facebook_user = $response->getGraphUser();

        if(is_null($facebook_user->getFirstName())){
            $name = explode(' ', $facebook_user->getName(), 2);
            $firstName = $name[0];
            $lastName = isset($name[1]) ? $name[1] : null;
        }else{
            $firstName = $facebook_user->getFirstName();
            $lastName = $facebook_user->getLastName();
        }

        $pass = self::generatePassword();

        $data = [
            'fb_id' => $facebook_user->getId(),
            'email' => $facebook_user->getEmail(),
            'password' => $pass,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'birthday' => $facebook_user->getBirthday()->hasDate() ? $facebook_user->getBirthday()->format('Y-m-d') : null,
            'photo' => $facebook_user->getPicture()['url']
        ];

        if(!is_null($facebook_user->getHometown()))
            $data['nationality'] = self::convertCountryNameToCode($facebook_user->getHometown()['name']);

        if(!is_null($facebook_user->getLocation()))
            $data['country'] = self::convertCountryNameToCode($facebook_user->getLocation()['name']);

        $data = array_merge($data, $reqData);

        $validator = $this->validator($data);

        if ($validator->fails()) {
            throw new ResourceException('Could not register user.', $validator->errors());
        }

        $user = $this->create($data);
        if(!empty($data['photo'])) {
            $user->profile->photo()->associate(\App\Models\File::createFromUrl($data['photo']));
            $user->push();
        }

        if(!empty($user))
            $this->mailer->raw('You were registered at chipin with password ' . $pass,
                function (Message $m) use ($user) {
                    $m->to($user->email)->subject('Welcome mail');
                });
        else
            return false;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
            'first_name' => 'required|min:2',
            'last_name' => 'required|min:2',
            'nationality' => 'required|size:2',
            'country' => 'required|size:2',
            'birthday' => 'date|after:28.11.1899',
            'photo_id' => 'exists:'.with(new \App\Models\File)->getTable().',id',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $user->active = true;
        if(isset($data['fb_id']))
            $user->fb_id = $data['fb_id'];
        $user->save();

        $profile = new Profile($data);
        $user->profile()->save($profile);

        return $user;
    }

    protected static function generatePassword($length=8){ //TODO: move it out to service
        return bin2hex(openssl_random_pseudo_bytes($length/2));
    }

    protected static function convertCountryNameToCode($location){//TODO: move it out to service

        $pieces = explode(' ', $location);
        $countryName = array_pop($pieces);

        $countries = Config::get('constants.countries');

        return isset($countries[$countryName]) ? $countries[$countryName] : null;
    }
}
