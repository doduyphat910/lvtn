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
use Illuminate\Support\MessageBag;

class StudentInformationController extends Controller
{
    use ModelForm;
    public function index()
    {
        return User::content(function (ContentUser $content) {

            $content->header('Thông tin cá nhân');
            $content->description('Cá nhân');
            $id = Auth::User()->id;
            $content->body($this->form()->edit($id));
        });
    }

    public function edit($id)
    {
        return User::content(function (ContentUser $content) use ($id) {

            $content->header('Thông tin cá nhân');
            $name = Auth::User()->last_name;
            $content->description($name);
            $content->body($this->form()->edit($id));
        });
    }


    protected function form()
    {
        return User::form(StudentUser::class, function (Form $form) {
            $form->registerBuiltinFields();
            $id = Auth::User()->id;
            $form->setAction('/user/information/'.$id);
            $form->text('code_number', 'Mã số SV')->readOnly();
            $form->text('first_name', 'Họ')->rules('required')->readOnly();
            $form->text('last_name', 'Tên')->rules('required')->readOnly();
            $form->email('email', 'Email');
            $form->password('password', 'Mật khẩu')->rules('required|confirmed');
            $form->password('password_confirmation', 'Xác nhận mật khẩu')->rules('required')
                ->default(function ($form) {
                    return $form->model()->password;
                });
            $form->ignore(['password_confirmation', 'id_class', 'school_year', 'level', 'code_number', 'first_name', 'last_name']);
            $form->image('avatar', 'Ảnh đại diện');

            $form->select('id_class', 'Lớp')->options(ClassSTU::all()->pluck('name', 'id'))->readOnly();
            $form->year('school_year', 'Năm nhập học')->readOnly();
            $form->select('level', 'Trình độ')->options(['CD'=>'Cao đẳng', 'DH'=>'Đại học'])->readOnly();
            $form->disableReset();
            $form->tools(function (Form\Tools $tools) {
                $tools->disableBackButton();
                $tools->disableListButton();
            });

            $form->saving(function (Form $form) {
                if($form->password != $form->password_confirmation) {
//                    $error = new MessageBag([
//                        'title'   => 'Lỗi',
//                        'message' => 'Mật khẩu và xác nhận mật khẩu khác nhau',
//                    ]);
                    return back()->with(compact('error'));
                }
            });
        });
    }
}
