<?php

namespace App\Admin\Controllers;

use App\Models\ClassSTU;

use App\Models\Department;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ClassController extends Controller
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

            $content->header('Khoa, lớp');
            $content->description('Danh sách lớp');

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
        return Admin::form(ClassSTU::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('name', 'Tên lớp')->rules(function ($form){
                return 'required|unique:class,name,'.$form->model()->id.',id';
            });
            $form->select('id_department', 'Tên khoa')->options(Department::all()->pluck('name', 'id'))->rules('required');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
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
        return view('vendor.details',
            [
                'template_body_name' => 'admin.Class.info',
                'form' => $form,
            ]
        );

    }
}
