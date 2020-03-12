<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ModelFormCustom;
use App\Admin\Extensions\Subject\AdminMissID;
use App\Admin\Extensions\Subject\FormID;
use App\Models\Classroom;
use App\Models\ClassSTU;
use App\Models\ResultRegister;
use App\Models\Semester;
use App\Models\SemesterSubjects;
use App\Models\StudentUser;
use App\Models\TimeTable;
use App\Models\UserAdmin;
use App\Models\SubjectRegister;

use App\Models\Subjects;
use App\Models\TimeRegister;
use App\Models\TimeStudy;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\MessageBag;

class SubjectRegisterController extends Controller
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

            $content->header('Học phần');
            $content->description('Danh sách học phần');
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

            $subjectRegister = SubjectRegister::findOrFail($id);
            $content->header('Học phần');
            $content->description($subjectRegister->id);
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

            $content->header('Học phần');
            $content->description('Tạo học phần');

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
        return Admin::grid(SubjectRegister::class, function (Grid $grid) {
            $grid->model()->orderBy('created_at', 'DESC');
//            $grid->rows(function (Grid\Row $row) {
//                $row->column('number', $row->number);
//            });
//            $grid->number('STT');
//            $grid->id('ID')->sortable();
            $grid->id('Mã học phần')->display(function ($name){
                return  '<a href="/admin/subject_register/' . $this->id . '/details">'.$name.'</a>';
            })->sortable();
            $grid->id_subjects('Môn học')->display(function ($idSubject){
                if($idSubject){
                    $name = Subjects::find($idSubject)->name;
                    return "<span class='label label-success'>{$name}</span>";
                } else {
                    return '';
                }
            })->sortable();
//            $grid->column('Phòng')->display(function () {
//                $idClassroom = TimeStudy::where('id_subject_register', $this->id)->pluck('id_classroom')->toArray();
//                $classRoom = Classroom::whereIn('id', $idClassroom)->pluck('name')->toArray();
//                $classRoom = array_map(function ($classRoom) {
//                    return "<span class='label label-danger'>{$classRoom}</span>";
//                }, $classRoom);
//                return join('&nbsp;', $classRoom);
//            })->sortable();
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
            })->sortable();
            $grid->qty_current('Số lượng hiện tại')->sortable();
//            $grid->qty_min('Số lượng tối thiểu');
//            $grid->qty_max('Số lượng tối đa');
//            $grid->time_study_start('Giờ học bắt đầu');
//            $grid->time_study_end('Giờ học kết thúc');
            $grid->date_start('Ngày bắt đầu')->sortable();
            $grid->date_end('Ngày kết thúc')->sortable();
            $grid->id_time_register('Thời gian đăng ký')->display(function ($idTimeRegister){
                $timeRegister = TimeRegister::find($idTimeRegister);
                if(!empty($timeRegister->name)){
                    if($idTimeRegister % 2 == 0) {
                        return "<span class='label label-info'>{$timeRegister->name}</span>";
                    } else {
                        return "<span class='label label-success'>{$timeRegister->name}</span>";
                    }
                } else {
                    return '';
                }
            })->sortable();
//            $grid->comlumn('Đợt đăng ký')->display(function (){
//                if($this->id_time_register){
//                    return TimeRegister::find($this->id_time_register)->name;
//                } else {
//                    return '';
//                }
//            });
//            $grid->id_time_register('Thời hạn đăng ký')->display(function ($id_time_register){
//                if($id_time_register){
//                    return TimeRegister::find($id_time_register)->time_register_start .' -> '. TimeRegister::find($id_time_register)->time_register_end ;
//                } else {
//                    return '';
//                }
//            });
            //$grid->column('Ngày học')->display(function (){
