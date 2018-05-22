<?php

namespace App\Http\Controllers;

use App\Http\Extensions\Facades\User;
use app\Http\Extensions\LayoutUser\ContentUser;
use App\Models\ClassSTU;
use Illuminate\Http\Request;
use App\Models\Department;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use ModelForm;
    public function test()
    {
        return User::content(function (ContentUser $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->grid());
        });
    }

    protected function grid()
    {
        return User::grid(ClassSTU::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->created_at();
            $grid->updated_at();
        });
    }

    public function postlogin(Request $request)
   {
    // $this->validate($request,[
    //     'email'=>'required',
    //     'password'=>'required|min:3|max:32'
    // ], 
    // [
    //     'email.required'=>'Bạn chưa nhập email', 
    //     'password.required'=>'Bạn chưa nhập password',
    //     'password.min'=>'Password không được nhỏ hơn 3 ký tự',
    //     'password.max'=>'Password không được lớn hơn 5 ký tự'
    //    ]);

    if(Auth::attempt(['username'=>$request->username,'password'=>$request->password]))
        {
            return redirect('user/student');
        }
        else
        {
            return redirect('getLogin')->with('notification','Đăng nhập không thành công');
        }
   }
   public function logout() {
        Auth::logout();
        return redirect('admin/getLogin');
   }
}
