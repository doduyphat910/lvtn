<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClassSTU;
use App\Models\ResultRegister;
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
            $roles = Admin::user()->roles->first();
            if($roles->slug == 'giangvien') {
                $script = <<<EOT
                 window.location.href = '/admin/teacher/class';
EOT;
                Admin::script($script);
            }
//            $script = <<<EOT
//            if (location.href.indexOf('reload')==-1)
//            {
//               location.href=location.href+'?reload';
//            }
//EOT;
//            Admin::script($script);
//            header("Refresh:0");
            $content->header('Trang chủ');
//            $content->description('Description...');
            $countUserStudent = StudentUser::count();
            $countTeacher = UserAdmin::where('type_user',0)->count();
            $countAdmin = UserAdmin::where('type_user',1)->count();
            $countClass = ClassSTU::count();
            $countTimeRegister = TimeRegister::count();
            $countSubjectRegister = SubjectRegister::count();
            //chart 1
            $arrClass = StudentUser::distinct('school_year')->orderBy('school_year', 'DESC')->limit(6)->pluck('school_year')->toArray();
            $countStudent = [];
            foreach($arrClass as $class) {
                $countClass = StudentUser::where('school_year', $class)->count();
                array_push($countStudent, $countClass);
            }
            //chart 2
            $timeRegisters = TimeRegister::orderBy('id', 'DESC')->limit(5)->pluck('name')->toArray();
            $timeRegisters2 = TimeRegister::orderBy('id', 'DESC')->limit(5)->get()->toArray();
            $dataTimeRegister = [];
            foreach($timeRegisters2 as $timeRegister) {
                $countStudentRegister = ResultRegister::where('time_register', $timeRegister['id'])->count();
                array_push($dataTimeRegister, $countStudentRegister);
            }
            $content->row(
                view('vendor.admin.dashboard.title',
                    [
                        'countUserStudent' => $countUserStudent,
                        'countTeacher' => $countTeacher,
                        'countAdmin' => $countAdmin,
                        'countClass' => $countClass,
                        'countTimeRegister' => $countTimeRegister,
                        'countSubjectRegister' => $countSubjectRegister,
                        //chart 1
                        'arrClass' => json_encode($arrClass),
                        'countStudent' =>json_encode($countStudent),
                        //chart 2
                        'timeRegisters' => json_encode($timeRegisters),
                        'dataTimeRegister' => json_encode($dataTimeRegister),

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
