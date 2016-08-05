<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as Controller;
use App\Models\Category;
use App\Models\Profile;
use Illuminate\Http\Request;

class CategoryController extends Controller {

    public function index() {
        return $this->response->array(Category::all());
    }

}