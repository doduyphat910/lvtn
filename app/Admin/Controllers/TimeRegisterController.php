<?php

namespace App\Admin\Controllers;

use App\Models\TimeRegister;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\Semester;

class TimeRegisterController extends Controller
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
        return Admin::grid(TimeRegister::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->name('Mô tả');
            $grid->time_register_start('Thời gian bắt đầu');
            $grid->time_register_end('Thời gian kết thúc');
            $grid->id_semester('Học kỳ');
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
        return Admin::form(TimeRegister::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('name', 'Mô tả');
            $form->datetimeRange('time_register_start', 'time_register_end', 'Thời gian đăng ký')->attribute(['data-date-min-date' => date("Y-m-d")])
                ->rules('required');
            $form->select('id_semester', 'Học kỳ')->options(Semester::all()->pluck('name', 'id'));
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');

            //->dateRange('from_date', 'to_date', 'Date')->attribute(['data-date-min-date' => date("Y-m-d")])
            //                    ->rules('required');
        });
    }
}
