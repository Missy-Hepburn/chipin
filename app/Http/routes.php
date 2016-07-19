<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$api = app('Dingo\Api\Routing\Router');

/* API */
$api->version('v1', function ($api) {
    $api->post('/auth', 'App\Api\V1\Controllers\AuthController@authenticate');
    $api->post('/register', 'App\Api\V1\Controllers\AuthController@register');
    $api->post('/register/facebook', 'App\Api\V1\Controllers\AuthController@registerFacebook');
});

$api->version('v1', ['middleware' => 'api.auth', ['providers' => 'facebook']], function ($api) {
    $api->post('/auth/facebook', 'App\Api\V1\Controllers\AuthController@authenticateFacebook');
});

$api->version('v1', ['middleware' => 'api'], function ($api) {
    $api->get('profile', 'App\Api\V1\Controllers\ProfileController@show');
    $api->post('profile', 'App\Api\V1\Controllers\ProfileController@update');
    $api->delete('profile', 'App\Api\V1\Controllers\ProfileController@destroy');
    $api->resource('files', 'App\Api\V1\Controllers\FileController', ['only'=>['store', 'show', 'destroy']]);
    $api->resource('friends', 'App\Api\V1\Controllers\FriendController');
    $api->post('friendList/{token}', 'App\Api\V1\Controllers\FriendController@updateFromFb');
    $api->post('profile/search', 'App\Api\V1\Controllers\ProfileController@search');
});

/* Profile activation link*/
Route::get('user/activation/{token}', 'Auth\AuthController@activateUser')->name('user.activate');

/* WebAdmin */

Route::get('/', 'Controller@welcome');

Route::get('login', 'Auth\AuthController@showLoginForm');
Route::post('login', 'Auth\AuthController@login');
Route::get('logout', 'Auth\AuthController@logout');

Route::group(['middleware' => ['auth:web']], function () {
    Route::get('/user/search/', ['as' => 'user.search', 'uses' => 'UserController@search']);
    Route::resource('user', 'UserController', ['except' => ['destroy']]);
    Route::post('/user/{user}/block/', ['as' => 'user.block', 'uses' => 'UserController@block']);
    Route::post('/user/activate', 'UserController@activate');
    Route::post('/user/deactivate', 'UserController@deactivate');
});
