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
Route::get('/banks', 'AccountController@fetchAllBanks');
Route::post('/user/verify', 'AccountController@verifyAccountNumber');
Route::post('/user/add/accountno/', 'AccountController@addAccount');
Route::get('/user/accountno/all', 'AccountController@fetchUserAccounts');
Route::delete('/user/accountno/delete', 'AccountController@deleteAccount');
Route::post('/user/add/card/{TnxRef}', 'CardController@addCard');
Route::get('/user/cards', 'CardController@allCards');
Route::delete('/user/card/delete/{id}', 'CardController@deleteCard');
Route::get('/plans', 'PlanController@index');
Route::get('/plans/subscribed', 'PlanController@fetchSubscribedPlans');
Route::post('/plan/create', 'PlanController@create');
Route::post('/plan/{plan}/register', 'PlanController@registerNewUser');
Route::get('/plan/{plan}/users', 'PlanController@fetchAllSubscribedUsersToAPlan');
Route::post('/plan/{plan}/users/add', 'PlanController@sendUserEmail');
Route::post('/plan/{plan}/subscribe/{TnxRef}', 'PlanController@subscribeNewUserToPlan');

});

});