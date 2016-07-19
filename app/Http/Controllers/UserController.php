<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\File;
use App\Models\Profile;
use App\Services\ActivationService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller {

    protected $guard = 'web';

    /**
     * Create a new controller instance
     */
    public function __construct() {
        $this->middleware('auth:web');
    }

    /**
     * GET Show the users list.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $users = User::orderBy('created_at', 'desc')->paginate(env('PAGINATOR', 20));

        return view('user.index', compact('users'));
    }

    /**
     * GET New user creation view
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     **/
    public function create() {
        $countries = Config::get('constants.countries');

        return view('user.create', compact('countries'));
    }

    /**
     * POST Saves new user
     *
     * @param Requests\StoreUserRequest $request
     * @param ActivationService $activationService
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Foundation\Validation\ValidationException
     */
    public function store(Requests\StoreUserRequest $request, ActivationService $activationService) {
        $user = new User([
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
        ]);
        $user->active = true;
        $user->save();

        $profile = new Profile($request->except(['password', 'email']));

        if ($request->hasFile('photo')) {
            $profile->setPhoto($request->file('photo'));
        }

        $user->profile()->save($profile);

        $activationService->sendCongratMail($user, true);

        return redirect()->route('user.index');
    }

    /**
     * GET Shows user profile
     *
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(User $user) {
        return redirect()->route('user.edit', ['user' => $user]);
    }

    /**
     * GET Shows user profile editing page
     *
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(User $user) {
        $countries = Config::get('constants.countries');
        return view('user.edit', compact('user', 'countries'));
    }

    /**
     * PUT Update user profile
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Requests\UpdateUserRequest $request, User $user) {
        $user->fill($request->intersect(['email']));
        $user->profile->fill($request->intersect([
            'first_name', 'last_name', 'nationality', 'country',
            'birthday', 'address', 'occupation', 'income'
        ]));

        if ($request->get('password')) {
            $user->password = bcrypt($request->get('password'));
        }

        if ($request->has('active')) {
            $user->active = true;
        } else {
            $user->active = false;
        }

        if ($request->has('delete-photo')) {
            $user->profile->deletePhoto();
        }

        if ($request->hasFile('photo')) {
            $user->profile->setPhoto($request->file('photo'));
        } elseif ($request->hasFile('new-photo')) {
            $user->profile->setPhoto($request->file('new-photo'));
        }

        $user->push();

        return redirect()->route('user.index');
    }

    /**
     * POST Search user profiles
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(Request $request) {
        $search = $request->get('search');
        if (empty($search)) {
            return redirect()->route('user.index');
        }

        $searchStr = '%' . $search . '%';

        $users = User::whereHas('profile',
            function ($query) use ($searchStr) {
                $query->whereRaw("concat(first_name, ' ', last_name) like '$searchStr'");
            })
            ->orWhere('email', 'like', $searchStr)
            ->orderBy('created_at', 'desc')->paginate(env('PAGINATOR', 20));

        return view('user.index', compact('users', 'search'));
    }

    /**
     * POST Bulk deactivation
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate(Request $request){
        $ids = $request->get('ids');
        $successIds = [];
        $users = User::whereIn('id', $ids)->get();
        foreach($users as $user){
            $user->active = 0;
            if($user->save())
                $successIds[] = $user->id;
        }
        return response()->json(['ids' => $successIds]);
    }

    /**
     * POST Bulk activation
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activate(Request $request){
        $ids = $request->get('ids');
        $users = User::whereIn('id', $ids)->get();
        $successIds = [];
        foreach($users as $user){
            $user->active = 1;
            if($user->save())
                $successIds[] = $user->id;
        }
        return response()->json(['ids' => $successIds]);
    }
}
