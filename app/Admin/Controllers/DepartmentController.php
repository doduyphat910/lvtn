<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ModelFormCustom;
use App\Models\ClassSTU;
use App\Models\Department;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
//use Encore\Admin\Controllers\ModelForm;

class DepartmentController extends Controller
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
            $content->description('Danh sách khoa');

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

            $department = Department::findOrFail($id);
            $content->header('Khoa');
            $content->description($department->name);

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

            $content->header('Khoa');
            $content->description('Thêm khoa');

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
        return Admin::grid(Department::class, function (Grid $grid) {
            $grid->rows(function (Grid\Row $row) {
                $row->column('number', $row->number);
            });
            $grid->number('STT');
//            $grid->id('ID')->sortable();
            $grid->name('Tên khoa')->display(function ($name){
                return '<a href="/admin/department/' . $this->id . '/details">'.$name.'</a>';
            })->sortable();
            $grid->actions(function ($actions) {
//                $actions->disableDelete();
//                $flag = 1;
//                $actions->append('<a href="/admin/department/' . $actions->getKey().'/'.$flag .'"><i class="fa fa-eye"></i></a>');
                $actions->append('<a href="/admin/department/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->created_at('Tạo vào lúc')->sortable();
            $grid->updated_at('Cập nhật vào lúc')->sortable();
            $grid->filter(function ($filter){
                $filter->disableIdFilter();
                $filter->like('name', 'Tên');
                $filter->between('created_at', 'Tạo vào lúc')->datetime();
            });
            $grid->disableExport();
        });
    }


    protected function gridClass($idClass)
    {
        return Admin::grid(ClassSTU::class, function (Grid $grid) use ($idClass) {
            $grid->model()->whereIn('id', $idClass);
            $grid->id('ID')->sortable();
            $grid->name('Tên lớp')->display(function ($name){
                return '<a href="/admin/class/' . $this->id . '/details">'.$name.'</a>';
            });
            $grid->id_department('Tên khoa')->display(function ($idDepartment){
                if($idDepartment) {
                    return Department::find($idDepartment)->name;
                } else {
                    return '';
                }
            });
            $grid->actions(function ($actions) {
                $actions->append('<a href="/admin/class/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->created_at('Tạo vào lúc');
            $grid->updated_at('Cập nhật vào lúc');
            $grid->actions(function ($actions) {
                $actions->disableEdit();
                $actions->disableDelete();
                $actions->append('<a href="/admin/class/' . $actions->getKey() . '/edit"><i class="fa fa-edit" ></i></a>');
                $actions->append('<a href="/admin/class/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableFilter();

        });
    }
    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Department::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('name', 'Tên khoa')->rules(function ($form){
                return 'required|unique:department,name,'.$form->model()->id.',id';
            });
            $form->display('created_at', 'Tạo vào lúc');
            $form->display('updated_at', 'Cập nhật vào lúc');
            $form->disableReset();
        });
    }

    public function details($id){
        return Admin::content(function (Content $content) use ($id) {
            $department = Department::findOrFail($id);
            $content->header('Khoa');
            $content->description($department->name);
            $content->body($this->detailsView($id));
        });
    }

    public function detailsView($id) {
         $form = $this->form()->view($id);
         $idClass = ClassSTU::where('id_department', $id)->pluck('id');
         $gridClass = $this->gridClass($idClass)->render();
         return view('vendor.details',
             [
                 'template_body_name' => 'admin.Department.info',
                 'form' => $form,
                 'gridClass' => $gridClass
             ]
         );

    }
}
