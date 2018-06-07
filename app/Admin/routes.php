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
    $router->get('year/{id}/details', 'YearController@details');
    $router->resource('year', YearController::class);

    //semester
    $router->get('semester/{id}/details', 'SemesterController@details');
    $router->resource('semester', SemesterController::class);

    //subject_register
    $router->get('subject_register/{id}/details', 'SubjectRegisterController@details');
    $router->resource('subject_register', SubjectRegisterController::class);

    //time_register
    $router->get('time-register/{id}/details', 'TimeRegisterController@details');
    $router->resource('time-register', TimeRegisterController::class);

    //subject
    $router->get('subject/{id}/details', 'SubjectsController@details');
    $router->resource('subjects', SubjectsController::class);

    //subject_group
    $router->get('subject_group/{id}/details', 'SubjectGroupController@details');
    $router->resource('subject_group', SubjectGroupController::class);

    //class_room
    $router->get('class_room/{id}/details', 'ClassroomController@details');
    $router->resource('class_room', ClassroomController::class);

    //rate
    $router->get('rate/{id}/details', 'RateController@details');
    $router->resource('rate', RateController::class);

    // User
    $router->get('user_admin/create');
    $router->resource('user_admin',UserAdminController::class );
    $router->resource('teacher_user',UserAdminController::class );
    $router->resource('all_user',UserAdminController::class );

    //Import user
    $router->post('/import_student/parse', 'ImportStudentController@parse');
    $router->post('/import_student/review', 'ImportStudentController@review');
    $router->resource('import_student',ImportStudentController::class );

    //Student User
    $router->get('student_user/{id}/details', 'StudentUserController@details');
    $router->resource('student_user', StudentUserController::class);

    //status
    $router->resource('student_status', StatusController::class);
    //class
    $router->get('class/{id}/details', 'ClassController@details');
    $router->resource('class', ClassController::class);

    //department
    $router->get('department/{id}/details', 'DepartmentController@details');
    $router->resource('department', DepartmentController::class);

    //subject_before_after
    $router->resource('subject_before_after', SubjectBeforeAfterController::class);

    //subjects_parallel
    $router->resource('subjects_parallel', SubjectParallelController::class);
}

);
