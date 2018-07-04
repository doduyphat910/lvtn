<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClassSTU;
use App\Models\StudentUser;
use App\Models\SubjectRegister;
use App\Models\TimeRegister;
use App\Models\UserAdmin;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

class HomeController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('Trang chủ');
//            $content->description('Description...');
            $countStudent = StudentUser::count();
            $countTeacher = UserAdmin::where('type_user',0)->count();
            $countAdmin = UserAdmin::where('type_user',1)->count();
            $countClass = ClassSTU::count();
            $countTimeRegister = TimeRegister::count();
            $countSubjectRegister = SubjectRegister::count();
            $arrClass = StudentUser::distinct('school_year')->orderBy('school_year', 'DESC')->limit(6)->pluck('school_year')->toArray();
            $arrClassShow = [];
//            foreach($arrClass as $class) {
//                $arrClassShow += (string)$class;
//            }
            $arrClassShow += array_values($arrClass);
//            dd($arrClassShow);
            $content->row(
                view('vendor.admin.dashboard.title',
                    [
                        'countStudent' => $countStudent,
                        'countTeacher' => $countTeacher,
                        'countAdmin' => $countAdmin,
                        'countClass' => $countClass,
                        'countTimeRegister' => $countTimeRegister,
                        'countSubjectRegister' => $countSubjectRegister,
                        'arrClassShow' => $arrClassShow

                    ]
                )
            );

//            $content->row(function (Row $row) {
//
//                $row->column(4, function (Column $column) {
//                    $column->append(Dashboard::environment());
//                });
//
//                $row->column(4, function (Column $column) {
//                    $column->append(Dashboard::extensions());
//                });
//
//                $row->column(4, function (Column $column) {
//                    $column->append(Dashboard::dependencies());
//                });
//            });
        });
    }
}
