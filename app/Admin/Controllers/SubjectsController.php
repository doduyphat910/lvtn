<?php

namespace App\Admin\Controllers;

use App\Models\Subjects;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\Semester;
use App\Models\SubjectGroup;

class SubjectsController extends Controller
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
        return Admin::grid(Subjects::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->name('Tên môn học');
            $grid->credits('Số tín chỉ');
            $grid->credits_fee('Số tín chỉ học phí');
            $grid->id_semester('Học kỳ')->display(function ($id) {
                return Semester::find($id)->name;
            });
            $grid->id_subject_group('Nhóm môn')->display(function ($id) {
                return SubjectGroup::find($id)->name;
            });
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
        return Admin::form(Subjects::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('name','Tên môn học');
            $form->number('credits','Tín chỉ');
            $form->number('credits_fee', 'Tín chỉ học phí');
            $form->select('id_semester', 'Học kỳ')->options(Semester::all()->pluck('name', 'id'));
            $form->select('id_subject_group', 'Nhóm môn')->options(SubjectGroup::all()->pluck('name', 'id'));
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
