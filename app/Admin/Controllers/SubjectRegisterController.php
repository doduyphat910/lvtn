<?php

namespace App\Admin\Controllers;

use App\Models\Classroom;
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
use Illuminate\Support\Facades\Route;
use Illuminate\Support\MessageBag;

class SubjectRegisterController extends Controller
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
            $grid->rows(function (Grid\Row $row) {
                $row->column('number', $row->number);
            });
            $grid->number('STT');
//            $grid->id('ID')->sortable();
            $grid->code_subject_register('Mã học phần')->display(function ($name){
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
            $grid->column('Phòng')->display(function () {
                $idClassroom = TimeStudy::where('id_subject_register', $this->code_subject_register)->pluck('id_classroom')->toArray();
                $classRoom = Classroom::whereIn('id', $idClassroom)->pluck('name')->toArray();
                $classRoom = array_map(function ($classRoom) {
                    return "<span class='label label-danger'>{$classRoom}</span>";
                }, $classRoom);
                return join('&nbsp;', $classRoom);
            })->sortable();
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
            $grid->id_time_register('Đợt đăng ký')->display(function ($idTimeRegister){
                $timeRegister = TimeRegister::find($idTimeRegister);
                if(!empty($timeRegister->name)){
                    return "<span class='label label-info'>{$timeRegister->name}</span>";
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
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(SubjectRegister::class, function (Form $form) {
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
            $form->text('code_subject_register', 'Mã học phần')->rules(function ($form){
//
                return 'required|unique:subject_register,code_subject_register,'.$form->model()->code_subject_register.',code_subject_register,deleted_at,NULL';
            });
            $form->select('id_subjects', 'Môn học')->options(Subjects::all()->pluck('name', 'subject_code'))->rules('required');
            $form->select('id_user_teacher', 'Giảng viên')->options(UserAdmin::where('type_user', '0')->pluck('name', 'id'))->rules('required');
            $form->hidden('qty_current', 'Số lượng hiện tại')->value('0');
            $form->number('qty_min', 'Số lượng tối thiểu')->rules('integer|min:5');
            $form->number('qty_max', 'Số lượng tối đa')->rules('integer|min:10');
            $form->select('id_time_register', 'Đợt đăng ký')->options(TimeRegister::all()->pluck('name', 'id'));
            $form->date('date_start', 'Ngày bắt đầu')->placeholder('Ngày bắt đầu')->rules('required');
            $form->date('date_end', 'Ngày kết thúc')->placeholder('Ngày kết thúc')->rules('required');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
            $form->hasMany('time_study', 'Thời gian học', function (Form\NestedForm $form) {
                $options = ['2'=>'Thứ 2', '3'=>'Thứ 3', '4'=>'Thứ 4', '5'=>'Thứ 5', '6'=>'Thứ 6', '7'=>'Thứ 7', '8'=>'Chủ nhật'];
                $form->select('day', 'Ngày học')->options($options);
                $form->select('id_classroom', 'Phòng học')->options(Classroom::all()->pluck('name', 'id'));
                $timeStart = Timetable::all()->pluck('time_start', 'time_start' );
                $timeEnd = Timetable::all()->pluck('time_end', 'time_end' );
                $form->select('time_study_start', 'Giờ học bắt đầu')->options($timeStart);
                $form->select('time_study_end', 'Giờ học kết thúc')->options($timeEnd);
            })->rules('required');
            $form->hidden('id_semester');
            $form->saving(function (Form $form){
                //add more semester
                $subject = Subjects::find($form->id_subjects);
                $idSemester = $subject->semester()->pluck('id')->toArray();
                $form->id_semester = $idSemester['0'] ;
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
//                $idSubjectRegisters = SubjectRegister::where('id_classroom', $form->id_classroom)->pluck('id');
                $currentPath = Route::getFacadeRoot()->current()->uri();
                $subjectToTime = SubjectRegister::where('id_time_register', $form->id_time_register)->pluck('code_subject_register')->toArray();
                if($currentPath == "admin/subject_register/{subject_register}") {
                    $timeStudys = TimeStudy::where('id_subject_register', '!=',$form->model()->code_subject_register)->whereIn('id_subject_register', $subjectToTime)->get()->toArray();
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
        });
    }

    public function details($id){
        return Admin::content(function (Content $content) use ($id) {
            $subjectRegister = SubjectRegister::findOrFail($id);
            $content->header('Học phần');
            $content->description($subjectRegister->code_subject_register);
            $content->body($this->detailsView($id));
        });
    }

    public function detailsView($id){
        $form = $this->form()->view($id);
        return view('vendor.details',
            [
                'template_body_name' => 'admin.SubjectRegister.info',
                'form' => $form

            ]
        );
    }
}
