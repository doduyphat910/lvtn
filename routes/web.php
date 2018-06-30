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

    $router->resource('user-subject',UserSubjectController::class);

    $router->resource('result-register',ResultRegisterController::class);

    $router->resource('point-subject',PointSubjectController::class);

    

    Route::group(['middleware' => ['subjectRegister']], function (Router $router) {
        //subject timeable
        $router->get('subject-timetable', 'SubjectRegisterController@timetable');
        //get list subject register from API
        $router->get('subject-register/{id}/list', 'APIController@getListSubjectRegister');
        $router->get('timetable', 'APIController@getTimetable');

        $router->get('subject-register/{id}/details', 'SubjectRegisterController@details');
        $router->resource('subject-register', SubjectRegisterController::class);

        //API save result register
        $router->get('subject-register/{id}/result-register', 'APIController@resultRegister');
        //API delete result register
        $router->get('subject-register/{id}/delete-register', 'APIController@deleteRegister');

        //API check subject before-after
        $router->get('subject-register/{id}/checkBeforeAtfer', 'APIController@checkBeforeAtfer');

        //API check subject parallel
        $router->get('subject-register/{id}/checkParallel', 'APIController@checkParallel');
        //learn-improvement
        $router->resource('learn-improvement',LearnImprovenmentController::class);
    });
});

