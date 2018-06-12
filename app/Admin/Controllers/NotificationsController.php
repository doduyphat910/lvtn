<?php

namespace App\Admin\Controllers;

use App\Models\Notifications;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class NotificationsController extends Controller
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

            $content->header('Thông báo');
            $content->description('Danh sách thông báo');

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

            $content->header('Thêm danh sách');
            $content->description('Danh sách');

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
        return Admin::grid(Notifications::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->name('Tên thông báo');
            $grid->description('Mô tả');
            //$grid->URL('Đường dẫn');
            $grid->URL('Đường dẫn')->display(function ($name){
                return  '<a href="' . $this->URL . '" >'.$name.'</a>';
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
        return Admin::form(Notifications::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('name','Tên thông báo');
            $form->text('description','Mô tả thông báo');
            $form->text('URL','Đường dẫn thông báo');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
