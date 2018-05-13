<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    //year
    $router->resource('year', YearController::class);

    //semester
    $router->resource('semester', SemesterController::class);

    //subject_register
    $router->resource('subject_register', SubjectRegisterController::class);

    //time_register
    $router->resource('time_register', TimeRegisterController::class);

    //subject
    $router->resource('subjects', SubjectsController::class);

    //subject_group
    $router->resource('subject_group', SubjectGroupController::class);

    //class_room
    $router->resource('class_room', ClassroomController::class);

    //rate
    $router->resource('rate', RateController::class);

    //User
    $router->get('student_user/create');
    $router->resource('all_user',StudentUserController::class );
    $router->resource('teacher_user', StudentUserController::class);
    $router->resource('student_user', StudentUserController::class);

    //class
    $router->resource('class', ClassController::class);

    //department
    $router->resource('department', DepartmentController::class);

}

);
