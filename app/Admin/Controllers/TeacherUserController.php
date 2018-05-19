<?php

namespace App\Admin\Controllers;

use App\Models\ClassSTU;
use App\Models\TeacherUser;

use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Role;
use Encore\Admin\Controllers\UserController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class TeacherUserController extends UserController
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
        return Admin::grid(TeacherUser::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->avatar('Avatar')->image();
            $grid->code_number('Mã số sinh viên');
            $grid->username('Tên đăng nhập');
            $grid->name('Tên');
            $grid->roles('Vai trò')->pluck('name')->label();
            $grid->email('Email');
            $grid->id_class('Lớp')->display(function ($idClass){
                if($idClass){
                    return ClassSTU::find($idClass)->name;
                } else {
                    return 'Không có';
                }
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
    public function form()
    {
        return Admin::form(TeacherUser::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('name', 'Tên');
            $form->text('username', 'Tên đăng nhập');
            $form->email('email', 'Email');
            $form->password('password','Mật khẩu')->rules('required|confirmed');
            $form->password('password_confirmation', 'Nhập lại mật khẩu')->rules('required')
                ->default(function ($form) {
                    return $form->model()->password;
                });
            $form->ignore(['password_confirmation']);
            $form->ignore(['password_confirmation']);
            $form->image('avatar', 'Avatar');
            $form->select('id_class', 'Class')->options(ClassSTU::all()->pluck('name', 'id'));
            $form->select('roles', 'Vai trò')->options(Role::all()->pluck('name', 'id'));
            $form->multipleSelect('permissions', 'Quyền')->options(Permission::all()->pluck('name', 'id'));
            $form->saving(function (Form $form) {
                if ($form->password && $form->model()->password != $form->password) {
                    $form->password = bcrypt($form->password);
                }
            });
            $form->hidden('code_number');
            $form->saving(function (Form $form) {
                $count = TeacherUser::get()->count();
                $form->code_number = 'GV' . '500'. ($count + 1);
            });

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
