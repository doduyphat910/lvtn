<?php

namespace App\Admin\Controllers;

use App\Models\Semester;
use App\Models\Year;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class YearController extends Controller
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

            $content->header('Năm, học kỳ');
            $content->description('Danh sách năm');

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

            $content->header('header');
            $content->description('description');

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
        return Admin::grid(Year::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->name('Tên năm')->display(function ($name){
                return  '<a href="/admin/year/' . $this->id . '/details">'.$name.'</a>';
            });
            $grid->actions(function ($actions) {
                $actions->append('<a href="/admin/year/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->created_at('Tạo vào lúc');
            $grid->updated_at('Cập nhật vào lúc');
        });
    }

    protected function gridSemester($idYear)
    {
        return Admin::grid(Semester::class, function (Grid $grid) use ($idYear) {
            $grid->model()->where('id_year', $idYear);

            $grid->id('ID')->sortable();
            $grid->name('Tên')->display(function ($name){
                return  '<a href="/admin/semester/' . $this->id . '/details">'.$name.'</a>';
            });
            $grid->credits_max('Số tín chỉ lớn nhất');
            $grid->credits_min('Số tín chỉ nhỏ nhất');
            $grid->id_year('Tên năm')->display(function ($idyear) {
                return Year::find($idyear)->name;
            });
            $grid->actions(function ($actions) {
                $actions->append('<a href="/admin/semester/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->created_at();
            $grid->updated_at();
            //disable
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
        return Admin::form(Year::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('name', 'Tên năm');
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
