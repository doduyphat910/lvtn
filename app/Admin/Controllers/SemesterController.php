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
use Illuminate\Support\Facades\Route;
use Illuminate\Support\MessageBag;

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

            $content->header('Năm, học kỳ');
            $content->description('Thêm học kỳ');

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
            $grid->rows(function (Grid\Row $row) {
                $row->column('number', $row->number);
            });
            $grid->number('STT');
//            $grid->id('ID')->sortable();
            $grid->name('Tên')->display(function ($name){
                if($name == 0) {
                    $name = 'Học kỳ hè';
                } elseif ($name == 1) {
                    $name = 'Học kỳ 1';
                } elseif ($name == 2) {
                    $name = 'Học kỳ 2';
                }
                return  '<a href="/admin/semester/' . $this->id . '/details">'.$name.'</a>';
            })->sortable();
            $grid->id_year('Tên năm')->display(function ($idyear) {
                if ($idyear) {
                    $name = Year::find($idyear)->name;
                    return "<span class='label label-info'>{$name}</span>";
                } else {
                    return '';
                }

            })->sortable();

            // $grid->time_start('Thời gian bắt đầu');
            // $grid->time_end('Thời gian kết thúc');
            $grid->actions(function ($actions) {
                $actions->append('<a href="/admin/semester/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->created_at('Tạo vào lúc')->sortable();
            $grid->updated_at('Cập nhật vào lúc')->sortable();
            $grid->filter(function($filter) {
                $filter->disableIdFilter();
                $filter->like('name', 'Tên học kì');
                $filter->where(function ($query){
                    $query->whereIn('id_year', $this->input);
                }, 'Năm')->multipleSelect(Year::all()->pluck('name','id'));
                $filter->between('created_at', 'Tạo vào lúc')->datetime();
            });
        });
    }

    protected function gridSubject($idSemester)
    {
        return Admin::grid(Subjects::class, function (Grid $grid) use ($idSemester) {
            $idSubjects = SemesterSubjects::where('semester_id', $idSemester)->pluck('subjects_id');
            $grid->model()->whereIn('id', $idSubjects);
            $grid->id('ID')->sortable();
            $grid->id('Mã môn học');
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
//            $grid->id('ID')->sortable();
            $grid->id('Mã học phần');
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

            // $grid->date_start('Ngày bắt đầu');
            // $grid->date_end('Ngày kết thúc');

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
//            $currentPath = Route::getFacadeRoot()->current()->uri();
//            if($currentPath == 'admin/semester/{semester}/edit') {
//                $form->number('credits_max', 'Số tín chỉ lớn nhất')->rules('integer|max:28');
//                $form->number('credits_min', 'Số tín chỉ nhỏ nhất')->rules('integer|min:1');
//            } else {
//                $form->hidden('credits_min')->value(10);
//                $form->hidden('credits_max')->value(28);
//            }
            // $form->date('time_start', 'Ngày bắt đầu')->attribute(['data-date-min-date' => date("Y-m-d")])->rules('required');
            // $form->date('time_end', 'Ngày kết thúc')->attribute(['data-date-min-date' => date("Y-m-d")])->rules('required');
//            $states = [
//                'on'  => ['value' => 1, 'text' => 'Mở', 'color' => 'success'],
//                'off' => ['value' => 0, 'text' => 'Đóng', 'color' => 'danger'],
//            ];
//            $form->switch('status', 'Trạng thái đăng ký môn')->states($states)->default('0');
            $form->listbox('subjects', 'Môn học')->options(Subjects::all()->pluck('name', 'id'));
            $form->display('created_at', 'Tạo vào lúc');
            $form->display('updated_at', 'Cập nhật vào lúc');
//            $form->saving(function (Form $form) use ($currentPath) {
            $form->saving(function (Form $form)  {
                if(($form->name == 1 || $form->name == 2) && $form->id_year == null )
                {
                    $error = new MessageBag([
                        'title'   => 'Lỗi',
                        'message' => 'Học kỳ ' . $form->name.' phải thuộc năm',
                    ]);
                    return back()->with(compact('error'));
                }elseif ($form->name == 0 && $form->id_year != null ) {
                    $error = new MessageBag([
                        'title'   => 'Lỗi',
                        'message' => 'Học kỳ hè không được có năm',
                    ]);
                    return back()->with(compact('error'));
                }
                    if ($form->name && $form->id_year) {
                        $count = Semester::where('name', $form->name)->where('id_year', $form->id_year)->where('id', '!=' ,$form->model()->id)->count();
                        if ($count > 1) {
                            $nameYear = Year::find($form->id_year)->name;
                            $error = new MessageBag([
                                'title' => 'Lỗi',
                                'message' => 'Đã tồn tại học kỳ ' . $form->name . ' thuộc ' . $nameYear,
                            ]);
                            return back()->with(compact('error'));
                        }
                    }
                //check subject in semester
//                if($form->subjects != null) {
//                    $semesterSummer = Semester::where('name', 0)->pluck('id')->toArray();
//                    $count = SemesterSubjects::where('subjects_id', $form->subjects)->whereNotIn('semester_id', $semesterSummer)->pluck('semester_id');
//                    dd($count);
//                }

            });
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
