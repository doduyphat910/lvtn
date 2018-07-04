<?php

namespace App\Http\Controllers;

use App\Http\Extensions\Facades\User;
use app\Http\Extensions\LayoutUser\ContentUser;
use App\Models\ClassSTU;
use App\Models\Notifications;
use App\Models\StudentUser;
use Illuminate\Http\Request;
use Encore\Admin\Form;
//use Encore\Admin\Grid;
use App\Http\Extensions\GridUser;
use Encore\Admin\Facades\Admin;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use ModelForm;
    public function index()
    {
        return User::content(function (ContentUser $content) {

            $content->header('Thông báo');
            $content->description('Thông báo cho sinh viên');

            $content->body($this->grid());
        });
    }
    public function edit($id)
    {
        return User::content(function (ContentUser $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }
    protected function grid()
    {
        return User::gridUser(Notifications::class, function (GridUser $grid) {
            //$grid->id('ID')->sortable();
            $grid->name('Tên thông báo');
            $grid->description('Mô tả')->display(function ($name){
                return  '<a href="' . $this->url . '" target="_blank" >'.$name.'</a>';
            });
            //$grid->URL('Đường dẫn');
//            $grid->URL('Đường dẫn')->display(function ($name){
//                return  '<a href="' . $this->URL . '" target="_blank" >'.$name.'</a>';
//            });
            $grid->created_at('Tạo vào lúc');
            $grid->disableCreateButton();
            //$grid->disableCreateButton();
            $grid->disableActions();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableFilter();
        });
    }

    protected function form()
    {
        return Admin::form(StudentUser::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
    public function postlogin(Request $request)
   {
    $this->validate($request,[
        'code_number'=>'required',
        'password'=>'required|min:3|max:32'
    ], 
    [
        'code_number.required'=>'Bạn chưa nhập mã số sinh viên',
        'password.required'=>'Bạn chưa nhập mật khẩu',
        'password.min'=>'Password không được nhỏ hơn 3 ký tự',
        'password.max'=>'Password không được lớn hơn 5 ký tự'
       ]);

    if(Auth::attempt(['code_number'=>$request->code_number,'password'=>$request->password]))
        {
            return redirect('user/student');
        }
        else
        {
            return redirect('getLogin')->with('notification','Đăng nhập không thành công');
        }
   }
       public function logout() {
//            Auth::logout();
           Auth::guard('web')->logout();
           return redirect('getLogin');
       }
}
