<?php

namespace App\Admin\Controllers;

use App\Models\Classroom;
use App\Models\ClassSTU;

use App\Models\Department;
use App\Models\ResultRegister;
use App\Models\StudentUser;
use App\Models\SubjectRegister;
use App\Models\Subjects;
use App\Models\TimeRegister;
use App\Models\UserAdmin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class TeacherController extends Controller
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
//            $grid->options()->select([
//                1 => 'Sed ut perspiciatis unde omni',
//                2 => 'voluptatem accusantium doloremque',
//                3 => 'dicta sunt explicabo',
//                4 => 'laudantium, totam rem aperiam',
//            ]);
            $user = Admin::user();
            $idUser = $user->id;
            $grid->model()->where('id_user_teacher', $idUser);
//            $grid->id('ID')->sortable();
            $grid->name('Tên lớp')->display(function ($name){
                return '<a href="/admin/teacher/class/' . $this->id . '/details">'.$name.'</a>';
            });
            $grid->id_department('Tên khoa')->display(function ($idDepartment){
                if($idDepartment) {
                    return Department::find($idDepartment)->name;
                } else {
                    return '';
                }
            });
            $grid->actions(function ($actions) {
                $actions->disableEdit();
                $actions->disableDelete();
                $actions->append('<a href="/admin/teacher/class/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->created_at('Tạo vào lúc');
            $grid->updated_at('Cập nhật vào lúc');
            $grid->disableCreateButton();
        });
    }

    protected function gridStudent($idClass)
    {
        return Admin::grid(StudentUser::class, function (Grid $grid) use ($idClass) {
            $grid->model()->where('id_class', $idClass);
//            $grid->id('ID')->sortable();
            $grid->code_number('Mã số sinh viên');
//            $grid->avatar('Avatar')->image();
            $grid->first_name('Họ');
            $grid->last_name('Tên')->display(function ($name){
                return  '<a href="/admin/student_user/' . $this->id . '/details">'.$name.'</a>';
            });
            $grid->username('Tên đăng nhập');
            $grid->email('Email');
            $grid->id_class('Lớp')->display(function ($idClass){
                if($idClass){
                    return ClassSTU::find($idClass)->name;
                } else {
                    return 'Không có';
                }
            });
            $grid->school_year('Năm nhập học');
            $grid->level('Trình độ');
            $grid->created_at('Thêm vào lúc');
            $grid->updated_at('Cập nhật vào lúc');
            //import student
//            $grid->tools(function ($tools) {
//                $tools->append("<a href='/admin/import_student' class='btn btn-info btn-sm '><i class='fa fa-sign-in'></i> Import DS sinh viên</a>");
//            });
            $grid->disableActions();
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
            $form->text('name', 'Tên lớp')->rules(function ($form){
                return 'required|unique:class,name,'.$form->model()->id.',id';
            })->readOnly();
            $form->select('id_department', 'Tên khoa')->options(Department::all()->pluck('name', 'id'))->rules('required')->readOnly();
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
                'template_body_name' => 'admin.Teacher.info',
                'form' => $form,
                'gridStudent' => $gridStudent
            ]
        );
    }

    public function subjectRegister()
    {
        return Admin::content(function (Content $content) {

            $content->header('Khoa, lớp');
            $content->description('Danh sách lớp');

            $content->body($this->gridSubjectRegister());
        });
    }

    protected function gridSubjectRegister()
    {
        return Admin::grid(SubjectRegister::class, function (Grid $grid) {
            $user = Admin::user();
            $idUser = $user->id;
            $grid->model()->where('id_user_teacher', $idUser);
//            $grid->id('ID')->sortable();
            $grid->code_subject_register('Mã học phần')->display(function ($name){
                return  '<a href="/admin/teacher/subject-register/' . $this->id . '/details">'.$name.'</a>';
            });
            $grid->id_subjects('Môn học')->display(function ($idSubject){
                if($idSubject){
                    $name = Subjects::find($idSubject)->name;
                    return "<span class='label label-info'>{$name}</span>";
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

            //action
            $grid->actions(function ($actions) {
                $actions->disableEdit();
                $actions->disableDelete();
                $actions->append('<a href="/admin/teachers/subject-register/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();
        });
    }

    public function detailsSubjectRegister($id){
        return Admin::content(
            function (Content $content) use ($id) {
                $class = SubjectRegister::findOrFail($id);
                $content->header('Lớp HP');
                $content->description($class->code_subject_register);
                $content->body($this->detailsViewSubjectRegister($id));
            });
    }

    public function detailsViewSubjectRegister($id) {
        $formSubjectRegister = $this->formSubjectRegister()->view($id);
        $gridStudentSubject = $this->gridStudentSubject($id);
        return view('vendor.details',
            [
                'template_body_name' => 'admin.Teacher.StudentSubjectRegister.info',
                'formSubjectRegister' => $formSubjectRegister,
                'gridStudentSubject' => $gridStudentSubject
            ]
        );
    }

    protected function formSubjectRegister()
    {
        return Admin::form(SubjectRegister::class, function (Form $form) {
            $script = <<<EOT
        $(function () {
            var url = window.location.href;
            var action_link =url.split("/");
            var action  = action_link[action_link.length-1]
            if(action=="details")
            {
                $('.remove').remove();
                $('.add').remove();
            }

        });
EOT;
            Admin::script($script);
            $form->text('code_subject_register', 'Mã học phần')->rules(function ($form) {
                return 'required|unique:subject_register,code_subject_register,' . $form->model()->id . ',id';
            })->readOnly();
            $form->select('id_subjects', 'Môn học')->options(Subjects::all()->pluck('name', 'id'))->rules('required')->readOnly();
            $form->select('id_user_teacher', 'Giảng viên')->options(UserAdmin::where('type_user', '0')->pluck('name', 'id'))->rules('required')->readOnly();
            $form->date('date_start', 'Ngày bắt đầu')->placeholder('Ngày bắt đầu')->rules('required')->readOnly();
            $form->date('date_end', 'Ngày kết thúc')->placeholder('Ngày kết thúc')->rules('required')->readOnly();
            $form->disableReset();
            $form->hasMany('time_study', 'Thời gian học', function (Form\NestedForm $form) {
                $options = ['2' => 'Thứ 2', '3' => 'Thứ 3', '4' => 'Thứ 4', '5' => 'Thứ 5', '6' => 'Thứ 6', '7' => 'Thứ 7', '8' => 'Chủ nhật'];
                $form->select('day', 'Buổi học')->options($options)->readOnly();
                $form->select('id_classroom', 'Phòng học')->options(Classroom::all()->pluck('name', 'id'))->rules('required')->readOnly();
                $form->time('time_study_start', 'Giờ học bắt đầu')->readOnly();
                $form->time('time_study_end', 'Giờ học kết thúc')->readOnly();
            })->rules('required');
        });
    }

    protected function gridStudentSubject($idSubjectRegister)
    {
        return Admin::grid(ResultRegister::class, function (Grid $grid) use ($idSubjectRegister) {
            $user = Admin::user();
            $idUser = $user->id;
            $grid->model()->where('id_subject_register', $idSubjectRegister);
//            $grid->id('ID')->sortable();
            $grid->column('MSSV')->display(function (){
                if(StudentUser::find($this->id_user_student)->code_number) {
                    return StudentUser::find($this->id_user_student)->code_number;
                } else {
                    return '';
                }
            });
            $grid->column('Họ')->display(function (){
                if(StudentUser::find($this->id_user_student)->first_name) {
                    return StudentUser::find($this->id_user_student)->first_name;
                } else {
                    return '';
                }
            });
            $grid->id_user_student('Tên')->display(function ($idStudent){
                if(StudentUser::find($idStudent)->last_name) {
                    return StudentUser::find($idStudent)->last_name;
                } else {
                    return '';
                }
            });
            $grid->id_subject_register('Mã HP')->display(function ($idSubjectRegister){
                if(SubjectRegister::find($idSubjectRegister)->code_subject_register) {
                    return SubjectRegister::find($idSubjectRegister)->code_subject_register;
                } else {
                    return '';
                }
            });
            $grid->id_subject('Môn')->display(function ($idSubject){
                if(Subjects::find($idSubject)->name) {
                    return Subjects::find($idSubject)->name;
                } else {
                    return '';
                }
            });
//            $grid->time_register('Đợt đăng kí')->display(function ($timeRegister){
//                if(TimeRegister::find($timeRegister)->name) {
//                    return TimeRegister::find($timeRegister)->name;
//                } else {
//                    return '';
//                }
//            });
            $grid->column('Lớp')->display(function (){
                $idClass = StudentUser::find($this->id_user_student)->id_class;
                $name = ClassSTU::find($idClass)->name;
                return "<span class='label label-info'>{$name}</span>";
            });
            $grid->attendance('Điểm chuyên cần');
            $grid->mid_term('Điểm giữa kì');
            $grid->end_term('Điểm cuối kì');
            $grid->final('Điểm tổng kết');

//            $grid->created_at('Tạo vào lúc');
//            $grid->updated_at('Cập nhật vào lúc');
            $grid->disableActions();
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->tools(function ($tools) use ($idSubjectRegister){
                $idTimeRegister = ResultRegister::where('id_subject_register',$idSubjectRegister)->pluck('time_register');
                $statusImport = TimeRegister::find($idTimeRegister)->first();
                $statusImport = $statusImport->status_import;

                if(in_array('1',$statusImport))
                {
                    $tools->append("<a href='/admin/teacher/import-point-attendance' class='btn btn-info btn-sm btn-import-attendance'><i class='fa fa-sign-in'></i> Import điểm chuyên cần</a>");
                }
                if (in_array('2',$statusImport)) {
                    $tools->append("<a href='/admin/teacher/import-point-midterm' class='btn btn-info btn-sm btn-import-midterm'><i class='fa fa-sign-in'></i> Import điểm giữa kì</a>");
                }
                if (in_array('3',$statusImport)) {
                    $tools->append("<a href='/admin/teacher/import-point-endterm' class='btn btn-info btn-sm btn-import-endterm'><i class='fa fa-sign-in'></i> Import điểm cuối kì</a>");
                }
                if (in_array('all',$statusImport)) {
                    $tools->append("<a href='/admin/teacher/import-point-all' class='btn btn-info btn-sm btn-import-all'><i class='fa fa-sign-in'></i> Import điểm SV</a>");
                }
            });

        });
    }
}
