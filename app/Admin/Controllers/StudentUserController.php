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
use Illuminate\Support\Facades\Route;

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

            $content->body($this->formEdit()->edit($id));
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
            $grid->rows(function (Grid\Row $row) {
                $row->column('number', $row->number);
            });
            $grid->number('STT');
//            $grid->id('ID')->sortable();
//            $grid->avatar('Avatar')->image();
            $grid->code_number('Mã số sinh viên')->sortable();
            $grid->first_name('Họ')->sortable();
            $grid->last_name('Tên')->display(function ($name){
                return  '<a href="/admin/student_user/' . $this->id . '/details">'.$name.'</a>';
            })->sortable();
//            $grid->username('Tên đăng nhập');
            $grid->email('Email');
            $grid->id_class('Lớp')->display(function ($idClass){
                if($idClass){
                    return ClassSTU::find($idClass)->name;
                } else {
                    return 'Không có';
                }
            })->sortable();
            $grid->school_year('Năm nhập học')->sortable();
            $grid->level('Trình độ')->sortable();
            $grid->created_at('Thêm vào lúc')->sortable();
            $grid->updated_at('Cập nhật vào lúc')->sortable();
            //import student
            $grid->tools(function ($tools) {
                $tools->append("<a href='/admin/import_student' class='btn btn-info btn-sm '><i class='fa fa-sign-in'></i> Import DS sinh viên</a>");
            });
            $grid->actions(function ($actions) {
                $actions->append('<a href="/admin/student_user/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->filter(function ($filter){
                $filter->disableIdFilter();
                $filter->like('code_number', 'MSSV');
                $filter->like('first_name', 'Họ');
                $filter->like('last_name', 'Tên');
                $filter->like('email', 'Email');
                $filter->in('id_class', 'Lớp')->select(ClassSTU::all()->pluck('name','id'));
                $filter->equal('school_year', 'Năm nhập học')->year();
                $filter->in('level', 'Trình độ')->radio(['CD'=>'Cao đẳng', 'DH'=>'Đại học']);
                $filter->between('created_at', 'Tạo vào lúc')->datetime();
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
            $form->text('first_name', 'Họ')->rules('required');
            $form->text('last_name', 'Tên')->rules('required');
            $form->text('code_number', 'Mã số SV')->rules(function ($form){
                return 'required|min:10|unique:student_user,code_number,'.$form->model()->id.',id';
            });
            $form->email('email', 'Email');
            $form->hidden('password');
            $form->saving(function (Form $form) {
                $form->password = $form->code_number;
            });
//            $form->image('avatar', 'Avatar');
            $form->select('id_class', 'Lớp')->options(ClassSTU::all()->pluck('name', 'id'))->rules('required');
            $form->select('id_status', 'Trạng thái')->options(Status::all()->pluck('status', 'ids'))->rules('required');
            $form->year('school_year', 'Năm nhập học')->rules('required');
            $form->select('level', 'Trình độ')->options(['CD'=>'Cao đẳng', 'DH'=>'Đại học'])->rules('required');
            $form->display('created_at', 'Thêm vào lúc');
            $form->display('updated_at', 'Cập nhật vào lúc');
        });
    }

    public function formEdit()
    {
        return Admin::form(StudentUser::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->display('code_number', 'Mã số SV');
            $form->text('first_name', 'Họ')->rules('required');
            $form->text('last_name', 'Tên')->rules('required');
            $form->email('email', 'Email');
            $form->password('password', 'Password')->rules('required|confirmed');
            $form->password('password_confirmation', 'Xác nhận password')->rules('required')
                    ->default(function ($form) {
                        return $form->model()->password;
                    });
            $form->ignore(['password_confirmation']);
            $form->image('avatar', 'Avatar');
            $form->select('id_class', 'Lớp')->options(ClassSTU::all()->pluck('name', 'id'));
            $form->select('id_status', 'Trạng thái')->options(Status::all()->pluck('status', 'ids'));
            $form->year('school_year', 'Năm nhập học');
            $form->select('level', 'Trình độ')->options(['CD'=>'Cao đẳng', 'DH'=>'Đại học']);
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
