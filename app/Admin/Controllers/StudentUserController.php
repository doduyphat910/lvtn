<?php

namespace App\Admin\Controllers;

use App\Models\ClassSTU;
use App\Models\Status;
use App\Models\StudentUser;

use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Role;
use Encore\Admin\Controllers\UserController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class StudentUserController extends Controller
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

            $content->header('Sinh viên');
            $content->description('Danh sách sinh viên');

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

            $content->header('Sinh viên');
            $content->description('Tạo sinh viên');

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
        return Admin::grid(StudentUser::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->avatar('Avatar')->image();
            $grid->name('Tên')->display(function ($name){
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
            //import student
            $grid->tools(function ($tools) {
                $tools->append("<a href='your-create-URI' class='btn btn-info btn-sm '><i class='fa fa-sign-in'></i> Import DS sinh viên</a>");
            });

        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        return Admin::form(StudentUser::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('name', 'Tên');
//            $form->text('username', 'Tên đăng nhập');
            $form->email('email', 'Email');
//            $form->password('password','Mật khẩu')->rules('required|confirmed');
//            $form->password('password_confirmation', 'Nhập lại mật khẩu')->rules('required')
//                ->default(function ($form) {
//                    return $form->model()->password;
//                });
            $form->hidden('username');
            $form->hidden('password');
//            $form->ignore(['password_confirmation']);
            $form->image('avatar', 'Avatar');
            $form->select('id_class', 'Lớp')->options(ClassSTU::all()->pluck('name', 'id'));
            $form->select('id_status', 'Trạng thái')->options(Status::all()->pluck('status', 'ids'));
            $form->year('school_year', 'Năm nhập học');

            $form->select('level', 'Trình độ')->options(['CD'=>'Cao đẳng', 'DH'=>'Đại học']);
            $form->hidden('code_number');
            $form->saving(function (Form $form) {
                $codeNumber = StudentUser::orderBy('code_number')->where('level', $form->level)->where('school_year', $form->school_year)
                    ->pluck('code_number');
                if(count($codeNumber) == 0) {
                    $count = 0;
                } else {
                    $count = substr($codeNumber['0'], strlen($codeNumber['0'])-1, 1);
                }
                $year = $form->school_year;
                $year = substr($year, 2, 2);
                $form->code_number = $form->level . '5'. $year. '00'. ((int)$count + 1);
                $form->username = $form->code_number;
                $form->password = $form->code_number;
            });
//            $form->saving(function (Form $form) {
//                if ($form->password && $form->model()->password != $form->password) {
//                    $form->password = bcrypt($form->password);
//                }
//            });

            $form->display('created_at', 'Thêm vào lúc');
            $form->display('updated_at', 'Cập nhật vào lúc');
        });
    }

    public function details($id){
        return Admin::content(function (Content $content) use ($id) {
            $studentUser = StudentUser::findOrFail($id);
            $content->header('Sinh viên');
            $content->description($studentUser->name);
            $content->body($this->detailsView($id));
        });
    }

    public function detailsView($id){
        $form = $this->form()->view($id);
        return view('vendor.details',
            [
                'template_body_name' => 'admin.StudentUser.info',
                'form' => $form,

            ]
        );
    }
}
