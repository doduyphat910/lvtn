<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ModelFormCustom;
use App\Admin\Extensions\Subject\AdminMissID;
use App\Admin\Extensions\Subject\FormID;
use App\Models\Classroom;
use App\Models\Rate;
use App\Models\SubjectRegister;
use App\Models\Subjects;

use App\Models\TimeRegister;
use App\Models\TimeStudy;
use App\Models\UserAdmin;
use App\Models\Year;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\Semester;
use App\Models\SubjectGroup;
use Illuminate\Support\Facades\Route;

class SubjectsController extends Controller
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

            $subject = Subjects::findOrFail($id);
            $content->header('Môn học');
            $content->description($subject->name);

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

            $content->header('Môn học');
            $content->description('Thêm môn học');

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

//            $grid->model()->orderBy('created_at','DESC');
//            $grid->rows(function (Grid\Row $row) {
//                $row->column('number', $row->number);
//            });
//            $grid->number('STT');
//            $grid->model()->where();
//            $grid->id('ID')->sortable();
            $grid->id('Mã môn học')->sortable();
            $grid->name('Tên môn học')->display(function ($name){
                return  '<a href="/admin/subject/' . $this->id . '/details" >'.$name.'</a>';
            })->sortable();
            $grid->credits('Số tín chỉ')->sortable();
            $grid->credits_fee('Số tín chỉ học phí')->sortable();
            $grid->column('Học kỳ - Năm')->display(function () {
                $id = $this->id;
                $subject = Subjects::find($id);
                $arraySemester = $subject->semester()->pluck('id')->toArray();
                $name = array_map( function ($arraySemester){
                    $nameSemester = Semester::find($arraySemester)->name;
                    switch ($nameSemester) {
                        case 0:
                            $nameSemester = 'Học kỳ hè';
                            break;
                        case 1:
                            $nameSemester = 'Học kỳ 1';
                            break;
                        case 2:
                            $nameSemester = 'Học kỳ 2';
                            break;
                        default:
                            $nameSemester = '';
                            break;
                    }
//                    $year = Semester::find($arraySemester)->year()->get();
                    $year = Semester::find($arraySemester)->year()->first();
                    if(!empty($year)) {
                        $nameYear = $year->name;

                    } else {
                        $nameYear = '';
                    }
//                    $nameYear = $year['0']->name;
                    if(substr($nameYear,4,5) % 2 == 0){
                        if($nameSemester == 'Học kỳ hè') {
//                            return  "<span class='label label-primary'>$nameSemester</span>"  ;
                        } else {
                            return "<span class='label label-info'>{$nameSemester} - {$nameYear}</span>"  ;
                        }
                    } else {
                        if($nameSemester == 'Học kỳ hè') {
//                            return  "<span class='label label-primary'>$nameSemester</span>"  ;
                        } else {
                            return "<span class='label label-success'>{$nameSemester} - {$nameYear}</span>";
                        }
                    }
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
//            $grid->id_rate('Tỷ lệ chuyên cần')->display(function ($rate){
//                if($rate){
//                    return Rate::find($rate)->attendance;
//                } else {
//                    return '';
//                }
//            })->sortable();
//            $grid->column('Tỷ lệ giữa kì')->display(function (){
//                if($this->id_rate) {
//                    return Rate::find($this->id_rate)->mid_term;
//                } else {
//                    return '';
//                }
//            })->sortable();
//            $grid->column('Tỷ lệ cuối kì')->display(function (){
//                if($this->id_rate) {
//                    return Rate::find($this->id_rate)->end_term;
//                } else {
//                    return '';
//                }
//            })->sortable();
            $grid->created_at('Tạo vào lúc')->sortable();
            $grid->updated_at('Cập nhật vào lúc')->sortable();
            //action
            $grid->actions(function ($actions) {
                $actions->append('<a href="/admin/subject/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->filter(function($filter){
                $filter->disableIdFilter();
                $filter->like('id', 'Mã môn học');
                $filter->like('name', 'Tên môn học');
                $filter->like('credits', 'Tín chỉ');
                $filter->like('credits_fee', 'Tín chỉ học phí');
                $semesters = Semester::all()->toArray();
                $optionSemesters = [];
                foreach($semesters as $semester) {
                    if($semester['name'] == 0) {
                        $optionSemesters += [$semester['id'] => 'Học kỳ hè'];
                    } else {
                        $nameYear = Year::where('id', $semester['id_year'])->first();
                        $optionSemesters += [$semester['id'] => 'Học kỳ '. $semester['name']. ' - ' . $nameYear->name];
                    }
                }
                $filter->where(function ($query){
                    $input = $this->input;
                    $semester = Semester::where('id',$input)->first();
                    $idSubject = $semester->subjects()->pluck('id')->toArray();
                    $query->whereIn('id', $idSubject);
                }, 'Học kì')->select($optionSemesters);
                $filter->where(function ($query){
                    $input = $this->input;
                    $subjectGroup = SubjectGroup::where('id',$input)->first();
                    $idSubject = $subjectGroup->subject()->pluck('id')->toArray();
                    $query->where(function ($query) use ($idSubject) {
                        $query->whereIn('id', $idSubject);
                    });
//                    $query->whereIn('id', $idSubject);
                }, 'Nhóm môn học')->multipleSelect(SubjectGroup::all()->pluck('name', 'id'));
                $rates = Rate::all();
                $arrayRate = [];
                foreach($rates as $rate) {
                    $arrayRate += [$rate['id'] => $rate['attendance'] . '-'.  $rate['mid_term'] .'-' .$rate['end_term']];
                }
                $filter->where(function ($query){
                    $input = $this->input;
//                    $idRate = Rate::where('attendance', '%'. $input .'%')->pluck('id')->toArray();
                    $query->whereIn('id_rate', $input);
                }, 'Tỷ lệ điểm')->multipleSelect($arrayRate);
//                $filter->in('id_subject1', 'Môn học trước')->multipleSelect(Subjects::all()->pluck('name', 'id'));
//                $filter->in('id_subject2', 'Môn học song song')->multipleSelect(Subjects::all()->pluck('name', 'id'));
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
        return AdminMissID::form(Subjects::class, function (FormID $form) {

//            $form->display('id', 'ID');
//            $form->text('id', 'Mã môn học')->rules(function ($form){
//                return 'required|unique:subjects,'.$form->model()->id.',id,deleted_at,NULL';
//            });
            $currentPath = Route::getFacadeRoot()->current()->uri();
            if($currentPath != "admin/subjects/{subject}/edit"){
                $form->text('id', 'Mã môn học')->rules(function ($form){
                    if (!$id = $form->model()->id) {
                        return 'required|unique:subjects,id';
                    }
//                return 'required|unique:subjects,'.$form->model()->id.',id,deleted_at,NULL';

                });
            } else {
                $form->display('id', 'ID');
            }

            $form->text('name','Tên môn học')->rules('required');
            $form->number('credits','Tín chỉ')->rules('integer|min:1|max:6');
            $form->number('credits_fee', 'Tín chỉ học phí')->rules('integer|min:1|max:15');
//            $form->select('id_semester', 'Học kỳ')->options(Semester::all()->pluck('name', 'id'));
            $form->multipleSelect('subject_group', 'Nhóm môn')->options(SubjectGroup::all()->pluck('name', 'id'))->rules('required');
            $rates = Rate::all();
            $arrayRate = [];
            foreach($rates as $rate) {
                $arrayRate += [$rate['id'] => $rate['attendance'] . '-'.  $rate['mid_term'] .'-' .$rate['end_term']];
            }
            $form->select('id_rate', 'Tỷ lệ điểm')->options($arrayRate)->rules('required');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
            $form->disableReset();
        });
    }

    protected function gridSubjectRegister($idSubjects)
    {
        return Admin::grid(SubjectRegister::class, function (Grid $grid) use ($idSubjects) {
            $grid->model()->where('id_Subjects', $idSubjects)->orderBy('created_at', 'DESC');
//            $grid->id('ID')->sortable();
//            $grid->rows(function (Grid\Row $row) {
//                $row->column('number', $row->number);
//            });
//            $grid->number('STT');
            $grid->id('Mã học phần')->sortable();
            $grid->id_subjects('Môn học')->display(function ($idSubject){
                if($idSubject){
                    return Subjects::find($idSubject)->name;
                } else {
                    return '';
                }
            })->sortable();
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
            $grid->id_time_register('Đợt đăng ký')->display(function ($idTimeRegister){
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
            $grid->qty_current('Số lượng hiện tại')->sortable();

            $grid->date_start('Ngày bắt đầu')->sortable();
            $grid->date_end('Ngày kết thúc')->sortable();

            $grid->created_at('Tạo vào lúc')->sortable();
            $grid->updated_at('Cập nhật vào lúc')->sortable();
            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->filter(function($filter){
                $filter->disableIdFilter();
                $filter->like('id', 'Mã học phần');
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
