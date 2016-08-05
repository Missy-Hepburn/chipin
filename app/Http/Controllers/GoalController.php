<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Goal;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;

class GoalController extends Controller
{
    protected $guard = 'web';

    /**
     * Create a new controller instance
     */
    public function __construct() {
        $this->middleware('auth:web');
    }

    /**
     * GET /goal
     * Show goal list.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $goals = Goal::where('parent_id', null)
            ->paginate(env('PAGINATOR', 20));
        $categories = Category::all();
        $types = Goal::getTypes();

        return view('goal.index', compact('goals', 'categories', 'types'));
    }

    /**
     * GET /goal/search
     * Search category
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function search(Request $request) {
        $search = $request->get('search', '');
        $category = $request->get('category');
        $type = $request->get('type');
        $user_id = $request->get('user_id');
        $user = null;

        if (empty($search) && empty($category) && empty($type) && empty($user_id)) {
            return redirect()->route('goal.index');
        }

        $query = Goal::where('parent_id', null);

        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }
        if (!empty($category)) {
            $query->where('category_id', $category);
        }
        if (!empty($type)) {
            $query->where('type', $type);
        }
        if (!empty($user_id)) {
            $query->where('user_id', $user_id);
            $user = User::find($user_id);
        }

        $goals = $query->orderBy('name', 'asc')->paginate(env('PAGINATOR', 20));

        $categories = Category::all();
        $types = Goal::getTypes();

        return view('goal.index', compact('goals', 'search', 'category', 'type', 'categories', 'types', 'user'));
    }

    /**
     * GET /goal/{goal}
     * View a goal
     *
     * @param Goal $goal
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function view(Goal $goal) {
        return view('goal.view', compact('goal'));
    }
}
