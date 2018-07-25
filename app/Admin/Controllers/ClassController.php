<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ModelFormCustom;
use App\Models\ClassSTU;

use App\Models\Department;
use App\Models\StudentUser;
use App\Models\UserAdmin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ClassController extends Controller
{
    use ModelFormCustom;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('Khoa, lớp');
            $content->description('Danh sách lớp');
            header("Content-Type: text/html; charset=UTF-8");
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

            $class = ClassSTU::findOrFail($id);
            $content->header('Lớp');
            $content->description($class->name);

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

            $content->header('Lớp');
            $content->description('Thêm lớp');

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
        return Admin::grid(ClassSTU::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
//            $grid->rows(function (Grid\Row $row) {
//                $row->column('number', $row->number);
//            });
//            $grid->number('STT');
            $grid->name('Tên lớp')->display(function ($name){
                return '<a href="/admin/class/' . $this->id . '/details">'.$name.'</a>';
            })->sortable();
            $grid->id_user_teacher('GV cố vấn')->display(function ($idTeacher){
                if(!empty($idTeacher)){
                    $nameTeacher = UserAdmin::find($idTeacher);
                    if(!empty($nameTeacher)) {
                        return "<span class='label label-success'>{$nameTeacher->name}</span>";
                    } else {
                        return '';
                    }
                } else {
                    return $idTeacher = '';
                }
            })->sortable();
            $grid->id_department('Tên khoa')->display(function ($idDepartment){
                if($idDepartment) {
                    return Department::find($idDepartment)->name;
                } else {
                    return '';
                }
            })->sortable();
            $grid->actions(function ($actions) {
                $actions->append('<a href="/admin/class/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->created_at('Tạo vào lúc')->sortable();
            $grid->updated_at('Cập nhật vào lúc')->sortable();
            $grid->filter(function ($filter){
                $filter->disableIdFilter();
                $filter->like('name', 'Tên');
                $filter->in('id_user_teacher', 'GV cố vấn')->multipleSelect(UserAdmin::where('type_user',0)->pluck('name', 'id'));
                $filter->in('id_department', 'Tên khoa')->select(Department::all()->pluck('name','id'));
                $filter->between('created_at', 'Tạo vào lúc')->datetime();
            });
            $grid->disableExport();
        });
    }

    protected function gridStudent($idClass)
    {
        return Admin::grid(StudentUser::class, function (Grid $grid) use ($idClass) {
            $grid->model()->where('id_class', $idClass);
//            $grid->rows(function (Grid\Row $row) {
//                $row->column('number', $row->number);
//            });
//            $grid->number('STT');
//            $grid->id('ID')->sortable();
//            $grid->avatar('Avatar')->image();
            $grid->code_number('Mã số sinh viên')->sortable();
            $grid->first_name('Họ')->sortable();
            $grid->last_name('Tên')->display(function ($name){
                return  '<a href="/admin/student_user/' . $this->id . '/details">'.$name.'</a>';
            })->sortable();
            $grid->email('Email')->sortable();
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
//            $grid->tools(function ($tools) {
//                $tools->append("<a href='/admin/import_student' class='btn btn-info btn-sm '><i class='fa fa-sign-in'></i> Import DS sinh viên</a>");
//            });
            $grid->filter(function ($filter){
                $filter->disableIdFilter();
                $filter->like('code_number', 'MSSV');
                $filter->like('first_name', 'Họ');
                $filter->like('last_name', 'Tên');
                $filter->like('email', 'Email');
                $filter->equal('school_year', 'Năm nhập học')->year();
                $filter->in('level', 'Trình độ')->radio(['CD'=>'Cao đẳng', 'DH'=>'Đại học']);
                $filter->between('created_at', 'Tạo vào lúc')->datetime();
            });
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();
        });
    }
    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(ClassSTU::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('name', 'Tên lớp')->rules(function ($form){
                return 'required|unique:class,name,'.$form->model()->id.',id';
            });
            $form->select('id_department', 'Tên khoa')->options(Department::all()->pluck('name', 'id'))->rules('required');
            $form->select('id_user_teacher', 'GV cố vấn')->options(UserAdmin::where('type_user', 0)->pluck('name', 'id'));
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
            $form->disableReset();
        });
    }

    public function details($id){
        return Admin::content(
            function (Content $content) use ($id) {
            $class = ClassSTU::findOrFail($id);
            $content->header('Lớp');
            $content->description($class->name);
            $content->body($this->detailsView($id));
        });
    }

    public function detailsView($id) {
        $form = $this->form()->view($id);
        $gridStudent = $this->gridStudent($id);
        return view('vendor.details',
            [
                'template_body_name' => 'admin.Class.info',
                'form' => $form,
                'gridStudent' => $gridStudent
            ]
        );

    }
}
