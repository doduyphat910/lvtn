<?php

namespace App\Admin\Controllers;

use App\Models\Classroom;
use App\Models\ResultRegister;
use App\Models\SubjectRegister;
use App\Models\Subjects;
use App\Models\TimeRegister;
use App\Models\TimeStudy;
use App\Models\UserAdmin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class InformationRegisterPointController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function detail($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('Đăng ký môn học');
            $content->description('Danh sách môn học');
            $content->body(
                view('vendor.details',
                    [
                        'template_body_name' => 'admin.Teacher.ResultRegisterStudent.info',
                        'form' => $this->formTimeRegister($id),
                        'grid' => $this->grid($id),
                        'idUser' => $id
                    ])
            );
        });
    }
    protected function formTimeRegister($id)
    {
        return Admin::form(TimeRegister::class, function (Form $form) use ($id) {
            $arrIdTimeRegiter = ResultRegister::where('id_user_student',$id)->distinct()->pluck('time_register')->toArray();
            $options = TimeRegister::whereIn('id',$arrIdTimeRegiter)->orderBy('id', 'DESC')->pluck('name', 'id')->toArray();
            $form->select('id_time_register', 'Thời gian')->options($options)->attribute(['id' => 'resultRegisterStudent']);
            $form->disableReset();
            $form->disableSubmit();

        });
    }

    protected function grid($id)
    {
        return Admin::Grid(ResultRegister::class, function (Grid $grid) use ($id) {
            $timeRegister = ResultRegister::where('id_user_student', $id)->orderBy('time_register', 'DESC')->first()->time_register;
            $grid->model()->where('time_register', $timeRegister)->where('id_user_student', $id);
            // $grid->id('ID');
            $grid->column('Mã học phần')->display(function () {
                $subjectRegister = SubjectRegister::where('id',$this->id_subject_register)->first();
                if (!empty($subjectRegister)) {
                    return $subjectRegister->id;
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
            $grid->column('Giảng viên')->display(function () {
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
            $grid->column('Ngày bắt đầu')->display(function (){
                $idSubjectRegister = $this->id_subject_register;
                $subjectRegister = SubjectRegister::find($idSubjectRegister);
                if($subjectRegister->date_start){
                    return $subjectRegister->date_start;
                } else {
                    return '';
                }
            });
            $grid->column('Ngày kết thúc')->display(function (){
                $idSubjectRegister = $this->id_subject_register;
                $subjectRegister = SubjectRegister::find($idSubjectRegister);
                if($subjectRegister->date_end){
                    return $subjectRegister->date_end;
                } else {
                    return '';
                }
            });
            $grid->column('Sô tín chỉ hiện tại')->display(function () use ($id, $timeRegister){
                $idSubject = ResultRegister::where('id_user_student', $id)->where('time_register',  $timeRegister)->pluck('id_subject');
                $subjects = Subjects::find($idSubject);
                $sumCredit = 0;
                foreach ($subjects as $subject){
                    $sumCredit+=$subject->credits;
                }
                return $sumCredit;

            });
            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableFilter();
            $grid->disableActions();
        });
    }

    public function detailPoint($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header('Điểm');
            $content->description('Danh sách điểm');
            $content->body(
                view('vendor.details',
                    [
                        'template_body_name' => 'admin.Teacher.PointStudent.info',
                        'form' => $this->formTimeRegisterPoint($id),
                        'gridPoint' => $this->gridPoint($id),
                        'idUser' => $id
                    ])
            );
        });
    }

    protected function formTimeRegisterPoint($id)
    {
        return Admin::form(TimeRegister::class, function (Form $form) use ($id) {
            $arrIdTimeRegiter=ResultRegister::where('id_user_student',$id)->distinct()->pluck('time_register')->toArray();
            $options = ['Tất cả'];
            $options += TimeRegister::whereIn('id',$arrIdTimeRegiter)->orderBy('id', 'DESC')->pluck('name', 'id')->toArray();
            $form->select('id_time_register', 'Thời gian')->options($options)->attribute(['id' => 'resultPoint']);
            $form->disableReset();
            $form->disableSubmit();

        });
    }

    protected function gridPoint($id)
    {
        return Admin::Grid(ResultRegister::class, function (Grid $grid) use ($id) {

            $grid->model()->where('id_user_student', $id)->orderBy('time_register', 'DESC');
            $grid->column('Mã MH')->display(function(){
                $subjetRegister = Subjects::find($this->id_subject);
                if($subjetRegister->id) {
                    return $subjetRegister->id;
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

            $grid->column('Năm')->display(function () {
                $subject = TimeRegister::find($this->time_register);
                $id = $subject->id;
                if($id % 2 == 0)
                {
                    return "<span class='label label-info'>{$subject->name}</span>";
                } else {
                    return "<span class='label label-success'>{$subject->name}</span>";
                }
            });
            $grid->column('%QT')->display(function () {
                return $this->rate_attendance;
            });
            $grid->column('%GK')->display(function () {
                return $this->rate_mid_term;
            });
            $grid->column('%CK')->display(function () {
                return $this->rate_end_term;
            });
            $grid->column('Điểm QT')->display(function () {
                if(!empty($this->attendance))
                {
                    return $this->attendance;
                }
                else{ return "0"; }
            });
            $grid->column('Điểm GK')->display(function () {
                if(!empty($this->mid_term))
                {
                    return $this->mid_term;
                }
                else{ return "0"; }
            });
            $grid->column('Điểm CK')->display(function () {
                if(!empty($this->end_term))
                {
                    return $this->end_term;
                }
                else{ return "0"; }
            });
            $grid->column('Điểm TK')->display(function () {
                $final = (($this->attendance * $this->rate_attendance) +
                        ($this->mid_term * $this->rate_mid_term) +
                        ($this->end_term * $this->rate_end_term)) / 100;
                return "<b>{$final}</b>";
            });
            $grid->column('Kết quả')->display(function () {
                $final = (($this->attendance * $this->rate_attendance) +
                        ($this->mid_term * $this->rate_mid_term) +
                        ($this->end_term * $this->rate_end_term)) / 100;
                if($final < 5){
                    return "<b>X</b>";
                }
                else
                {
                    return "<b>Đạt</b>";
                }

            });
            $grid->column('Sô tín chỉ hiện tại')->display(function () use ($id){
                $idSubject = ResultRegister::where('id_user_student', $id)->pluck('id_subject');
                $subjects = Subjects::find($idSubject);
                $sumCredit = 0;
                foreach ($subjects as $subject){
                    $sumCredit+=$subject->credits;
                }
                return $sumCredit;

            });
            $grid->filter(function($filter){
                $filter->disableIdFilter();
                $filter->like('id', 'Mã môn học');
                $filter->where(function ($query){
                    $user = Auth::user();
                    $input = $this->input;
                    $arrSubject = Subjects::where('name', 'like', '%'.$input.'%')->pluck('id')->toArray();
                    $idResult = ResultRegister::where('id_user_student',$user->id )->whereIn('id_subject',$arrSubject)->pluck('id')->toArray();
                    $query->whereIn('id',$idResult);
                }, 'Tên môn học');
                $filter->where(function ($query){
                    $user = Auth::user();
                    $input = $this->input;
                    $arrSubject = Subjects::where('credits', 'like', '%'.$input.'%')->pluck('id')->toArray();
                    $idResult = ResultRegister::where('id_user_student',$user->id )->whereIn('id_subject',$arrSubject)->pluck('id')->toArray();
                    $query->whereIn('id',$idResult);
                }, 'Số tín chỉ');
                $filter->like('rate_attendance', '% Qúa trình');
                $filter->like('rate_mid_term', '% Giữa kỳ');
                $filter->like('rate_end_term', '% Cuối kỳ');
                $filter->like('attendance', 'Điểm quá trình');
                $filter->like('mid_term', 'Điểm giữa kỳ');
                $filter->like('end_term', 'Điểm cuối kỳ');
                $filter->where(function ($query)  {
                    $user = Auth::user();
                    $input = $this->input;
                    $idFinal = ResultRegister::where('id_user_student',$user->id )->whereRaw("((attendance *rate_attendance)+(mid_term*rate_mid_term)+(end_term*rate_end_term))/100 = ".$input)
                        ->pluck('id')->toArray();
                    $query->whereIn('id', $idFinal);
                }, 'Điểm TK');
            });
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableActions();
            $grid->disableFilter();
        });
    }
}
