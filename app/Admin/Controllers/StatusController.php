<?php

namespace App\Admin\Controllers;
use App\Models\Status;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class StatusController extends Controller
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
            $content->description('Danh sách trạng thái');

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
            $content->description('Tạo trạng thái');

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
        return Admin::grid(Status::class, function (Grid $grid) {

            $grid->ids('ID')->sortable();
            $grid->status('Trạng thái');
            $grid->created_at('Tạo vào lúc');
            $grid->updated_at('Cập nhật vào lúc');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Status::class, function (Form $form) {

            $form->text('ids', 'ID')->help('Chú ý: Nếu ID lớn hơn 5 thì sinh viên không được phép đăng ký')
                ->rules('required|unique:status');
            $form->text('status', 'Tên trạng thái');
            $form->display('created_at', 'Tạo vào lúc');
            $form->display('updated_at', 'Cập nhật vào lúc');
        });
    }
    public function details($id){
        return Admin::content(
            function (Content $content) use ($id) {
                $year = Year::findOrFail($id);
                $content->header('Năm');
                $content->description($year->name);
                $content->body($this->detailsView($id));
            });
    }
    public function detailsView($id) {
        $form = $this->form()->view($id);
        $gridSemester = $this->gridSemester($id)->render();
        return view('vendor.details',
            [
                'template_body_name' => 'admin.Year.info',
                'form' => $form,
                'gridSemester' => $gridSemester
            ]
        );

    }
}
