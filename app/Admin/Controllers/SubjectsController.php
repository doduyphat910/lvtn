<?php

namespace App\Admin\Controllers;

use App\Models\Classroom;
use App\Models\Rate;
use App\Models\SubjectRegister;
use App\Models\Subjects;

use App\Models\UserAdmin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\Semester;
use App\Models\SubjectGroup;

class SubjectsController extends Controller
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

            $content->header('Môn học');
            $content->description('Danh sách môn học');

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
        return Admin::grid(Subjects::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->subject_code('Mã môn học');
            $grid->name('Tên môn học')->display(function ($name){
                return  '<a href="/admin/subject/' . $this->id . '/details">'.$name.'</a>';
            });
            $grid->credits('Số tín chỉ');
            $grid->credits_fee('Số tín chỉ học phí');
            $grid->id_semester('Học kỳ')->display(function ($id) {
                return Semester::find($id)->name;
            });
            $grid->id_subject_group('Nhóm môn')->display(function ($id) {
                return SubjectGroup::find($id)->name;
            });
            $grid->id_rate('Tỷ lệ chuyên cần')->display(function ($rate){
                if($rate){
                    return Rate::find($rate)->attendance;
                } else {
                    return '';
                }
            });
            $grid->column('Tỷ lệ giữa kì')->display(function (){
                if($this->id_rate) {
                    return Rate::find($this->id_rate)->midterm;
                } else {
                    return '';
                }
            });
            $grid->column('Tỷ lệ cuối kì')->display(function (){
                if($this->id_rate) {
                    return Rate::find($this->id_rate)->end_term;
                } else {
                    return '';
                }
            });
            $grid->created_at();
            $grid->updated_at();
            //action
            $grid->actions(function ($actions) {
                $actions->append('<a href="/admin/subject/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
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
        return Admin::form(Subjects::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('subject_code', 'Mã môn học')->rules(function ($form) {
                if (!$id = $form->model()->id) {
                    return 'unique:subject_code';//todo not completed unique code
                }

            });
            $form->text('name','Tên môn học');
            $form->number('credits','Tín chỉ');
            $form->number('credits_fee', 'Tín chỉ học phí');
            $form->select('id_semester', 'Học kỳ')->options(Semester::all()->pluck('name', 'id'));
            $form->select('id_subject_group', 'Nhóm môn')->options(SubjectGroup::all()->pluck('name', 'id'));
            $rates = Rate::all();
            $arrayRate = [];
            foreach($rates as $rate) {
                $arrayRate += [$rate['id'] => $rate['attendance'] . '-'.  $rate['midterm'] .'-' .$rate['end_term']];
            }
            $form->select('id_rate', 'Tỷ lệ điểm')->options($arrayRate);
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    protected function gridSubjectRegister($idSubjects)
    {
        return Admin::grid(SubjectRegister::class, function (Grid $grid) use ($idSubjects) {
            $grid->model()->where('id_Subjects', $idSubjects);
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

    public function details($id){
        return Admin::content(function (Content $content) use ($id) {
            $subject = Subjects::findOrFail($id);
            $content->header('Môn học');
            $content->description($subject->name);
            $content->body($this->detailsView($id));
        });
    }

    public function detailsView($id){
        $form = $this->form()->view($id);
        $gridSubjectRegister = $this->gridSubjectRegister($id)->render();
        return view('vendor.details',
            [
                'template_body_name' => 'admin.Subject.info',
                'form' => $form,
                'gridSubjectRegister' => $gridSubjectRegister

            ]
        );
    }
}
