<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('getLogin',function(){
	return view('User.getLogin');
});
Route::post('postLogin','UserController@postlogin');

Route::group(['prefix'=>'user', 'middleware'=>'studentLogin'], function(){
	Route::get('student', 'UserController@test');
});

