<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix'=>'v1'], function() {
Route::post('/register', 'UserController@register');
Route::post('/login', 'UserController@login');
//Route::post('/plan/{plan}/user/register', 'PlanController@subscribeNewUserToPlan')->name('add.new.user.to.plan');

Route::post('/logout', 'UserController@logout');

Route::group(['prefix'=>'account', 'middleware'=>'jwt.auth'], function() {
Route::get('/user', 'UserController@fetchUser');
Route::post('/user/add/card/{TnxRef}', 'CardController@addCard');
Route::get('/plans', 'PlanController@index');
Route::post('/plan/create', 'PlanController@create');
Route::post('/plan/{plan}/register', 'PlanController@registerNewUser');
Route::post('/plan/{plan}/users/add', 'PlanController@sendUserEmail');
Route::post('/plan/{plan}/subscribe/{TnxRef}', 'PlanController@subscribeNewUserToPlan');

});

});