<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ModelFormCustom;
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
    use ModelFormCustom;

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

            $year = Year::findOrFail($id);
            $content->header('Năm');
            $content->description($year->name);

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

            $content->header('Năm, học kỳ');
            $content->description('Thêm năm');

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
//            $grid->rows(function (Grid\Row $row) {
//                $row->column('number', $row->number);
//            });
//            $grid->number('STT');
            $grid->name('Tên năm')->display(function ($name){
                return  '<a href="/admin/year/' . $this->id . '/details">'.$name.'</a>';
            })->sortable();

            $grid->actions(function ($actions) {
                $actions->append('<a href="/admin/year/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->created_at('Tạo vào lúc')->sortable();
            $grid->updated_at('Cập nhật vào lúc')->sortable();
            $grid->filter(function($filter) {
                $filter->disableIdFilter();
                $filter->like('name', 'Tên năm');
                $filter->between('created_at', 'Tạo vào lúc')->datetime();
            });
            $grid->disableExport();
        });
    }

    protected function gridSemester($idYear)
    {
        return Admin::grid(Semester::class, function (Grid $grid) use ($idYear) {
            $grid->model()->where('id_year', $idYear);

            $grid->id('ID')->sortable();
            $grid->name('Tên')->display(function ($name){
                switch ($name){
                    case 0:
                        $name = 'Học kỳ hè';
                        break;
                    case 1:
                        $name = 'Học kỳ 1';
                        break;
                    case 2:
                        $name = 'Học kỳ 2';
                        break;
                    default:
                        $name = '';
                        break;
                }
                return  '<a href="/admin/semester/' . $this->id . '/details">'.$name.'</a>';
            });
            $grid->id_year('Tên năm')->display(function ($idyear) {
                return Year::find($idyear)->name;
            });
//            $grid->time_start('Thời gian bắt đầu');
//            $grid->time_end('Thời gian kết thúc');
            $grid->actions(function ($actions) {
                $actions->disableEdit();
                $actions->disableDelete();
                $actions->append('<a href="/admin/semester/' . $actions->getKey() . '/edit"><i class="fa fa-edit" ></i></a>');
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
            $options = ['Năm 1'=>'Năm 1', 'Năm 2'=>'Năm 2', 'Năm 3'=>'Năm 3', 'Năm 4'=>'Năm 4', 'Năm 5'=>'Năm 5', 'Năm 6'=>'Năm 6' ];
            $form->select('name', 'Tên năm')->options($options)->rules(function ($form){
                return 'required|unique:year,name,'.$form->model()->id.',id';
            });
            $form->display('created_at', 'Tạo vào lúc');
            $form->display('updated_at', 'Cập nhật vào lúc');
            $form->disableReset();
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
