<?php

namespace App\Admin\Controllers;

use App\Models\Semester;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\Year;
class SemesterController extends Controller
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
        return Admin::grid(Semester::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->name('Tên');
            $grid->credits_max('Số tín chỉ lớn nhất');
            $grid->credits_min('Số tín chỉ nhỏ nhất');
            $grid->id_year('Tên năm')->display(function ($idyear) {
                return Year::find($idyear)->name;
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
        return Admin::form(Semester::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('name', 'Tên học kì');
            $form->select('id_year', 'Năm')->options(Year::all()->pluck('name', 'id'));
            $form->number('credits_max', 'Số tín chỉ lớn nhất');
            $form->number('credits_min', 'Số tín chỉ nhỏ nhất');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
