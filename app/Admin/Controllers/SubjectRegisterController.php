<?php

namespace App\Admin\Controllers;

use App\Models\Classroom;
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

            $grid->created_at();
            $grid->updated_at();
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
            $form->select('id_subject', 'Môn học')->options(Subjects::all()->pluck('name', 'id'));
            $form->select('id_classroom', 'Phòng học')->options(Classroom::all()->pluck('name', 'id'));
            $form->number('qty_current', 'Số lượng hiện tại');
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
