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

class SubjectRegisterController extends Controller
{
    use ModelForm;

    public function index()
    {
        return User::content(function (ContentUser $content) {

            $content->header('Đăng ký môn học');
            $content->description('Danh sách môn học');
            $content->breadcrumb(
                ['text' => 'Đăng kí môn học', 'url' => '../user/subject-register']
            );
            $content->body($this->grid());
        });
    }

    protected function grid()
    {
        return User::grid(Subjects::class, function (Grid $grid) {
            $grid->registerColumnDisplayer();
            $user = Auth::user();
            $schoolYearUser = $user->school_year;
            //check school year
            $timeRegister = TimeRegister::where('status', 1)->orderBy('id', 'DESC')->first();
//             $schoolYearUser = (string) $schoolYearUser;
            if ($timeRegister) {
//                 //get school year in time register and school year = "ALL"
//                 if(in_array($schoolYearUser, $timeRegister->school_year) || $timeRegister->school_year['0'] == "All") {
//                 } else {
//                     $grid->model()->where('id', -1);
//                 }

                //open subject register follow semester
                $nameSemester = $timeRegister->semester;
                $idSemester = Semester::where('name', $nameSemester)->pluck('id');
                $subjects_id = SemesterSubjects::whereIn('semester_id', $idSemester)->orderBy('semester_id', 'DESC')->pluck('subjects_id')->toArray();
                //sort follow semester
                $field = '';
                foreach ($subjects_id as $id) {
                    $field .= ($id . ',');
                }
                $field = substr($field, 0, strlen($field) - 1);
                //get subject user learned
                $idSubjectRegister = ResultRegister::where('id_user_student', $user->id)->where('is_learned', 1)->pluck('id_subject_register')->toArray();
                $idSubjectLearned = SubjectRegister::whereIn('id', $idSubjectRegister)->pluck('id_subjects')->toArray();
                //show subject not learned and subjects in semester in time register (hiển thị các môn chưa học & trong đợt đăng kí đang mở)
                $grid->model()->whereIn('id', $subjects_id)->whereNotIn('id', $idSubjectLearned)->orderBy(DB::raw('FIELD(id, ' . $field . ')'));
            }
            //$grid->id('id');
            $grid->subject_code('Mã môn học');
            $grid->name('Tên môn học')->display(function ($name) {
                return '<a href="/user/subject-register/' . $this->id . '/details"  target="_blank" >' . $name . '</a>';
            });

            $grid->credits('Số tín chỉ');
            $grid->credits_fee('Số tín chỉ học phí');
            $grid->column('Nhóm môn')->display(function () {
                if ($this->id_subject_group) {
                    if (SubjectGroup::find($this->id_subject_group)) {
                        $nameGroup = SubjectGroup::find($this->id_subject_group)->name;
                        return $nameGroup;
                    } else {
                        return '';
                    }
                } else {
                    return '';
                }
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
            $grid->column('Đăng ký')->display(function () {
                return '<a href="/user/subject-register/' . $this->id . '/details"  target="_blank" class="btn btn-md" ><i class="glyphicon glyphicon-pencil"></i></a>';
            });

            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();

            $grid->disableActions();
        });
    }

    protected function form()
    {
        return User::form(Subjects::class, function (Form $form) {
            $form->registerBuiltinFields();
            $form->text('name', 'Tên môn học')->rules('required')->readOnly();
            $form->number('credits', 'Tín chỉ')->rules('integer|min:1|max:6')->readOnly();
            $form->multipleSelect('subject_group', 'Nhóm môn')->readOnly()->options(SubjectGroup::all()->pluck('name', 'id'))->rules('required');
            $form->disableReset();

            $form->tools(function (Form\Tools $tools) {
                $tools->disableListButton();
                $tools->disableBackButton();
            });
        });
    }

    protected function gridSubjectRegister($idSubjects)
    {
        return User::grid(SubjectRegister::class, function (Grid $grid) use ($idSubjects) {
            $grid->model()->where('id_Subjects', $idSubjects);
//            $grid->id('ID');
            $grid->code_subject_register('Mã học phần');
            $grid->id_subjects('Môn học')->display(function ($idSubject) {
                if ($idSubject) {
                    return Subjects::find($idSubject)->name;
                } else {
                    return '';
                }
            });
            $grid->column('Phòng')->display(function () {
                $idClassroom = TimeStudy::where('id_subject_register', $this->id)->pluck('id_classroom')->toArray();
                $classRoom = Classroom::whereIn('id', $idClassroom)->pluck('name')->toArray();
                $classRoom = array_map(function ($classRoom) {
                    return "<span class='label label-success'>{$classRoom}</span>";
                }, $classRoom);
                return join('&nbsp;', $classRoom);
            });
            $grid->column('Buổi học')->display(function () {
                $day = TimeStudy::where('id_subject_register', $this->id)->pluck('day')->toArray();
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
                $timeStart = TimeStudy::where('id_subject_register', $this->id)->pluck('time_study_start')->toArray();
                $timeEnd = TimeStudy::where('id_subject_register', $this->id)->pluck('time_study_end')->toArray();
                $time = array_map(function ($timeStart, $timeEnd) {
                    return "<span class='label label-success'>{$timeStart} - {$timeEnd}</span>";
                }, $timeStart, $timeEnd);
                return join('&nbsp;', $time);
            });
            $grid->id_user_teacher('Giảng viên')->display(function ($id_user_teacher) {
                if ($id_user_teacher) {
                    $teacher = UserAdmin::find($id_user_teacher);
                    if ($teacher) {
                        return $teacher->name;
                    } else {
                        return '';
                    }
                } else {
                    return '';
                }
            });
            $grid->qty_current('Số lượng hiện tại');
            $grid->qty_max('Số lượng tối đa');
            $grid->date_start('Ngày bắt đầu');
            $grid->date_end('Ngày kết thúc');

            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableFilter();
            $grid->actions(function ($actions) use ($idSubjects){
                $actions->disableEdit();
                $actions->disableDelete();
                $actions->append('<a href="javascript:void(0);" data-id="' . $this->getKey() . '"  class="btn btn-danger btnCancel" style="display: none;font-size: 1.5rem"><i class="glyphicon glyphicon-trash"></i> &nbsp Hủy bỏ </a>');
//
//                $arrIdSubjectsList=SubjectRegister::where('id_subjects',$idSubjects)->pluck('id')->toArray();
//                $arrIdResultRegister=ResultRegister::get()->pluck('id_subject_register')->toArray();
                $user = Auth::user();
                $idUser = $user->id;
                $timeRegister = TimeRegister::where('status', 1)->orderBy('id', 'DESC')->first();
                $idTimeRegister = $timeRegister->id;
                $idSubjectsList=ResultRegister::where('id_subject',$idSubjects)->where('id_user_student', $idUser)->where('time_register', $idTimeRegister)->first();

//                dd($arrIdSubjectsList);
//                foreach ($arrIdResultRegister as $valueResultRegisters){
//                    foreach ($arrIdSubjectsList as $valueSubjectRegisters){
//                        if($valueResultRegisters==$valueSubjectRegisters)
//                        {
//                            $valueCK = $valueResultRegisters;
                if($idSubjectsList) {
                    $script = <<<SCRIPT
                             $('.btnRegister').each(function(){
                                var idRegister =$(this).data('id');
                                if(idRegister == $idSubjectsList->id_subject_register) {
//                                    $('[data-id='+idRegister+']').hide();
                                    $('.btnCancel[data-id='+idRegister+']').css("display", "initial");
//                                 $('.btnCancel').find('a[data-id='+idRegister+']').css("display", "initial");
                                }
                                else {
                                    $('.btnRegister[data-id='+idRegister+']').css("display", "initial");
                                }
                             });
SCRIPT;
                    User::script($script);
                } else {
                    $script = <<<SCRIPT
                                    $('.btnRegister').css("display", "initial");

SCRIPT;
                    User::script($script);

                }

//                        }
//                    }
//                }

                //button Register (nút đăng kí)
                $actions->append('<a href="javascript:void(0);" data-id="' . $this->getKey() . '"  class="btn btn-primary btnRegister" style="display: none;" ><i class="glyphicon glyphicon-pencil"></i> &nbsp Đăng ký </a>');
                //$actions->append('<a href="javascript:void(0);" data-id="' . $this->getKey() . '"  class="btn btn-primary btnRegister"><i class="glyphicon glyphicon-pencil"></i> &nbsp Đăng ký </a>');

            });

            $registerConfirm = trans('Bạn có chắc chắn muốn đăng ký không?');
            $confirm = trans('Đăng ký');
            $cancel = trans('Hủy bỏ');
            $cancelConfirm = trans('Bạn có chắc chắn muốn hủy không?');
            $confirmDelete = trans('Hủy đăng ký');
//            $cancel = trans('Hủy bỏ');

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






$('.btnRegister').unbind('click').click(function() {
    var id = $(this).data('id');
    swal({
      title: "$registerConfirm",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3c8dbc",
      confirmButtonText: "$confirm",
      closeOnConfirm: false,
      cancelButtonText: "$cancel"
    },
    function(){
        $.ajax({
            method: 'get',
            url: '/user/subject-register/' + id + '/result-register',
            data: {
                _method:'resultRegister',
                _token:LA.token,
            },
            success: function (data) {
                if (typeof data === 'object') {
                    if (data.status) {
                         swal({
                              title: "Đăng ký thành công", 
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

    public function details($id)
    {
        return User::content(function (ContentUser $content) use ($id) {
            $subject = Subjects::findOrFail($id);
            $content->header('Môn học');
            $content->description($subject->name);
            $content->breadcrumb(
                ['text' => 'Đăng kí môn học', 'url' => '../user/subject-register'],
                ['text' => $subject->name, 'url' => '../user/subject-register/'.$id.'/deltails']
            );
            $content->body($this->detailsView($id));
        });
    }

    public function detailsView($id)
    {
//        $form = $this->form()->view($id);
        $gridSubject_Register = $this->gridSubjectRegister($id)->render();
        return view('vendor.details',
            [
                'template_body_name' => 'User.SubjectRegister.info',
//                'form' => $form,
                'gridSubjectRegister' => $gridSubject_Register

            ]
        );
    }

    public function timetable()
    {

        return view('User.SubjectRegister.timetable');
    }
}
