<?php

namespace App\Admin\Controllers;

use App\Models\Point;

use App\Models\ResultRegister;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class PointController extends Controller
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
        return Admin::grid(ResultRegister::class, function (Grid $grid) {

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
        return Admin::form(ResultRegister::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->number('attendance', 'Điểm chuyên cần')->rules('min:0|max:10');
            $form->number('mid_term', 'Điểm giữa kì')->rules('min:0|max:10');
            $form->number('end_term', 'Điểm cuối kì')->rules('min:0|max:10');
            $form->number('final', 'Điểm tổng kết')->rules('numeric|min:0|max:10');
            $form->number('rate_attendance', 'Tỉ lệ điểm chuyên cần');
            $form->number('rate_mid_term', 'Tỉ lệ điểm giữa kì');
            $form->number('rate_end_term', 'Tỉ lệ điểm cuối kì');
            $form->hidden('is_learned');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
            $form->saving(function (Form $form){
                $form->is_learned = 1;
            });
        });
    }
}
