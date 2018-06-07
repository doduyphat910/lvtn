<?php

namespace App\Http\Controllers;

use App\Models\ResultRegister;
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
use Illuminate\Support\MessageBag;

class SubjectRegisterController extends Controller
{
    use ModelForm;
    public function index()
    {
        return User::content(function (ContentUser $content) {

            $content->header('Đăng ký môn học');
            $content->description('Danh sách môn học');

            $content->body($this->grid());
        });
    }
    protected function grid()
    {
        return User::grid(Subjects::class, function (Grid $grid) {
             $timeRegister = TimeRegister::where('status', 1)->orderBy('id', 'DESC')->first();
             $nameSemester = $timeRegister->semester;
             $idSemester = Semester::where('name', $nameSemester)->pluck('id');
             $subjects_id = SemesterSubjects::whereIn('semester_id', $idSemester)->orderBy('semester_id', 'DESC')->pluck('subjects_id')->toArray();
             $field = '';
             foreach ($subjects_id as $id) {
                $field .= ($id . ',');
             }
             $field = substr( $field , 0, strlen($field)-1);
             $grid->model()->whereIn('id', $subjects_id)->orderBy(DB::raw('FIELD(id, '. $field .')'));
             $grid->subject_code('Mã môn học');
//             $grid->id('ID')->sortable();
             $grid->name('Tên môn học')->display(function ($name){
                 return  '<a href="/user/subject-register/' . $this->id . '/details"  target="_blank" >'.$name.'</a>';
             });

             // $grid->credits('Số tín chỉ');
             // $grid->credits_fee('Số tín chỉ học phí');
             $grid->column('Học kỳ - Năm')->display(function () {
                $id = $this->id;
                $subject = Subjects::find($id);
                $arraySemester = $subject->semester()->pluck('id')->toArray();
                $name = array_map( function ($arraySemester){
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
                    return "<span class='label label-info'>{$nameSemester} - {$nameYear}</span>"  ;
                }, $arraySemester);
                return join('&nbsp;', $name);});

           	$grid->disableExport();
            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableActions();
            $grid->disableFilter();
        });
    }
    protected function form()
    {
        return User::form(Subjects::class, function (Form $form) {
        	$form->registerBuiltinFields();
            // $form->text('subject_code', 'Mã môn học')->readOnly()->rules(function ($form){
            //     return 'required|unique:subjects,subject_code,'.$form->model()->id.',id';
            // });
            $form->text('name','Tên môn học')->rules('required')->readOnly();
            $form->number('credits','Tín chỉ')->rules('integer|min:1|max:6')->readOnly();
            // $form->number('credits_fee', 'Tín chỉ học phí')->rules('integer|min:1|max:12');
			//$form->select('id_semester', 'Học kỳ')->options(Semester::all()->pluck('name', 'id'));
            $form->multipleSelect('subject_group', 'Nhóm môn')->readOnly()->options(SubjectGroup::all()->pluck('name', 'id'))->rules('required');
            $form->disableReset();

            $form->tools(function (Form\Tools $tools) {
			    // Disable list btn
			    $tools->disableListButton();
			});
            // $rates = Rate::all();
            // $arrayRate = [];
            // foreach($rates as $rate) {
            //     $arrayRate += [$rate['id'] => $rate['attendance'] . '-'.  $rate['midterm'] .'-' .$rate['end_term']];
            // }
            // $form->select('id_rate', 'Tỷ lệ điểm')->options($arrayRate)->rules('required');
        });
    }
    protected function gridSubjectRegister($idSubjects)
    {
        return User::grid(SubjectRegister::class, function (Grid $grid) use ($idSubjects) {
            $grid->model()->where('id_Subjects', $idSubjects);
             $grid->id('ID')->sortable();
            $grid->code_subject_register('Mã học phần');
            $grid->id_subjects('Môn học')->display(function ($idSubject){
                if($idSubject){
                    return Subjects::find($idSubject)->name;
                } else {
                    return '';
                }
            });
            $grid->id_classroom('Phòng học')->display(function ($id_classroom){
                if($id_classroom){
                    return Classroom::find($id_classroom)->name;
                } else {
                    return '';
                }
            });
            $grid->id_user_teacher('Giảng viên')->display(function ($id_user_teacher){
                if($id_user_teacher){
                    $teacher = UserAdmin::find($id_user_teacher);
                    if($teacher){
                        return $teacher->name;
                    } else {
                        return '';
                    }
                } else {
                    return '';
                }
            });
            $grid->qty_current('Số lượng hiện tại');

            $grid->date_start('Ngày bắt đầu');
            $grid->date_end('Ngày kết thúc');

            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableFilter();
            $grid->actions(function ($actions) {
                $actions->disableEdit();
                $actions->disableDelete();
                // $actions->append('<a href="/admin/subject_register/' . $actions->getKey() . '/edit"><i class="fa fa-edit" ></i></a>');
                $actions->append('<a href="/user/subject-register/'.$actions->getKey(). '/result-register" class="btn btn-primary"><i class="glyphicon glyphicon-pencil"></i> &nbsp Đăng ký </a>');
            });
        });
    }
    public function details($id){
        return User::content(function (ContentUser $content) use ($id) {
            $subject = Subjects::findOrFail($id);
            $content->header('Môn học');
            $content->description($subject->name);
            $content->body($this->detailsView($id));
        });
    }
    public function detailsView($id){
        $form = $this->form()->view($id);
        $gridSubject_Register = $this->gridSubjectRegister($id)->render();
        return view('vendor.details',
            [
                'template_body_name' => 'User.SubjectRegister.info',
                'form' => $form,
                'gridSubjectRegister' => $gridSubject_Register

            ]
        );
    }
    public function resultRegister($idSubjecRegister){
        $user = Auth::user();
        $idUser = $user->id;
        $timeRegister = TimeRegister::where('status', 1)->orderBy('id', 'DESC')->first();
        $idTimeRegister = $timeRegister->id;

        $resultRegister = new ResultRegister();
        $resultRegister->id_user_student = $idUser;
        $resultRegister->id_subject_register = $idSubjecRegister;
        $resultRegister->is_learned = 0;
        $resultRegister->time_register = $idTimeRegister;
        if($resultRegister->save()) {
            $subjecRegister = SubjectRegister::find($idSubjecRegister);
            $qtyCurrent = $subjecRegister->qty_current;
            $subjecRegister->qty_current = $qtyCurrent + 1;
            if($subjecRegister->save()) {
                $success = new MessageBag([
                    'title'   => 'Thành công',
                    'message' => 'Bạn đã đăng ký thành công môn học này',
                ]);
                return back()->with(compact('success'));
            }else {
                $error = new MessageBag([
                    'title'   => 'Thất bại',
                    'message' => 'Đăng ký thất bại',
                ]);
                return back()->with(compact('error'));

            }
        }

    }
}
