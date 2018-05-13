<?php

namespace App\Admin\Controllers;

use App\Models\Classroom;
use App\Models\StudentUser;
use App\Models\SubjectRegister;

use App\Models\Subjects;
use App\Models\TimeRegister;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class SubjectRegisterController extends Controller
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
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(SubjectRegister::class, function (Grid $grid) {

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
                    return StudentUser::find($id_user_teacher)->name;
                } else {
                    return '';
                }
            });
            $grid->qty_current('Số lượng hiện tại');
            $grid->qty_min('Số lượng tối thiểu');
            $grid->qty_max('Số lượng tối đa');
            $grid->time_study_start('Giờ học bắt đầu');
            $grid->time_study_end('Giờ học kết thúc');
            $grid->date_start('Ngày bắt đầu');
            $grid->date_end('Ngày kết thúc');
            $grid->comlumn('Đợt đăng ký')->display(function (){
                if($this->id_time_register){
                    return TimeRegister::find($this->id_time_register)->name;
                } else {
                    return '';
                }
            });
            $grid->id_time_register('Thời hạn đăng ký')->display(function ($id_time_register){
                if($id_time_register){
                    return TimeRegister::find($id_time_register)->time_register_start .' -> '. TimeRegister::find($id_time_register)->time_register_end ;
                } else {
                    return '';
                }
            });
            $grid->created_at('Tạo vào lúc');
            $grid->updated_at('Cập nhật vào lúc');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(SubjectRegister::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('code_subject_register', 'Mã học phần');
            $form->select('id_subjects', 'Môn học')->options(Subjects::all()->pluck('name', 'id'));
            $form->select('id_classroom', 'Phòng học')->options(Classroom::all()->pluck('name', 'id'));
            $form->select('id_user_teacher', 'Giảng viên')->options(StudentUser::where('is_teacher', '1')->pluck('name', 'id'));
            $form->hidden('qty_current', 'Số lượng hiện tại')->value('0');
            $form->number('qty_min', 'Số lượng tối thiểu');
            $form->number('qty_max', 'Số lượng tối đa');
            $form->time('time_study_start', 'Giờ học bắt đầu');
            $form->time('time_study_end', 'Giờ học kết thúc');
            $form->date('date_start', 'Ngày bắt đầu')->placeholder('Ngày bắt đầu');
            $form->date('date_end', 'Ngày kết thúc')->placeholder('Ngày kết thúc');
            $form->select('id_time_register', 'Thời gian đăng ký')->options(TimeRegister::all()->pluck('name', 'id'));

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
