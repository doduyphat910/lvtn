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
use App\Models\TimeStudy;
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
            $user = Admin::user();
            $idUser = $user->id;
            $grid->model()->where('id_user_teacher', $idUser);
            $grid->rows(function (Grid\Row $row) {
                $row->column('number', $row->number);
            });
            $grid->number('STT');
//            $grid->id('ID')->sortable();
            $grid->name('Tên lớp')->display(function ($name) {
                return '<a href="/admin/teacher/class/' . $this->id . '/details">' . $name . '</a>';
            })->sortable();
            $grid->id_department('Tên khoa')->display(function ($idDepartment) {
                if ($idDepartment) {
                    return Department::find($idDepartment)->name;
                } else {
                    return '';
                }
            })->sortable();
            $grid->actions(function ($actions) {
                $actions->disableEdit();
                $actions->disableDelete();
                $actions->append('<a href="/admin/teacher/class/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->created_at('Tạo vào lúc')->sortable();
            $grid->updated_at('Cập nhật vào lúc')->sortable();
            $grid->filter(function ($filter){
                $filter->disableIdFilter();
                $filter->like('name', 'Tên');
                $filter->in('id_department', 'Tên khoa')->select(Department::all()->pluck('name','id'));
                $filter->between('created_at', 'Tạo vào lúc')->datetime();
            });
            $grid->disableCreateButton();
            $grid->disableRowSelector();
            $grid->disableExport();
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
            $grid->last_name('Tên')->display(function ($name) {
                return '<a href="/admin/student_user/' . $this->id . '/details">' . $name . '</a>';
            });
//            $grid->username('Tên đăng nhập');
            $grid->email('Email');
            $grid->id_class('Lớp')->display(function ($idClass) {
                if ($idClass) {
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
            $grid->filter(function ($filter){
                $filter->disableIdFilter();
                $filter->like('code_number', 'MSSV');
                $filter->like('first_name', 'Họ');
                $filter->like('last_name', 'Tên');
                $filter->like('email', 'Email');
                $filter->equal('school_year', 'Năm nhập học')->year();
                $filter->in('level', 'Trình độ')->radio(['CD'=>'Cao đẳng', 'DH'=>'Đại học']);
                $filter->between('created_at', 'Tạo vào lúc')->datetime();
            });
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
            $form->text('name', 'Tên lớp')->rules(function ($form) {
                return 'required|unique:class,name,' . $form->model()->id . ',id';
            })->readOnly();
            $form->select('id_department', 'Tên khoa')->options(Department::all()->pluck('name', 'id'))->rules('required')->readOnly();
            $form->disableReset();
        });
    }


    public function details($id)
    {
        return Admin::content(
            function (Content $content) use ($id) {
                $class = ClassSTU::findOrFail($id);
                $content->header('Lớp');
                $content->description($class->name);
                $content->body($this->detailsView($id));
            });
    }

    public function detailsView($id)
    {
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

    //todo start subject-register
    #region subject register
    //subject register of teacher
    public function subjectRegister()
    {
        return Admin::content(function (Content $content) {

            $content->header('Giảng viên');
            $content->description('Xem lịch, TKB');

            $content->body(
             view('vendor.details',
                [
                    'template_body_name' => 'admin.Teacher.SubjectRegister.info',
                    'formTimeRegister' => $this->formTimeRegister(),
                    'gridSubjectRegister' => $this->gridSubjectRegister()
                ]));
        });
    }

    protected function formTimeRegister()
    {
        return Admin::form(TimeRegister::class, function (Form $form) {
            $user = Admin::user();
            $idUser = $user->id;
            $timeRegisterTeacher = SubjectRegister::where('id_user_teacher', $idUser)->pluck('id_time_register')->toArray();
            $form->select('id_time_register', 'Thời gian')->options(TimeRegister::whereIn('id', $timeRegisterTeacher)->orderBy('id', 'DESC')->pluck('name', 'id'));

            $form->disableReset();
            $form->disableSubmit();

        });
    }

    protected function gridSubjectRegister()
    {
        return Admin::grid(SubjectRegister::class, function (Grid $grid)  {
            $user = Admin::user();
            $idUser = $user->id;
            $subjectRegister = SubjectRegister::where('id_user_teacher', $idUser)->orderBy('id_time_register', 'DESC')->first();
            if(!empty($subjectRegister)) {
                $grid->model()->where('id_time_register', $subjectRegister->id_time_register)->where('id_user_teacher', $idUser);
            } else {
                $grid->model()->where('id', '-1');
            }
            $grid->code_subject_register('Mã học phần')->display(function ($name) {
                return '<a href="/admin/teacher/subject-register/' . $this->id . '/details">' . $name . '</a>';
            });
            $grid->id_subjects('Môn học')->display(function ($idSubject) {
                if ($idSubject) {
                    $name = Subjects::find($idSubject)->name;
                    return "<span class='label label-info'>{$name}</span>";
                } else {
                    return '';
                }
            });
            $grid->column('Phòng')->display(function () {
                $idClassroom = TimeStudy::where('id_subject_register', $this->id)->pluck('id_classroom')->toArray();
                $classRoom = Classroom::whereIn('id', $idClassroom)->pluck('name')->toArray();
                $classRoom = array_map(function ($classRoom) {
                    return "<span class='label label-success'>{$classRoom}</span>";
                }, $classRoom);
                return join('&nbsp;', $classRoom);
            });
            $grid->column('Buổi học')->display(function () {
                $day = TimeStudy::where('id_subject_register', $this->id)->pluck('day')->toArray();
                $day = array_map(function ($day) {
                    switch ($day) {
                        case 2:
                            $day = 'Thứ 2';
                            break;
                        case 3:
                            $day = 'Thứ 3';
                            break;
                        case 4:
                            $day = 'Thứ 4';
                            break;
                        case 5:
                            $day = 'Thứ 5';
                            break;
                        case 6:
                            $day = 'Thứ 6';
                            break;
                        case 7:
                            $day = 'Thứ 7';
                            break;
                        case 8:
                            $day = 'Chủ nhật';
                            break;
                    }
                    return "<span class='label label-success'>{$day}</span>";
                }, $day);
                return join('&nbsp;', $day);
            });
            $grid->column('Thời gian học')->display(function () {
                $timeStart = TimeStudy::where('id_subject_register', $this->id)->pluck('time_study_start')->toArray();
                $timeEnd = TimeStudy::where('id_subject_register', $this->id)->pluck('time_study_end')->toArray();
                $time = array_map(function ($timeStart, $timeEnd) {
                    return "<span class='label label-success'>{$timeStart} - {$timeEnd}</span>";
                }, $timeStart, $timeEnd);
                return join('&nbsp;', $time);
            });
            $grid->id_user_teacher('Giảng viên')->display(function ($id_user_teacher) {
                if ($id_user_teacher) {
                    $teacher = UserAdmin::find($id_user_teacher);
                    if ($teacher) {
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
                $actions->append('<a href="/admin/teacher/subject-register/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->filter(function($filter){
                $filter->disableIdFilter();
                $filter->like('code_subject_register', 'Mã học phần');
//                $filter->in('id_subjects', 'Tên môn học')->multipleSelect(Subjects::all()->pluck('name', 'id'));
                $filter->where(function ($query) {
                    $input = $this->input;
                    $query->whereIn('id_subjects', $input);
                }, 'Tên môn học')->multipleSelect(Subjects::all()->pluck('name', 'id'));
                $filter->in('id_user_teacher', 'Giảng viên')->multipleSelect(UserAdmin::where('type_user', 0)->pluck('name', 'id'));
                $filter->in('id_time_register', 'TG Đăng ký')->multipleSelect(TimeRegister::all()->pluck('name','id'));
                $filter->like('qty_current', 'SL hiện tại');
                $filter->date('date_start', 'Ngày bắt đầu');
                $filter->date('date_end', 'Ngày kết thúc');
                $filter->between('created_at', 'Tạo vào lúc')->datetime();
            });
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();
        });
    }

    public function detailsSubjectRegister($id)
    {
        return Admin::content(
            function (Content $content) use ($id) {
                $class = SubjectRegister::findOrFail($id);
                $content->header('Lớp HP');
                $content->description($class->code_subject_register);
                $content->body($this->detailsViewSubjectRegister($id));
            });
    }

    public function detailsViewSubjectRegister($id)
    {
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
            $grid->resource('/admin/teacher/point');
            $user = Admin::user();
            $idUser = $user->id;
            $grid->model()->where('id_subject_register', $idSubjectRegister);
//            $grid->id('ID')->sortable();
            $grid->column('MSSV')->display(function () {
                if (StudentUser::find($this->id_user_student)->code_number) {
                    return StudentUser::find($this->id_user_student)->code_number;
                } else {
                    return '';
                }
            });
            $grid->column('Họ')->display(function () {
                if (StudentUser::find($this->id_user_student)->first_name) {
                    return StudentUser::find($this->id_user_student)->first_name;
                } else {
                    return '';
                }
            });
            $grid->id_user_student('Tên')->display(function ($idStudent) {
                if (StudentUser::find($idStudent)->last_name) {
                    return StudentUser::find($idStudent)->last_name;
                } else {
                    return '';
                }
            });
            $grid->id_subject_register('Mã HP')->display(function ($idSubjectRegister) {
                if (SubjectRegister::find($idSubjectRegister)->code_subject_register) {
                    return SubjectRegister::find($idSubjectRegister)->code_subject_register;
                } else {
                    return '';
                }
            });
            $grid->id_subject('Môn')->display(function ($idSubject) {
                if (Subjects::find($idSubject)->name) {
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
            $grid->column('Lớp')->display(function () {
                $idClass = StudentUser::find($this->id_user_student)->id_class;
                $name = ClassSTU::find($idClass)->name;
                return "<span class='label label-info'>{$name}</span>";
            });
            $idTimeRegister = ResultRegister::where('id_subject_register', $idSubjectRegister)->pluck('time_register');
            $timeRegister = TimeRegister::find($idTimeRegister)->first();
            $statusImport = $timeRegister->status_import;
            $statusEditPoint = $timeRegister->status_edit_point;
            if($statusEditPoint == null || $statusEditPoint == []) {
                $grid->attendance('Điểm chuyên cần');
                $grid->mid_term('Điểm giữa kì');
                $grid->end_term('Điểm cuối kì');
                $grid->column('Điểm tổng kết')->display(function () {
                    if(!$this->attendance || !$this->mid_term || !$this->end_term) {
                        return 'X';
                    } else {
                        return (($this->attendance * $this->rate_attendance) +
                                ($this->mid_term * $this->rate_mid_term) +
                                ($this->end_term * $this->rate_end_term)) / 100;
                    }

                })->setAttributes(['class'=>'finalPoint']);

            } else {
                switch (true){
                    case in_array('1', $statusEditPoint)&& in_array('2', $statusEditPoint) && in_array('3', $statusEditPoint):
                        $grid->attendance('Điểm chuyên cần')->editable();
                        $grid->mid_term('Điểm giữa kì')->editable();
                        $grid->end_term('Điểm cuối kì')->editable();
                        break;
                    case in_array('1', $statusEditPoint) && in_array('2', $statusEditPoint):
                        $grid->attendance('Điểm chuyên cần')->editable();
                        $grid->mid_term('Điểm giữa kì')->editable();
                        $grid->end_term('Điểm cuối kì');
                        break;
                    case in_array('2', $statusEditPoint) && in_array('3', $statusEditPoint):
                        $grid->attendance('Điểm chuyên cần');
                        $grid->mid_term('Điểm giữa kì')->editable();
                        $grid->end_term('Điểm cuối kì')->editable();
                        break;
                    case in_array('1', $statusEditPoint) && in_array('3', $statusEditPoint):
                        $grid->attendance('Điểm chuyên cần')->editable();
                        $grid->mid_term('Điểm giữa kì');
                        $grid->end_term('Điểm cuối kì')->editable();
                        break;
                    case in_array('1', $statusEditPoint):
                        $grid->attendance('Điểm chuyên cần')->editable();
                        $grid->mid_term('Điểm giữa kì');
                        $grid->end_term('Điểm cuối kì');
                    break;
                    case in_array('2', $statusEditPoint):
                        $grid->attendance('Điểm chuyên cần');
                        $grid->mid_term('Điểm giữa kì')->editable();
                        $grid->end_term('Điểm cuối kì');
                        break;
                    case in_array('3', $statusEditPoint):
                        $grid->attendance('Điểm chuyên cần');
                        $grid->mid_term('Điểm giữa kì');
                        $grid->end_term('Điểm cuối kì')->editable();
                        break;

                }
                $grid->column('Điểm tổng kết')->display(function () {
                    if(!$this->attendance || !$this->mid_term || !$this->end_term) {
                        return 'X';
                    } else {
                        $script = <<<SCRIPT
                    $(document).ready ( function () {
                        $(document).on ("click", ".editable-submit", function () {
                           location.reload();
                        });
                    });

SCRIPT;
                        Admin::script($script);
                        return (($this->attendance * $this->rate_attendance) +
                                ($this->mid_term * $this->rate_mid_term) +
                                ($this->end_term * $this->rate_end_term)) / 100;
                    }

                })->setAttributes(['class'=>'finalPoint']);
            }



//            $grid->created_at('Tạo vào lúc');
//            $grid->updated_at('Cập nhật vào lúc');
            $grid->filter(function($filter) {
                $filter->disableIdFilter();
                $filter->where(function ($query){
                    $input = $this->input;
                    $idUser = StudentUser::where('first_name','like', '%'.$input.'%')->pluck('id')->toArray();
                    $query->whereIn('id_user_student', $idUser);
                }, 'Họ SV');
                $filter->where(function ($query){
                    $input = $this->input;
                    $idUser = StudentUser::where('last_name','like', '%'.$input.'%')->pluck('id')->toArray();
                    $query->whereIn('id_user_student', $idUser);
                }, 'Tên SV');
                $filter->where(function ($query){
                    $input = $this->input;
                    $idUser = StudentUser::where('id_class', $input)->pluck('id')->toArray();
                    $query->whereIn('id_user_student', $idUser);
                }, 'Lớp')->select(ClassSTU::all()->pluck('name','id'));
                $filter->in('time_register', 'Đợt ĐK')->select(TimeRegister::all()->pluck('name','id'));
                $filter->between('created_at', 'Tạo vào lúc')->datetime();
            });
            $grid->disableActions();
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->tools(function ($tools) use ($idSubjectRegister) {
                $idTimeRegister = ResultRegister::where('id_subject_register', $idSubjectRegister)->pluck('time_register');
                $statusImport = TimeRegister::find($idTimeRegister)->first();
                if($statusImport != null) {
                    $statusImport = $statusImport->status_import;

                    if (in_array('1', $statusImport)) {
                        $tools->append('<a href="/admin/teacher/' . $idSubjectRegister . '/import-attendance" class="btn btn-info btn-sm btn-import-attendance"><i class="fa fa-sign-in"></i> Import điểm chuyên cần</a>');
                    }
                    if (in_array('2', $statusImport)) {
                        $tools->append('<a href="/admin/teacher/' . $idSubjectRegister . '/import-midterm" class="btn btn-info btn-sm btn-import-midterm"><i class="fa fa-sign-in"    ></i> Import điểm giữa kì</a>');
                    }
                    if (in_array('3', $statusImport)) {
                        $tools->append('<a href="/admin/teacher/' . $idSubjectRegister . '/import-endterm" class="btn btn-info btn-sm btn-import-endterm"><i class="fa fa-sign-in"></i> Import điểm cuối kì</a>');
                    }
                    if (in_array('All', $statusImport)) {
                        $tools->append('<a href="/admin/teacher/'. $idSubjectRegister .'/import-all" class="btn btn-info btn-sm btn-import-all"><i class="fa fa-sign-in"></i> Import điểm SV</a>');
                    }
                }
            });
        });
    }
    #endregion




}
