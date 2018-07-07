<?php
namespace App\Admin\Controllers;
use App\Models\Rate;

use App\Models\ResultRegister;
use App\Models\Semester;
use App\Models\StudentUser;
use App\Models\SubjectGroup;
use App\Models\SubjectRegister;
use App\Models\Subjects;
use App\Models\TimeRegister;
use App\Models\UserSubject;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\MessageBag;

class RegisterHistoryController extends Controller
{
    use ModelForm;

    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('Học phần');
            $content->description('Lịch sử ĐKMH');

            $content->body($this->grid());
        });
    }

    protected function grid()
    {
        return Admin::grid(ResultRegister::class, function (Grid $grid) {
            $grid->model()->orderBy('created_at', 'DESC');
            $grid->rows(function (Grid\Row $row) {
                $row->column('number', $row->number);
            });
            $grid->number('STT');
            $grid->column('Họ SV')->display(function (){
                $user = StudentUser::find($this->id_user_student);
                if($user->first_name){
                    return $user->first_name;
                } else {
                    return '';
                }
            })->sortable();
            $grid->id_user_student('Tên SV')->display(function ($idUser){
                $user = StudentUser::find($idUser);
                if($user->last_name){
                    return $user->last_name;
                } else {
                    return '';
                }
            })->sortable();
            $grid->id_subject_register('Mã HP')->display(function ($idSubjectRegister){
                $subjectRegister = SubjectRegister::find($idSubjectRegister);
                if(!empty($subjectRegister->id)) {
                    return $subjectRegister->id;
                } else {
                    return '';
                }
            });
            $grid->id_subject('Môn học')->display(function ($idSubject) {
                $subject = Subjects::find($idSubject);
                if(!empty($subject->name)){
                    return $subject->name;
                } else {
                    return '';
                }
            })->sortable();
            $grid->column('Đợt ĐK')->display(function (){
                $timeRegister = TimeRegister::find($this->time_register);
                if($timeRegister->name){
                    if($this->time_register % 2 == 0 ) {
                        return "<span class='label label-info'>{$timeRegister->name}</span>";
                    } else {
                        return "<span class='label label-success'>{$timeRegister->name}</span>";
                    }
                } else {
                    return '';
                }
            })->sortable();
//            $grid->time_register('Đợt ĐK');
            $grid->created_at('Tạo vào lúc')->sortable();
            $grid->updated_at('Cập nhật vào lúc')->sortable();
            $grid->filter(function($filter) {
                $filter->disableIdFilter();
                $filter->like('first_name', 'Họ SV');
                $filter->like('last_name', 'Tên SV');
                $filter->in('id_subject_register', 'Mã HP')->multipleSelect(SubjectRegister::all()->pluck('id','id'));
                $filter->in('id_subject', 'Môn học')->multipleSelect(Subjects::all()->pluck('name','id'));
                $filter->in('time_register', 'Đợt ĐK')->select(TimeRegister::all()->pluck('name','id'));
                $filter->between('created_at', 'Tạo vào lúc')->datetime();
            });
            $grid->disableCreateButton();
            $grid->disableActions();
            $grid->disableExport();
        });
    }
}