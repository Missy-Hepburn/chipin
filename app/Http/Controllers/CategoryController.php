<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

use App\Http\Requests;

class CategoryController extends Controller
{
    protected $guard = 'web';

    /**
     * Create a new controller instance
     */
    public function __construct() {
        $this->middleware('auth:web');
    }

    /**
     * GET Show category list.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $categories = Category::paginate(env('PAGINATOR', 20));

        return view('category.index', compact('categories'));
    }

    /**
     * GET New category creation view
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     **/
    public function create() {
        return view('category.create');
    }

    /**
     * POST Saves new category
     *
     * @param Requests\CategoryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Requests\CategoryRequest $request) {
        $category = new Category([
            'name' => $request->get('name'),
        ]);

        if ($request->hasFile('image')) {
            $category->setImage($request->file('image'));
        }

        $category->save();

        return redirect()->route('category.index');
    }

    /**
     * GET Shows category
     *
     * @param Category $category
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Category $category) {
        return redirect()->route('category.edit', ['category' => $category]);
    }

    /**
     * GET Shows category editing page
     *
     * @param Category $category
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Category $category) {
        return view('category.edit', compact('category'));
    }

    /**
     * PUT Update category
     *
     * @param Request $request
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Category $category) {
        $category->fill($request->intersect(['name']));

        if ($request->get('delete-image')) {
            $category->deleteImage();
        }

        if ($request->hasFile('image')) {
            $category->setImage($request->file('image'));
        }

        $category->save();

        return redirect()->route('category.index');
    }

    /**
     * GET Search category
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(Request $request) {
        $search = $request->get('search');
        if (empty($search)) {
            return redirect()->route('category.index');
        }

        $searchStr = '%' . $search . '%';

        $categories = Category::where('name', 'like', $searchStr)
            ->orderBy('name', 'asc')->paginate(env('PAGINATOR', 20));

        return view('category.index', compact('categories', 'search'));
    }

    /**
     * DELETE Bulk categories delete
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request) {
        $successIds = [];

        $items = Category::whereIn('id', $request->get('ids'))->get();
        foreach ($items as $item){
            if ($item->delete()) {
                $successIds[] = $item->id;
            }
        }
        return response()->json(['ids' => $successIds]);
    }

}
