<?php

use Illuminate\Routing\Router;

Route::get('/', function () {
    return view('welcome');
});
Route::get('getLogin',function(){
	return view('User.getLogin');
});
Route::post('postLogin','UserController@postlogin');
Route::get('logout', 'UserController@logout');
Route::group(['prefix'=>'user', 'middleware'=>'studentLogin'], function(Router $router){
    $router->resource('student', UserController::class);

    $router->resource('information', StudentInformationController::class);

    $router->resource('subjectparallel', SubjectsParallelController::class);

    $router->resource('subjectbeforeafter', SubjectBeforeAfterController::class);

    $router->resource('comments', CommentsController::class);
});

