<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Rate\AdminRateFacades;
use App\Admin\Extensions\Rate\FormRate;
use App\Models\Rate;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class RateController extends Controller
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
        return Admin::grid(Rate::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->name('Tỷ lệ');
            $grid->attendance('Chuyên cần');
            $grid->midterm('Giữa kì');
            $grid->end_term('Cuối kì');
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
        return AdminRateFacades::form(Rate::class, function (FormRate $form) {
            $form->display('id', 'ID');
            $form->text('name', 'Tên tỷ lệ')->rules('required');
            $form->number('attendance', 'Tỉ lệ điểm chuyên cần')->rules('integer|max:20')->rules('integer|min:0');
            $form->number('midterm', 'Tỉ lệ điểm giữa kì')->rules('integer|max:50')->rules('integer|min:0');
            $form->number('end_term', 'Tỉ lệ điểm cuối kì')->rules('integer|max:100')->rules('integer|min:50');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
