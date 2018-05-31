<?php

namespace App\Http\Controllers;

use App\Models\ClassSTU;
use App\Models\Status;
use App\Models\StudentUser;

use App\Http\Extensions\Facades\User;
use app\Http\Extensions\LayoutUser\ContentUser;
use Illuminate\Http\Request;
use Encore\Admin\Grid;
use Encore\Admin\Form;
use Encore\Admin\Facades\Admin;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Auth;
use App\Http\Extensions\src\FormUser;

class StudentInformationController extends Controller
{
    use ModelForm;
    public function index()
    {
        return User::content(function (ContentUser $content) {

            $content->header('Thông tin cá nhân');
            $content->description('Cá nhân');
            $id = Auth::User()->id;
            $content->body($this->formEdit());
        });
    }

    public function edit2($id)
    {
        return User::content(function (StudentUser $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->formEdit()->edit($id));
        });
    }

    protected function grid()
    {
        return User::grid(StudentUser::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->avatar('Avatar')->image();
            $grid->first_name('Họ');
            $grid->last_name('Tên')->display(function ($name){
                return  '<a href="/admin/student_user/' . $this->id . '/details">'.$name.'</a>';
            });
            $grid->code_number('Mã số sinh viên');
            $grid->username('Tên đăng nhập');
            $grid->email('Email');
            $grid->id_class('Lớp')->display(function ($idClass){
                if($idClass){
                    return ClassSTU::find($idClass)->name;
                } else {
                    return 'Không có';
                }
            });
            $grid->school_year('Năm nhập học');
            $grid->level('Trình độ');
            $grid->created_at('Thêm vào lúc');
            $grid->updated_at('Cập nhật vào lúc');
            

        });
    }
    protected function form()
    {
        return User::form(StudentUser::class, function (Form $form) {
            $form->display('id', 'ID');

            $form->text('created_at', 'Created At');
            $form->text('updated_at', 'Updated At');
        });
    }
    public function formEdit()
    {
        return User::formUser(StudentUser::class, function (FormUser $form) {
            $form->display('id', 'ID');
            $form->display('code_number', 'Mã số SV');
            $form->text('first_name', 'Họ')->rules('required');
//            $form->text('last_name', 'Tên')->rules('required');
//            $form->email('email', 'Email');
//            $form->password('password', 'Password')->rules('required|confirmed');
//            $form->password('password_confirmation', 'Xác nhận password')->rules('required')
//                    ->default(function ($form) {
//                        return $form->model()->password;
//                    });
//            $form->ignore(['password_confirmation']);
//            $form->image('avatar', 'Avatar');
//            $form->select('id_class', 'Lớp')->options(ClassSTU::all()->pluck('name', 'id'));
//            $form->select('id_status', 'Trạng thái')->options(Status::all()->pluck('status', 'ids'));
//            $form->year('school_year', 'Năm nhập học');
//            $form->select('level', 'Trình độ')->options(['CD'=>'Cao đẳng', 'DH'=>'Đại học']);
            $form->display('created_at', 'Thêm vào lúc');
            $form->display('updated_at', 'Cập nhật vào lúc');
        });
    }
}
