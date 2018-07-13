<?php

namespace App\Admin\Controllers;

use App\Models\ResultRegister;
use App\Models\StudentUser;
use App\Models\Subjects;
use App\Models\TimeRegister;

use App\Models\UserSubject;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\Semester;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\MessageBag;

class TimeRegisterController extends Controller
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

            $content->header('TG Đăng ký');
            $content->description('DS TG Đăng ký');
            $script = <<<EOT
            if (location.href.indexOf('reload')==-1)
            {
               location.href=location.href+'?reload';
            }
EOT;
//            Admin::script($script);
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

            $content->header('TG Đăng ký');
            $content->description('Thêm TG Đăng ký');

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
        return Admin::grid(TimeRegister::class, function (Grid $grid) {
            $grid->model()->orderBy('created_at', 'DESC');
            $grid->rows(function (Grid\Row $row) {
                $row->column('number', $row->number);
            });
            $grid->number('STT');
//            $grid->id('ID')->sortable();
            $grid->name('Tên')->display(function ($name){
                return '<a href="/admin/time-register/' . $this->id . '/details" >'.$name.'</a>';
            })->sortable();
            $grid->time_register_start('Thời gian bắt đầu')->sortable();
            $grid->time_register_end('Thời gian kết thúc')->sortable();
            $grid->semester('Học kỳ')->display(function ($semester) {
                switch ($semester) {
                    case 0: return "<span class='label label-info'>Học kỳ hè</span>";
                    break;
                    case 1: return "<span class='label label-info'>Học kỳ 1</span>";
                    break;
                    case 2: return "<span class='label label-info'>Học kỳ 2</span>";
                    break;
                    default:
                        return '';
                }
            })->sortable();
            $grid->credits_max('Số TC tối đa')->sortable();
            $grid->credits_min('Số TC tối thiểu')->sortable();
//            $grid->school_year('Khóa được ĐK')->display(function ($schoolYears){
//                $schoolYears = array_map(function ($schoolYears){
//                    if($schoolYears == 'All') {
//                        return "<span class='label label-primary'>Tất cả</span>";
//                    } elseif ($schoolYears ) {
////                        $arraySchoolYear = StudentUser::distinct('school_year')->orderBy('school_year', 'DESC')->limit(6)->pluck('school_year')->toArray();
////                        array_unshift($arraySchoolYear, 'Tất cả');
//                        return "<span class='label label-primary'>{$schoolYears}</span>";
//                    }
//                    else {
//                        return '';
//                    }
//                }, $schoolYears);
//                return join('&nbsp;', $schoolYears);
//
//            });
            $grid->status('Trạng thái')->display(function ($status){
                if($status == 1){
                    return "<span class='label label-success'>Đang mở</span>";
                } else {
                    return "<span class='label label-danger'>Đang đóng</span>";

                }
            })->sortable();
            $grid->created_at('Tạo vào lúc')->sortable();
            $grid->updated_at('Cập nhật vào lúc')->sortable();
            $grid->actions(function ($actions) {
                $actions->append('<a href="/admin/time-register/'.$actions->getKey().'/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->filter(function($filter) {
                $filter->disableIdFilter();
                $filter->like('name', 'Tên đợt');
                $options = [0 => 'Học kỳ hè', 1 => 'Học kỳ 1', 2 => 'Học kỳ 2'];
                $filter->equal('semester', 'Học kỳ')->select($options);
                $filter->equal('credits_max', 'Số tín chỉ tối đa');
                $filter->equal('credits_min', 'Số tín chỉ tối thiểu');
                $filter->equal('status', 'Trạng thái')->radio([
                    0    => 'Đang đóng',
                    1    => 'Đang mở',
                ]);
                $filter->between('time_register_start', 'Thời gian bắt đầu')->datetime();
                $filter->between('time_register_end', 'Thời gian kết thúc')->datetime();
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
        return Admin::form(TimeRegister::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('name', 'Tên')->rules('required')
                ->help('Bạn nên đặt tên là HK - Năm học (VD:HK2 - Năm 2018-2019 ) ');
            $form->datetimeRange('time_register_start', 'time_register_end', 'Thời gian đăng ký')
                ->rules('required');
            $options = [0 => 'Học kỳ hè', 1 => 'Học kỳ 1', 2 => 'Học kỳ 2'];
            $form->select('semester', 'Học kỳ')->options($options);
            $schoolYear = StudentUser::distinct('school_year')->orderBy('school_year', 'DESC')->limit(6)->pluck('school_year', 'school_year')->toArray();
            $schoolYear['0'] = "Tất cả";
            ksort($schoolYear);
            $form->multipleSelect('school_year','Khóa đăng ký')->options($schoolYear);
            $states = [
                'on'  => ['value' => 1, 'text' => 'Mở', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => 'Đóng', 'color' => 'danger'],
            ];
            $form->switch('status', 'Trạng thái')->states($states)->default('0');
            $options = ['All'=>'Tất cả', '1'=>'Chuyên cần', '2'=>'Giữa kì', '3'=>'Cuối kì' ];
            $options2 = ['AllPoint'=>'Tất cả', '1'=>'Chuyên cần', '2'=>'Giữa kì', '3'=>'Cuối kì' ];

            $form->checkbox('status_import', 'Trạng thái import')->options($options);
            $form->checkbox('status_edit_point', 'Trạng thái sửa điểm')->options($options2);
            $script = <<<EOT
            $(function () {
                $('input[value="All"]').on('ifChecked', function(event){
                  $('input[name="status_import[]"]').iCheck('check');
                });
                $('input[value="All"]').on('ifUnchecked', function(event){
                  $('input[name="status_import[]"]').iCheck('uncheck');
                });
                 $('input[value="AllPoint"]').on('ifChecked', function(event){
                  $('input[name="status_edit_point[]"]').iCheck('check');
                });
                $('input[value="AllPoint"]').on('ifUnchecked', function(event){
                  $('input[name="status_edit_point[]"]').iCheck('uncheck');
                });
            });
EOT;
            Admin::script($script);
            $form->saving(function (Form $form) {
                if($form->school_year['0'] == "0" || $form->school_year['0'] == null  ) {
                    $form->school_year = 'All';
                }
                if($form->status_import['0'] == 'All' || $form->status_import['0'] == '1' && $form->status_import['1'] == '2' && $form->status_import['2'] == '3'){
                    $form->status_import = 'All';
                }
                if(in_array('AllPoint',$form->status_edit_point)){
                    $form->status_edit_point = ["1","2","3"];
                }
                if($form->status == 'on' ) {
                    if (!$id = $form->model()->id) {
                        $countStatusActive = TimeRegister::where('status', 1)->get()->count();
                        if ($countStatusActive > 0) {
                            $error = new MessageBag([
                                'title' => 'Lỗi',
                                'message' => 'Có đợt đăng ký đang mở',
                            ]);
                            return back()->with(compact('error'));
                        }
                    }
                }
            });
            $currentPath = Route::getFacadeRoot()->current()->uri();
            if($currentPath == "admin/time-register/{time_register}/edit") {
                $form->number('credits_max', 'Số tín chỉ lớn nhất')->rules('integer|max:28');
                $form->number('credits_min', 'Số tín chỉ nhỏ nhất')->rules('integer|min:1');
            } else {
                $form->hidden('credits_min')->value(10);
                $form->hidden('credits_max')->value(28);
            }
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');

        });
    }

    protected function details($id){
        return Admin::content(function (Content $content) use ($id) {
            $script = <<<EOT
            if (location.href.indexOf('reload')==-1)
            {
               location.href=location.href+'?reload';
            }
EOT;
            Admin::script($script);
//            header("Refresh:0");
            $time = TimeRegister::findOrFail($id);
            $content->header('TG Đăng ký');
            $content->description($time->name);
            $content->body($this->detailsView($id));
        });
    }

    protected function detailsView($id)
    {
        $form = $this->form()->view($id);
        $arrIDSubject = UserSubject::where('id_time_register',$id)->select('id_subject')->distinct()->pluck('id_subject')->toArray();
        $dataStudents = [];
        foreach($arrIDSubject as $idSubject) {
            $countStudent = UserSubject::where('id_subject',$idSubject)->where('id_time_register',$id)->count('id_user');
            $dataStudents[$idSubject] = $countStudent;
        }
        arsort($dataStudents);
        $requestRegister = array_slice($dataStudents, 0, 6);
        $subject = json_encode(array_keys($requestRegister));
        $nameSubjects = Subjects::whereIn('id',array_keys($requestRegister))->pluck('name', 'id')->toArray();
        $student = json_encode(array_values($requestRegister));
        //chart 2
        $arrClass = StudentUser::distinct('school_year')->orderBy('school_year', 'DESC')->limit(6)->pluck('school_year')->toArray();
        $countClass = [];
        $arrUser = ResultRegister::where('time_register', $id)->pluck('id_user_student')->toArray();
        foreach($arrClass as $class) {
            $countStudentClass = StudentUser::whereIn('id', $arrUser)->where('school_year',$class)->count();
            array_push($countClass, $countStudentClass);
        }
        $class = json_encode($arrClass);
        $countClass = json_encode($countClass);
        return view('vendor.details',
            [
                'template_body_name' => 'admin.TimeRegister.info',
                'form' => $form,
                'subject' => $subject,
                'nameSubjects' => $nameSubjects,
                'student' => $student,
                'class' => $class,
                'countClass' => $countClass

            ]
        );
    }

}
