<?php

namespace App\Admin\Controllers;

use App\Models\Classroom;
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

            $grid->id('ID')->sortable();
            $grid->code_subject_register('Mã học phần')->display(function ($name){
                return  '<a href="/admin/subject_register/' . $this->id . '/details">'.$name.'</a>';
            });
            $grid->id_subjects('Môn học')->display(function ($idSubject){
                if($idSubject){
                    $name = Subjects::find($idSubject)->name;
                    return "<span class='label label-info'>{$name}</span>";
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
            $grid->qty_min('Số lượng tối thiểu');
            $grid->qty_max('Số lượng tối đa');
//            $grid->time_study_start('Giờ học bắt đầu');
//            $grid->time_study_end('Giờ học kết thúc');
            $grid->date_start('Ngày bắt đầu');
            $grid->date_end('Ngày kết thúc');
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
            $grid->created_at('Tạo vào lúc');
            $grid->updated_at('Cập nhật vào lúc');

            //action
            $grid->actions(function ($actions) {
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
                $('.remove')[0].remove();
            }
            if(action=="details")
            {
                $('.remove').remove();
                $('.add').remove();
            }

        });
EOT;
            Admin::script($script);
            $form->display('id', 'ID');
            $form->text('code_subject_register', 'Mã học phần')->rules(function ($form){
                return 'required|unique:subject_register,code_subject_register,'.$form->model()->id.',id';
            });
            $form->select('id_subjects', 'Môn học')->options(Subjects::all()->pluck('name', 'id'))->rules('required');
            $form->select('id_classroom', 'Phòng học')->options(Classroom::all()->pluck('name', 'id'))->rules('required');
            $form->select('id_user_teacher', 'Giảng viên')->options(UserAdmin::where('type_user', '0')->pluck('name', 'id'))->rules('required');
            $form->hidden('qty_current', 'Số lượng hiện tại')->value('0');
            $form->number('qty_min', 'Số lượng tối thiểu')->rules('integer|min:5');
            $form->number('qty_max', 'Số lượng tối đa')->rules('integer|min:10');
            $form->date('date_start', 'Ngày bắt đầu')->placeholder('Ngày bắt đầu')->rules('required');
            $form->date('date_end', 'Ngày kết thúc')->placeholder('Ngày kết thúc')->rules('required');
//            $form->select('id_time_register', 'Thời gian đăng ký')->options(TimeRegister::all()->pluck('name', 'id'));
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
            $form->hasMany('time_study', 'Thời gian học', function (Form\NestedForm $form) {
                $options = ['2'=>'Thứ 2', '3'=>'Thứ 3', '4'=>'Thứ 4', '5'=>'Thứ 5', '6'=>'Thứ 6', '7'=>'Thứ 7', '8'=>'Chủ nhật'];
                $form->select('day', 'Ngày học')->options($options);
                $form->time('time_study_start', 'Giờ học bắt đầu');
                $form->time('time_study_end', 'Giờ học kết thúc');
            })->rules('required');
            $form->hidden('id_semester');
            $form->saving(function (Form $form){
                //add more semester
                $subject = Subjects::find($form->id_subjects);
                $idSemester = $subject->semester()->pluck('id')->toArray();
                $form->id_semester = $idSemester['0'] ;
                //check time study
                foreach($form->time_study as $timeStudy) {
                    if($timeStudy['time_study_start'] >= $timeStudy['time_study_end']) {
                        $error = new MessageBag([
                            'title'   => 'Lỗi',
                            'message' => 'Giờ học bắt đầu không được lớn hơn hoặc bằng giờ học kết thúc',
                        ]);
                        return back()->with(compact('error'));
                    }
                }
                //check conditions register
                $idSubjectRegisters = SubjectRegister::where('id_classroom', $form->id_classroom)->pluck('id');
                $timeStudys = TimeStudy::all()->toArray();
                if(count($idSubjectRegisters) > 0) {
                    foreach($form->time_study as $day) {
                        foreach ($timeStudys as $timeStudy) {
                            if($day['day'] == $timeStudy['day']) {
                                if (
                                    ($day['time_study_end'] <= $timeStudy['time_study_start'] && $day['time_study_end'] >= $timeStudy['time_study_end'] )||
                                    ($day['time_study_start'] <= $timeStudy['time_study_start'] && $day['time_study_start'] >= $timeStudy['time_study_end'] )||
                                    ($day['time_study_start'] <= $timeStudy['time_study_start'] && $day['time_study_end'] <= $timeStudy['time_study_end'] )||
                                    ($day['time_study_start'] <= $timeStudy['time_study_start'] && $day['time_study_end'] >= $timeStudy['time_study_end'] )
                                ) {
                                    $error = new MessageBag([
                                        'title'   => 'Lỗi',
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
