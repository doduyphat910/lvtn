<?php

namespace App\Admin\Controllers;

use App\Models\CSVData;
use App\Models\UserAdmin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ImportHistoryController extends Controller
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

            $content->header('Lịch sử nhập');
            $content->description('DS file nhập');

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
        return Admin::grid(CSVData::class, function (Grid $grid) {
            $grid->model()->orderBy('created_at', 'DESC');
            $grid->rows(function (Grid\Row $row) {
                $row->column('number', $row->number);
            });
            $grid->number('STT');
            $grid->id('ID');
            $grid->file_name('Tên file');
            $grid->column('Tên model')->display(function (){
                switch ($this->model){
                    case 'mid_term_point':
                        return 'Điểm giữa kì';
                        break;
                    case 'attendance_point':
                        return 'Điểm chuyên cần';
                        break;
                    case 'end_term_point':
                        return 'Điểm cuối kì';
                        break;
                    case 'all_point':
                        return 'Điểm';
                        break;
                    case 'student_user':
                        return 'Sinh viên';
                        break;
                    default:
                        return 'Khác';
                }
            });
            $grid->id_user('Người nhập')->display(function ($idUser){
                $user = UserAdmin::find($idUser);
                if(!empty($user->name)){
                    return '<span class="label label-success"> '.$user->name.'</span>';
                } else {
                    return '';
                }
            });
            $grid->created_at('Tạo vào lúc');
//            $grid->updated_at('Cập nhật vào lúc');
            $grid->actions(function ($action){
                $action->disableEdit();
            });
            $grid->filter(function($filter) {
                $filter->like('file_name', 'Tên file');
                $arrModel = CSVData::distinct()->select('model')->pluck('model', 'model')->toArray();
                $filter->where(function ($query){
                    $input = $this->input;
                    $idCSV = CSVData::where('model', $input)->pluck('id');
                    $query->whereIn('id', $idCSV);
                }, 'Tên model')->multipleSelect($arrModel);
                $filter->between('created_at', 'Tạo vào lúc')->datetime();
            });
            $grid->disableExport();
            $grid->disableCreateButton();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(CSVData::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
