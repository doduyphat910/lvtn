<?php

namespace App\Admin\Controllers;

use App\Models\Classroom;

use App\Models\SubjectRegister;
use App\Models\Subjects;
use App\Models\TimeStudy;
use App\Models\UserAdmin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ClassroomController extends Controller
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
            $content->description('Danh sách phòng học');

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

            $content->header('Phòng học');
            $content->description('Thêm phòng học');

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
        return Admin::grid(Classroom::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->name('Tên')->display(function ($name){
                return  '<a href="/admin/class_room/' . $this->id . '/details">'.$name.'</a>';
            });
            $grid->actions(function ($actions) {
                $actions->append('<a href="/admin/class_room/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->created_at();
            $grid->updated_at();
        });
    }

    protected function gridSubjectRegister($idSubjectRegister)
    {
        return Admin::grid(SubjectRegister::class, function (Grid $grid) use ($idSubjectRegister) {
            $grid->model()->whereIn('id', $idSubjectRegister);
            $grid->id('ID')->sortable();
            $grid->code_subject_register('Mã học phần');
            $grid->id_subjects('Môn học')->display(function ($idSubject){
                if($idSubject){
                    return Subjects::find($idSubject)->name;
                } else {
                    return '';
                }
            });
            $grid->id_classroom('Phòng học')->display(function ($id_classroom){
                if($id_classroom){
                    return Classroom::find($id_classroom)->name;
                } else {
                    return '';
                }
            });
            $grid->id_user_teacher('Giảng viên')->display(function ($id_user_teacher){
                if($id_user_teacher){
                    $teacher = UserAdmin::find($id_user_teacher);
                    if($teacher){
                        return $teacher->name;
                    } else {
                        return '';
                    }
                } else {
                    return '';
                }
            });
            $grid->qty_current('Số lượng hiện tại');
//            $grid->qty_min('Số lượng tối thiểu');
//            $grid->qty_max('Số lượng tối đa');

            $grid->date_start('Ngày bắt đầu');
            $grid->date_end('Ngày kết thúc');

            $grid->created_at('Tạo vào lúc');
            $grid->updated_at('Cập nhật vào lúc');

            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableFilter();
            $grid->actions(function ($actions) {
                $actions->disableEdit();
                $actions->disableDelete();
                $actions->append('<a href="/admin/subject_register/' . $actions->getKey() . '/edit"><i class="fa fa-edit" ></i></a>');
                $actions->append('<a href="/admin/subject_register/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
        });
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Classroom::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('name', 'Tên phòng')->rules(function ($form){
                return 'required|unique:class_room,name,'.$form->model()->id.',id';
            });

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    public function details($id){
        return Admin::content(function (Content $content) use ($id) {
            $classRoom = Classroom::findOrFail($id);
            $content->header('Phòng học');
            $content->description($classRoom->name);
            $content->body($this->detailsView($id));
        });
    }

    public function detailsView($id){
        $form = $this->form()->view($id);
        $idSubjectRegister = TimeStudy::where('id_classroom', $id)->pluck('id_subject_register');
        $gridSubjectRegister = $this->gridSubjectRegister($idSubjectRegister)->render();
        return view('vendor.details',
            [
                'template_body_name' => 'admin.ClassRoom.info',
                'form' => $form,
                'gridSubjectRegister' => $gridSubjectRegister

            ]
        );
    }
}
