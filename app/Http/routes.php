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
$api->version('v1', ['namespace' => 'App\Http\Controllers\Api'], function ($api) {
    $api->post('/auth', 'AuthController@authenticate');
    $api->post('/register', 'AuthController@register');
    $api->post('/register/facebook', 'AuthController@registerFacebook');
});

$api->version('v1', ['namespace' => 'App\Http\Controllers\Api', 'middleware' => 'api.auth', ['providers' => 'facebook']], function ($api) {
    $api->post('/auth/facebook', 'AuthController@authenticateFacebook');
});

$api->version('v1', ['namespace' => 'App\Http\Controllers\Api', 'middleware' => 'api'], function ($api) {
    $api->get('profile', 'ProfileController@show');
    $api->post('profile', 'ProfileController@update');
    $api->delete('profile', 'ProfileController@destroy');
    $api->post('profile/search', 'ProfileController@search');
    $api->post('profile/image', 'ProfileController@image');
    $api->delete('profile/image', 'ProfileController@imageDestroy');

    $api->get('friends', 'FriendController@index');
    $api->post('friends', 'FriendController@store');
    $api->put('friends/{user}', 'FriendController@update');
    $api->delete('friends/{user}', 'FriendController@destroy');
    $api->get('friends/invites', 'FriendController@invites');
    $api->get('friends/requests', 'FriendController@requests');
    $api->post('friendList/{token}', 'FriendController@updateFromFb');

    $api->resource('category', 'CategoryController', ['only' => ['index']]);

    $api->resource('goal', 'GoalController', ['only' => ['index', 'store', 'update', 'show']]);

    $api->post('goal/{goal}/image', 'GoalController@image');
    $api->delete('goal/{goal}/image', 'GoalController@imageDestroy');

    $api->post('goal/{goal}/invite', 'InviteController@store'); //invite users to goal
    $api->get('goal/{goal}/invite', 'InviteController@show'); //show all goal invites
    $api->put('goal/{goal}/invite', 'InviteController@update'); //change user list to be invited
    $api->get('invite', 'InviteController@index'); //show all my invites
    $api->post('invite/{goal}', 'InviteController@confirm'); // accept or decline invite
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
    Route::post('/user/activate', 'UserController@activate');
    Route::post('/user/deactivate', 'UserController@deactivate');

    Route::get('category/search', ['as' => 'category.search', 'uses' => 'CategoryController@search']);
    Route::delete('category/delete', ['as' => 'category.delete', 'uses' => 'CategoryController@delete']);
    Route::resource('category', 'CategoryController');

    Route::get('goal', ['as' => 'goal.index', 'uses' => 'GoalController@index']);
    Route::get('goal/search', ['as' => 'goal.search', 'uses' => 'GoalController@search']);
    Route::get('goal/{goal}', ['as' => 'goal.show', 'uses' => 'GoalController@view']);
});