//                if($this->id) {
//                    $date = TimeStudy::where('id_subject_register', $this->id)->get();
//                    debug($date);
//                    if (isset($date)) {
//                        return $date->time_study_start;
//                    }
//                }
//                debug($this->id);
           // });
            $grid->created_at('Tạo vào lúc')->sortable();
            $grid->updated_at('Cập nhật vào lúc')->sortable();

            //action
            $grid->actions(function ($actions) {
                $actions->append('<a href="/admin/subject_register/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->filter(function($filter){
                $filter->disableIdFilter();
                $filter->like('id', 'Mã học phần');
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
            $grid->disableExport();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return AdminMissID::form(SubjectRegister::class, function (FormID $form) {
            $script = <<<EOT
        $(function () {
            var url = window.location.href;
            var action_link =url.split("/");
            var action  = action_link[action_link.length-1]
            if(action=="create"){
                var is_create_error = $('i').hasClass('fa-times-circle-o');
//                if(is_create_error){
//                     $('.remove')[0].remove();
//                }else{
                   $('.add').click();
                  // $('.remove').remove();
               // }
            }
            if(action=="edit")
            {
//                $('.remove')[0].remove();
            }
            if(action=="details")
            {
                $('.remove').remove();
                $('.add').remove();
            }

        });
EOT;
            Admin::script($script);
//            $form->display('id', 'ID');
            $currentPath = Route::getFacadeRoot()->current()->uri();
            if($currentPath == 'admin/subject_register/{subject_register}/edit'){
                $form->display('id', 'Mã học phần');
            } else {
                $form->text('id', 'Mã học phần')->rules(function ($form){
                    if (!$id = $form->model()->id) {
                        return 'required|unique:subjects,id';
                    }
//                return 'required|unique:subject_register,'.$form->model()->id.',id,deleted_at,NULL';
                });
            }
            $form->select('id_time_register', 'Đợt đăng ký')->options(TimeRegister::orderBy('created_at','DESC')
                ->pluck('name', 'id'))->rules('required')->load('id_subjects','/admin/subject-register/subject');
            $form->select('id_subjects', 'Môn học')->options(Subjects::all()->pluck('name', 'id'))->rules('required');
            $form->select('id_user_teacher', 'Giảng viên')->options(UserAdmin::where('type_user', '0')->pluck('name', 'id'))->rules('required');
            $form->hidden('qty_current', 'Số lượng hiện tại')->value('0');
            $form->number('qty_min', 'Số lượng tối thiểu')->rules('integer|min:5');
            $form->number('qty_max', 'Số lượng tối đa')->rules('integer|min:10');
            $form->date('date_start', 'Ngày bắt đầu')->placeholder('Ngày bắt đầu')->rules('required');
            $form->date('date_end', 'Ngày kết thúc')->placeholder('Ngày kết thúc')->rules('required');
            $form->display('created_at', 'Tạo vào lúc');
            $form->display('updated_at', 'Cập nhật vào lúc');
            $form->hasMany('time_study', 'Thời gian học', function (Form\NestedForm $form) {
                $options = ['2'=>'Thứ 2', '3'=>'Thứ 3', '4'=>'Thứ 4', '5'=>'Thứ 5', '6'=>'Thứ 6', '7'=>'Thứ 7', '8'=>'Chủ nhật'];
                $form->select('day', 'Ngày học')->options($options);
                $form->select('id_classroom', 'Phòng học')->options(Classroom::all()->pluck('name', 'id'));
                $timeStart = Timetable::all()->pluck('time_start', 'time_start' );
                $timeEnd = Timetable::all()->pluck('time_end', 'time_end' );
                $form->select('time_study_start', 'Giờ học bắt đầu')->options($timeStart);
                $form->select('time_study_end', 'Giờ học kết thúc')->options($timeEnd);
            })->rules('required');
//            $form->hidden('id_semester');
            $form->saving(function (Form $form){
                //add more semester
//                $subject = Subjects::find($form->id_subjects);
//                $idSemester = $subject->semester()->pluck('id')->toArray();
//                $form->id_semester = $idSemester['0'] ;
                $currentPath = Route::getFacadeRoot()->current()->uri();
                //check teacher the same time
                $idTeacher = $form->id_user_teacher;
                $idTimeRegister = $form->id_time_register;
                $subjectTeacherRegister = SubjectRegister::where('id_user_teacher', $idTeacher)->where('id_time_register', $idTimeRegister)->pluck('id')->toArray();
                if($currentPath == "admin/subject_register/{subject_register}") {
                    if (($key = array_search($form->model()->id, $subjectTeacherRegister )) !== false) {
                        unset($subjectTeacherRegister[$key]);
                    }
                    $timeStudyTeacher = TimeStudy::whereIn('id_subject_register', $subjectTeacherRegister)->get()->toArray();
                } else {
                    $timeStudyTeacher = TimeStudy::whereIn('id_subject_register',$subjectTeacherRegister)->get()->toArray();
                }
                if($form->time_study) {
                    foreach ($form->time_study as $day) {
                        foreach ($timeStudyTeacher as $timeStudy) {
                            if ($day['day'] == $timeStudy['day']) {
                                if (
                                    ($day['time_study_end'] > $timeStudy['time_study_start'] && $day['time_study_end'] <= $timeStudy['time_study_end']) ||
                                    ($day['time_study_start'] >= $timeStudy['time_study_start'] && $day['time_study_start'] < $timeStudy['time_study_end']) ||
                                    ($day['time_study_start'] >= $timeStudy['time_study_start'] && $day['time_study_end'] <= $timeStudy['time_study_end'])  ||
                                    ($day['time_study_start'] <= $timeStudy['time_study_start'] && $day['time_study_end'] >= $timeStudy['time_study_end'])
                                ) {
                                    $error = new MessageBag([
                                        'title' => 'Lỗi',
                                        'message' => 'Giảng viên đã có giờ dạy này ',
                                    ]);
                                    return back()->with(compact('error'));
                                }
                            }
                        }
                    }
                }
                //check time study
                if($form->time_study) {
                    foreach($form->time_study as $timeStudy) {
                        if($timeStudy['time_study_start'] >= $timeStudy['time_study_end']) {
                            $error = new MessageBag([
                                'title'   => 'Lỗi',
                                'message' => 'Giờ học bắt đầu không được lớn hơn hoặc bằng giờ học kết thúc',
                            ]);
                            return back()->with(compact('error'));
                        }
                    }
                }

                //check conditions register
                $currentPath = Route::getFacadeRoot()->current()->uri();
                $subjectToTime = SubjectRegister::where('id_time_register', $form->id_time_register)->pluck('id')->toArray();
                if($currentPath == "admin/subject_register/{subject_register}") {
                    $timeStudys = TimeStudy::where('id_subject_register', '!=',$form->model()->id)->whereIn('id_subject_register', $subjectToTime)->get()->toArray();
                } else {
                    $timeStudys = TimeStudy::all()->whereIn('id_subject_register', $subjectToTime)->toArray();
                }
                    if($form->time_study) {
                        foreach ($form->time_study as $day) {
                            foreach ($timeStudys as $timeStudy) {
                                if ($day['day'] == $timeStudy['day'] && $day['id_classroom'] == $timeStudy['id_classroom']) {
                                    if (
                                        ($day['time_study_end'] > $timeStudy['time_study_start'] && $day['time_study_end'] <= $timeStudy['time_study_end']) ||
                                        ($day['time_study_start'] >= $timeStudy['time_study_start'] && $day['time_study_start'] < $timeStudy['time_study_end']) ||
                                        ($day['time_study_start'] >= $timeStudy['time_study_start'] && $day['time_study_end'] <= $timeStudy['time_study_end'])  ||
                                        ($day['time_study_start'] <= $timeStudy['time_study_start'] && $day['time_study_end'] >= $timeStudy['time_study_end'])
                                    ) {
                                        $error = new MessageBag([
                                            'title' => 'Lỗi',
                                            'message' => 'Giờ học này đã có lớp học ',
                                        ]);
                                        return back()->with(compact('error'));
                                    }
                                }
                            }
                        }
                    }
            });
            $form->disableReset();

        });
    }

    protected function formDetails($id)
    {
        return AdminMissID::form(SubjectRegister::class, function (FormID $form) use ($id) {
            $script = <<<EOT
        $(function () {
            var url = window.location.href;
            var action_link =url.split("/");
            var action  = action_link[action_link.length-1]
            if(action=="create"){
                var is_create_error = $('i').hasClass('fa-times-circle-o');
//                if(is_create_error){
//                     $('.remove')[0].remove();
//                }else{
                   $('.add').click();
                  // $('.remove').remove();
               // }
            }
            if(action=="edit")
            {
//                $('.remove')[0].remove();
            }
            if(action=="details")
            {
                $('.remove').remove();
                $('.add').remove();
            }

        });
EOT;
            Admin::script($script);
            $form->display('id', 'Mã học phần');
            $form->select('id_time_register', 'Đợt đăng ký')->options(TimeRegister::orderBy('created_at','DESC')
                ->pluck('name', 'id'))->rules('required')->readOnly();
            $form->select('id_subjects', 'Môn học')->options(Subjects::all()->pluck('name', 'id'))->rules('required')->readOnly();
            $form->select('id_user_teacher', 'Giảng viên')->options(UserAdmin::where('type_user', '0')->pluck('name', 'id'))->rules('required')->readOnly();
            $form->hidden('qty_current', 'Số lượng hiện tại')->value('0');
            $form->number('qty_min', 'Số lượng tối thiểu')->rules('integer|min:5')->readOnly();
            $form->number('qty_max', 'Số lượng tối đa')->rules('integer|min:10')->readOnly();
            $form->date('date_start', 'Ngày bắt đầu')->placeholder('Ngày bắt đầu')->rules('required')->readOnly();
            $form->date('date_end', 'Ngày kết thúc')->placeholder('Ngày kết thúc')->rules('required')->readOnly();
            $form->display('created_at', 'Tạo vào lúc');
            $form->display('updated_at', 'Cập nhật vào lúc');
            $form->hasMany('time_study', 'Thời gian học', function (Form\NestedForm $form) {
                $options = ['2'=>'Thứ 2', '3'=>'Thứ 3', '4'=>'Thứ 4', '5'=>'Thứ 5', '6'=>'Thứ 6', '7'=>'Thứ 7', '8'=>'Chủ nhật'];
                $form->select('day', 'Ngày học')->options($options)->readOnly();
                $form->select('id_classroom', 'Phòng học')->options(Classroom::all()->pluck('name', 'id'))->readOnly();
                $timeStart = Timetable::all()->pluck('time_start', 'time_start' );
                $timeEnd = Timetable::all()->pluck('time_end', 'time_end' );
                $form->select('time_study_start', 'Giờ học bắt đầu')->options($timeStart)->readOnly();
                $form->select('time_study_end', 'Giờ học kết thúc')->options($timeEnd)->readOnly();
            })->rules('required');
            $form->disableReset();
            $form->tools(function (Form\Tools $tools) use ($id) {
                $tools->add('<a href="/admin/subject_register/'.$id.'/edit" class="btn btn-sm btn-default" style="margin-right: 10px;"><i class="fa fa-edit"></i>&nbsp;&nbsp;Sửa</a>');
            });

        });
    }

    public function subject(Request $request)
    {
        $timeRegisterId = $request->get('q');
        $timeRegister = TimeRegister::find($timeRegisterId);
        $semesterName = $timeRegister->semester;
        $semesters = Semester::where('name', $semesterName)->get()->toArray();
        $subject = [];
        foreach($semesters as $semester) {
            $semes = Semester::find($semester['id']);
            array_push($subject, $semes->subjects()->get(['id', DB::raw('name as text')])) ;
        }
        return $subject;
    }

    public function details($id){
        return Admin::content(function (Content $content) use ($id) {
            $subjectRegister = SubjectRegister::findOrFail($id);
            $content->header('Học phần');
            $content->description($subjectRegister->id);
            $content->body($this->detailsView($id));
        });
    }

    public function detailsView($id){
        $form = $this->formDetails($id)->view($id);
        return view('vendor.details',
            [
                'template_body_name' => 'admin.SubjectRegister.info',
                'form' => $form,
                'gridStudentRegister' => $this->gridStudentRegister($id)
            ]
        );
    }
    protected function gridStudentRegister($idSubjectRegister)
    {
        return Admin::grid(ResultRegister::class, function (Grid $grid) use ($idSubjectRegister) {
            $grid->resource('/admin/teacher/point');
            $user = Admin::user();
            $idUser = $user->id;
            $grid->model()->where('id_subject_register', $idSubjectRegister);
//            $grid->id('ID')->sortable();
//            $grid->rows(function (Grid\Row $row) {
//                $row->column('number', $row->number);
//            });
//            $grid->number('STT');
            $grid->id_user_student('MSSV')->display(function ($idStudent) {
                if (StudentUser::find($idStudent)->code_number) {
                    return StudentUser::find($idStudent)->code_number;
                } else {
                    return '';
                }
            })->sortable();
            $grid->column('Họ')->display(function () {
                if (StudentUser::find($this->id_user_student)->first_name) {
                    return StudentUser::find($this->id_user_student)->first_name;
                } else {
                    return '';
                }
            });
            $grid->column('Tên')->display(function () {
                if (StudentUser::find($this->id_user_student)->last_name) {
                    return StudentUser::find($this->id_user_student)->last_name;
                } else {
                    return '';
                }
            });
            $grid->id_subject_register('Mã HP')->display(function ($idSubjectRegister) {
                if (SubjectRegister::find($idSubjectRegister)->id) {
                    return SubjectRegister::find($idSubjectRegister)->id;
                } else {
                    return '';
                }
            })->sortable();
            $grid->id_subject('Môn')->display(function ($idSubject) {
                if (!empty(Subjects::find($idSubject)->name)) {
                    return Subjects::find($idSubject)->name;
                } else {
                    return '';
                }
            })->sortable();
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
            $grid->attendance('Điểm chuyên cần')->sortable();
            $grid->mid_term('Điểm giữa kì')->sortable();
            $grid->end_term('Điểm cuối kì')->sortable();
            $grid->column('Điểm tổng kết')->display(function () {
                if((!$this->attendance && $this->attendance !=0) || (!$this->mid_term && $this->mid_term !=0)  || (!$this->end_term && $this->end_term != 0)) {
                    return 'X';
                } else {
                    return (($this->attendance * $this->rate_attendance) +
                            ($this->mid_term * $this->rate_mid_term) +
                            ($this->end_term * $this->rate_end_term)) / 100;
                }

            })->setAttributes(['class'=>'finalPoint']);
            $idTimeRegister = ResultRegister::where('id_subject_register', $idSubjectRegister)->pluck('time_register');
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
//                $filter->in('time_register', 'Đợt ĐK')->select(TimeRegister::all()->pluck('name','id'));
//                $filter->between('created_at', 'Tạo vào lúc')->datetime();
            });
            $grid->disableActions();
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();
        });
    }
}
