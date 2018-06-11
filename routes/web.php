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

    // $router->get('information', 'StudentInformationController@edit2');
//    $router->get('information/{id}/update', 'StudentInformationController@update');
    $router->resource('information', StudentInformationController::class);

    $router->resource('subject-parallel', SubjectsParallelController::class);

    $router->resource('subject-before-after', SubjectBeforeAfterController::class);

    $router->resource('comments', CommentsController::class);

    Route::group(['middleware' => ['subjectRegister']], function (Router $router) {
        $router->get('subject-register/{id}/result-register', 'SubjectRegisterController@resultRegister');
        $router->get('subject-register/{id}/details', 'SubjectRegisterController@details');
        $router->resource('subject-register', SubjectRegisterController::class);
    });
});

