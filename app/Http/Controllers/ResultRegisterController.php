<?php

namespace App\Http\Controllers;

use App\Models\TimeStudy;
use Encore\Admin\Grid\Displayers;
use App\Models\ResultRegister;
use App\Models\StudentUser;
use App\Models\TimeRegister;
use App\Models\UserSubject;
use App\Models\Subjects;
use App\Models\SubjectRegister;
use App\Models\Semester;
use App\Models\SemesterSubjects;
use App\Models\Year;
use App\Models\SubjectGroup;
use App\Models\Rate;
use App\Models\Classroom;
use App\Models\UserAdmin;

use App\Http\Extensions\Facades\User;
use app\Http\Extensions\LayoutUser\ContentUser;
use Encore\Admin\Widgets\Alert;
use Encore\Admin\Widgets\Callout;
use Illuminate\Http\Request;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;

class ResultRegisterController extends Controller
{
    use ModelForm;

    public function index()
    {
        return User::content(function (ContentUser $content) {

            $content->header('Đăng ký môn học');
            $content->description('Danh sách môn học');
            $content->breadcrumb(
                ['text' => 'Đăng kí môn học', 'url' => '../user/result-register']
            );
            $content->body($this->grid());
        });
    }

    

 

   protected function grid()
    {
        return User::grid(ResultRegister::class, function (Grid $grid) {
            $user = Auth::user();
            //check school year
            $timeRegister = TimeRegister::orderBy('id', 'DESC')->first();
            //dd( $timeRegister->id);
            $grid->model()->where('time_register', $timeRegister->id)->where('id_user_student', $user->id);
//             $schoolYearUser = (string) $schoolYearUser;
            
            //$grid->id('id');
            $grid->column('Mã HP')->display(function(){
            	$subjetRegister = SubjectRegister::find($this->id_subject_register);
            	if($subjetRegister->code_subject_register) {
            		return $subjetRegister->code_subject_register;
            	} else {
            		return '';
            	}
            });
            $grid->id_subject('Tên môn học')->display(function ($id) {
            	$subject = Subjects::find($id);
            	if($subject->name) {
            		return $subject->name;
            	} else {
            		return '';
            	}
            });

            $grid->column('Số tín chỉ')->display(function () {
            	$subject = Subjects::find($this->id_subject);
            	if($subject->credits) {
            		return $subject->credits;
            	} else {
            		return '';
            	}
            });
            $grid->column('Số tín chỉ HP')->display(function () {
            	$subject = Subjects::find($this->id_subject);
            	if($subject->credits_fee) {
            		return $subject->credits_fee;
            	} else {
            		return '';
            	}
            });
            $grid->column('Nhóm môn')->display(function () {
                $subject = Subjects::find($this->id_subject);
                $nameGroup = $subject->subject_group()->pluck('name')->toArray();
                $groupSubject = array_map(function ($nameGroup){
                    if($nameGroup) {
                        return "<span class='label label-primary'>{$nameGroup}</span>"  ;
                    } else {
                        return '';
                    }
                },$nameGroup);
                return join('&nbsp;', $groupSubject);

            });
            $grid->column('Học kỳ - Năm')->display(function () {
                $id = $this->id;
                $subject = Subjects::find($id);
                $arraySemester = $subject->semester()->pluck('id')->toArray();
                $name = array_map(function ($arraySemester) {
                    $nameSemester = Semester::find($arraySemester)->name;
                    switch ($nameSemester) {
                        case 0 :
                            $nameSemester = 'Học kì hè';
                            break;
                        case 1:
                            $nameSemester = 'Học kì 1';
                            break;
                        case 2:
                            $nameSemester = 'Học kì 2';
                    }
                    $year = Semester::find($arraySemester)->year()->get();
                    $nameYear = $year['0']->name;
                    return "<span class='label label-info'>{$nameSemester} - {$nameYear}</span>";
                }, $arraySemester);
                return join('&nbsp;', $name);
            });
           
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();

 			$grid->actions(function ($actions) {
                $actions->disableEdit();
                $actions->disableDelete();
                $actions->append('<a href="javascript:void(0);" data-id="' . $this->getKey() . '"  class="btn btn-danger btnCancel" style="font-size: 1.5rem"><i class="glyphicon glyphicon-trash"></i> &nbsp Hủy bỏ </a>');
       		});
    	});
    }


//     public function details($id)
//     {
//         return User::content(function (ContentUser $content) use ($id) {
//             $subject = Subjects::findOrFail($id);
//             $content->header('Môn học');
//             $content->description($subject->name);
//             $content->breadcrumb(
//                 ['text' => 'Đăng kí môn học', 'url' => '../user/subject-register'],
//                 ['text' => $subject->name, 'url' => '../user/subject-register/'.$id.'/deltails']
//             );
//             $content->body($this->detailsView($id));
//         });
//     }

//     public function detailsView($id)
//     {
// //        $form = $this->form()->view($id);
//         $gridSubject_Register = $this->gridResultRegister($id)->render();
//         return view('vendor.details',
//             [
//                 'template_body_name' => 'User.SubjectRegister.info',
// //                'form' => $form,
//                 'gridSubjectRegister' => $gridSubject_Register

//             ]
//         );
//     }

//     public function timetable()
//     {

//         return view('User.SubjectRegister.timetable');
//     }
}
