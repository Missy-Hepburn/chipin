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

Route::get('/', function () {
    return view('welcome');
});

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->get('/auth', 'App\Api\V1\Controllers\AuthController@authenticate');
    $api->get('/register', 'App\Api\V1\Controllers\AuthController@register');
    $api->get('/register/facebook', 'App\Api\V1\Controllers\AuthController@registerFacebook');
});

$api->version('v1', ['middleware' => 'api.auth', ['providers' => 'facebook']], function ($api) {
    $api->get('/auth/facebook', 'App\Api\V1\Controllers\AuthController@authenticateFacebook');
});

$api->version('v1', ['middleware' => 'api'], function ($api) {
    $api->resource('profile', 'App\Api\V1\Controllers\ProfileController', ['only'=>['show', 'update', 'destroy']]);
    $api->resource('files', 'App\Api\V1\Controllers\FileController', ['only'=>['index', 'create', 'show', 'destroy']]);
    $api->resource('friends', 'App\Api\V1\Controllers\FriendController');
    $api->get('friendList/{token}', 'App\Api\V1\Controllers\FriendController@updateFromFb');
    $api->get('profile/search', 'App\Api\V1\Controllers\ProfileController@search');
});

Route::auth();
Route::get('user/activation/{token}', 'Auth\AuthController@activateUser')->name('user.activate');

Route::get('/home', 'HomeController@index');
