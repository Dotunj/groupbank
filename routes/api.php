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
Route::post('/plan/{plan}/user/register', 'PlanController@subscribeNewUserToPlan')->name('add.new.user.to.plan');

Route::group(['prefix'=>'account', 'middleware'=>'jwt.auth'], function() {
Route::get('/plans', 'PlanController@index');
Route::post('/plan/create', 'PlanController@create');
Route::post('/plan/{plan}/users/add', 'PlanController@sendUserEmail');
Route::post('/plan/{plan}/subscribe', 'PlanController@subscribeRegisteredUserToPlan');
});

});