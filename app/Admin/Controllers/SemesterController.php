<?php

namespace App\Admin\Controllers;

use App\Models\Classroom;
use App\Models\Rate;
use App\Models\Semester;

use App\Models\SemesterSubjects;
use App\Models\SubjectGroup;
use App\Models\SubjectRegister;
use App\Models\Subjects;
use App\Models\UserAdmin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\Year;
class SemesterController extends Controller
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
            $content->description('Danh sách học kỳ');

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
        return Admin::grid(Semester::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->name('Tên')->display(function ($name){
                return  '<a href="/admin/semester/' . $this->id . '/details">'.$name.'</a>';
            });
            $grid->credits_max('Số tín chỉ lớn nhất');
            $grid->credits_min('Số tín chỉ nhỏ nhất');
            $grid->id_year('Tên năm')->display(function ($idyear) {
                $name = Year::find($idyear)->name;
                return "<span class='label label-info'>{$name}</span>";
            });
            $grid->time_start('Thời gian bắt đầu');
            $grid->time_end('Thời gian kết thúc');
            $grid->status('Trạng thái đăng ký môn học')->display(function ($status){
                if($status == 1){
                    return "<span class='label label-success'>Đang mở</span>";
                } else {
                    return "<span class='label label-danger'>Đang đóng</span>";

                }
            });
            $grid->actions(function ($actions) {
                $actions->append('<a href="/admin/semester/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->created_at('Tạo vào lúc');
            $grid->updated_at('Cập nhật vào lúc');
        });
    }

    protected function gridSubject($idSemester)
    {
        return Admin::grid(Subjects::class, function (Grid $grid) use ($idSemester) {
            $idSubjects = SemesterSubjects::where('semester_id', $idSemester)->pluck('subjects_id');
            $grid->model()->whereIn('id', $idSubjects);
            $grid->id('ID')->sortable();
            $grid->subject_code('Mã môn học');
            $grid->name('Tên môn học')->display(function ($name){
                return  '<a href="/admin/subject/' . $this->id . '/details">'.$name.'</a>';
            });
            $grid->credits('Số tín chỉ');
            $grid->credits_fee('Số tín chỉ học phí');
            $grid->column('Học kỳ - Năm')->display(function () {
                $id = $this->id;
                $subject = Subjects::find($id);
                $arraySemester = $subject->semester()->pluck('id')->toArray();
                $name = array_map( function ($arraySemester){
                    $nameSemester = Semester::find($arraySemester)->name;
                    $year = Semester::find($arraySemester)->year()->get();
                    $nameYear = $year['0']->name;
                    return "<span class='label label-info'>{$nameSemester} - {$nameYear}</span>"  ;
                }, $arraySemester);
                return join('&nbsp;', $name);
            });
            $grid->column('Nhóm môn')->display(function () {
                $subject = Subjects::find($this->id);
                $nameGroup = $subject->subject_group()->pluck('name')->toArray();
                $groupSubject = array_map(function ($nameGroup){
                    if($nameGroup) {
                        return "<span class='label label-primary'>{$nameGroup}</span>"  ;
                    } else {
                        return '';
                    }
                },$nameGroup);
                return join('&nbsp;', $groupSubject);
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
            $grid->created_at('Tạo vào lúc');
            $grid->updated_at('Cập nhật vào lúc');
            //action
            $grid->actions(function ($actions) {
                $actions->disableEdit();
                $actions->disableDelete();
                $actions->append('<a href="/admin/subject_register/' . $actions->getKey() . '/edit"><i class="fa fa-edit" ></i></a>');
                $actions->append('<a href="/admin/subject_register/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            //disable
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableFilter();
        });
    }

    protected function gridSubjectRegister($idSubjects)
    {
        return Admin::grid(SubjectRegister::class, function (Grid $grid) use ($idSubjects) {
            $grid->model()->whereIn('id_Subjects', $idSubjects);
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

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Semester::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->select('name', 'Tên học kì')->options(['0'=>' Học kỳ hè', '1' => 'Học kì 1', '2' => 'Học kì 2'])->rules('required');
            $form->select('id_year', 'Năm')->options(Year::all()->pluck('name', 'id'));
            $form->number('credits_max', 'Số tín chỉ lớn nhất');
            $form->number('credits_min', 'Số tín chỉ nhỏ nhất');
//            $form->dateRange('time_start' , 'time_end', 'Thời gian')->attribute(['data-date-min-date' => date("Y-m-d")])
//                ->rules('required');
            $form->date('time_start', 'Ngày bắt đầu')->attribute(['data-date-min-date' => date("Y-m-d")])->rules('required');
            $form->date('time_end', 'Ngày kết thúc')->attribute(['data-date-min-date' => date("Y-m-d")])->rules('required');
            $states = [
                'on'  => ['value' => 1, 'text' => 'Mở', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => 'Đóng', 'color' => 'danger'],
            ];
            $form->switch('status', 'Trạng thái đăng ký môn')->states($states)->default('1');
            $form->listbox('subjects', 'Môn học')->options(Subjects::all()->pluck('name', 'id'));
            $form->display('created_at', 'Tạo vào lúc');
            $form->display('updated_at', 'Cập nhật vào lúc');
        });
    }

    public function details($id){
        return Admin::content(
            function (Content $content) use ($id) {
                $semester = Semester::findOrFail($id);
                $content->header('Học kỳ');
                $content->description($semester->name);
                $content->body($this->detailsView($id));
            });
    }

    public function detailsView($id){
        $form = $this->form()->view($id);
        $gridSubject = $this->gridSubject($id)->render();
        $idSubject = SemesterSubjects::where('semester_id', $id)->pluck('subjects_id');
        $gridSubjectRegister = $this->gridSubjectRegister($idSubject)->render();
        return view('vendor.details',
            [
                'template_body_name' => 'admin.Semester.info',
                'form' => $form,
                'gridSubject' => $gridSubject,
                'gridSubjectRegister' => $gridSubjectRegister

            ]
        );
    }
}
