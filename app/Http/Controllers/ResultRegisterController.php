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
            $content->body(
                view('vendor.details',
                [
                    'template_body_name' => 'User.ResultRegister.info',
                    'form' => $this->formTimeRegister(),
                    'grid' => $this->grid()
                ])
            );
        });
    }

    protected function formTimeRegister()
    {
        return User::form(TimeRegister::class, function (Form $form) {
            $form->registerBuiltinFields();
            $id = Auth::User()->id;
            $arrIdTimeRegiter = ResultRegister::where('id_user_student',$id)->distinct()->pluck('time_register')->toArray();

            $form->select('id_time_register', 'Thời gian')->options(TimeRegister::whereIn('id',$arrIdTimeRegiter)->orderBy('id', 'DESC')->pluck('name', 'id'))->attribute(['id' => 'resultRegister']);
            $form->disableReset();
            $form->disableSubmit();

        });
    }
    protected function grid()
    {
        return User::grid(ResultRegister::class, function (Grid $grid) {
            $user = Auth::user();
            $timeRegister = TimeRegister::orderBy('id', 'DESC')->first();
            $grid->model()->where('time_register', $timeRegister->id)->where('id_user_student', $user->id);
               // $grid->id('ID');
            $grid->column('Mã học phần')->display(function () {
                    $subjectRegister = SubjectRegister::where('id',$this->id_subject_register)->first();
                    if (!empty($subjectRegister)) {
                        return $subjectRegister->code_subject_register;
                    } else {
                        return '';
                    }
                });            
            $grid->id_subjects('Môn học')->display(function () {
                    $idSubject = $this->id_subject;
                    if (!empty($idSubject)) {
                        return Subjects::find($idSubject)->name;
                    } else {
                        return '';
                    }
                });
            $grid->column('Phòng')->display(function () {
                    $idClassroom = TimeStudy::where('id_subject_register', $this->id_subject_register)->pluck('id_classroom')->toArray();
                    $classRoom = Classroom::whereIn('id', $idClassroom)->pluck('name')->toArray();
                    $classRoom = array_map(function ($classRoom) {
                        return "<span class='label label-success'>{$classRoom}</span>";
                    }, $classRoom);
                    return join('&nbsp;', $classRoom);
                });
            $grid->column('Buổi học')->display(function () {
                    $day = TimeStudy::where('id_subject_register', $this->id_subject_register)->pluck('day')->toArray();
                    $day = array_map(function ($day) {
                        switch ($day) {
                            case 2:
                                $day = 'Thứ 2';
                                break;
                            case 3:
                                $day = 'Thứ 3';
                                break;
                            case 4:
                                $day = 'Thứ 4';
                                break;
                            case 5:
                                $day = 'Thứ 5';
                                break;
                            case 6:
                                $day = 'Thứ 6';
                                break;
                            case 7:
                                $day = 'Thứ 7';
                                break;
                            case 8:
                                $day = 'Chủ nhật';
                                break;
                        }

                        return "<span class='label label-success'>{$day}</span>";
                    }, $day);
                    return join('&nbsp;', $day);
                });
            $grid->column('Thời gian học')->display(function () {
                    $timeStart = TimeStudy::where('id_subject_register', $this->id_subject_register)->pluck('time_study_start')->toArray();
                    $timeEnd = TimeStudy::where('id_subject_register', $this->id_subject_register)->pluck('time_study_end')->toArray();
                    $time = array_map(function ($timeStart, $timeEnd) {
                        return "<span class='label label-success'>{$timeStart} - {$timeEnd}</span>";
                    }, $timeStart, $timeEnd);
                    return join('&nbsp;', $time);
                });
                // $grid->id_user_teacher('Giảng viên')->display(function ($id_user_teacher) {
                //     if ($id_user_teacher) {
                //         $teacher = UserAdmin::find($id_user_teacher);
                //         if ($teacher) {
                //             return $teacher->name;
                //         } else {
                //             return '';
                //         }
                //     } else {
                //         return '';
                //     }
                // });
            $grid->column('Giảng viên')->display(function () {
                    $idSubjectRegister = $this->id_subject_register;
                    $subjectRegister = SubjectRegister::where('id',$this->id_subject_register)->first();
                    if (!empty($subjectRegister)) {
                        $teacher = UserAdmin::find($subjectRegister->id_user_teacher);
                        if ($teacher) {
                            return $teacher->name;
                        } else {
                            return '';
                        }
                    } else {
                        return '';
                    }
                });
                // $grid->qty_current('Số lượng hiện tại');
                // $grid->qty_max('Số lượng tối đa');
                // $grid->date_start('Ngày bắt đầu');
                // $grid->date_end('Ngày kết thúc');

            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableFilter();
            if($timeRegister->status == 0) {
                $grid->disableActions();
            } 
            $grid->actions(function ($actions){
                $actions->disableEdit();
                $actions->disableDelete();
                $actions->append('<a href="javascript:void(0);" data-id="' . $this->row->id_subject_register . '"  class="btn btn-danger btnCancel" style="font-size: 1.5rem"><i class="glyphicon glyphicon-trash"></i> &nbsp Hủy bỏ </a>');
            });
            $cancel = trans('Hủy bỏ');
            $cancelConfirm = trans('Bạn có chắc chắn muốn hủy không?');
            $confirmDelete = trans('Hủy đăng ký');
            $script = <<<SCRIPT
$('.btnCancel').unbind('click').click(function() {
    var id = $(this).data('id');
    swal({
      title: "$cancelConfirm",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#dd4b39",
      confirmButtonText: "$confirmDelete",
      closeOnConfirm: false,
      cancelButtonText: "$cancel"
    },
    function(){
        $.ajax({
            method: 'get',
            url: '/user/subject-register/' + id + '/delete-register',
            data: {
                _method:'deleteRegister',
                _token:LA.token,
            },
            success: function (data) {
                if (typeof data === 'object') {
                    if (data.status) {
                         swal({
                              title: "Hủy thành công", 
                              type: "success"
                             },function() {
                              location.reload();
                             
                         });
                    } else {
                        swal(data.message, '', 'error');
                    }
                }
            }
        });
    });
});
SCRIPT;
                User::script($script);
        });
    }


}