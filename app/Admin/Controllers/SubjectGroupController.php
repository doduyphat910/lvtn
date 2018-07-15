<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ModelFormCustom;
use App\Models\Rate;
use App\Models\Semester;
use App\Models\SubjectGroup;

use App\Models\Subjects;
use App\Models\TimeRegister;
use App\Models\UserAdmin;
use App\Models\Year;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class SubjectGroupController extends Controller
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
            $content->description('Danh sách nhóm môn học');

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

            $subjectGroup = SubjectGroup::findOrFail($id);
            $content->header('Nhóm môn học');
            $content->description($subjectGroup->name);

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

            $content->header('Nhóm môn học');
            $content->description('Thêm nhóm môn học');

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
        return Admin::grid(SubjectGroup::class, function (Grid $grid) {
            $grid->rows(function (Grid\Row $row) {
                $row->column('number', $row->number);
            });
            $grid->number('STT');
//            $grid->id('ID')->sortable();
            $grid->model()->orderBy('created_at', 'DESC');
            $grid->name('Tên nhóm môn')->display(function ($name){
                return  '<a href="/admin/subject_group/' . $this->id . '/details">'.$name.'</a>';
            });
            $grid->actions(function ($actions) {
                $actions->append('<a href="/admin/subject_group/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->created_at('Tạo vào lúc');
            $grid->updated_at('Cập nhật vào lúc');
            $grid->filter(function($filter) {
                $filter->disableIdFilter();
                $filter->like('name', 'Tên nhóm môn');
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
        return Admin::form(SubjectGroup::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('name', 'Nhóm môn học')->rules('required');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    //details
    public function details($id){
        return Admin::content(function (Content $content) use ($id) {
            $subjectGroup = SubjectGroup::findOrFail($id);
            $content->header('Nhóm môn học');
            $content->description($subjectGroup->name);
            $content->body($this->detailsView($id));
        });
    }

    public function detailsView($id){
        $form = $this->form()->view($id);
        $group = SubjectGroup::find($id);
        $idSubject = $group->subject()->pluck('id')->toArray();
        $gridSubject = $this->gridSubject($idSubject)->render();
        return view('vendor.details',
            [
                'template_body_name' => 'admin.SubjectGroup.info',
                'form' => $form,
                'gridSubject' => $gridSubject

            ]
        );
    }

    protected function gridSubject($idSubject)
    {
        return Admin::grid(Subjects::class, function (Grid $grid) use ($idSubject) {
            $grid->resource('admin/subject');
            $grid->model()->whereIn('id', $idSubject);
//            $grid->id('ID')->sortable();
            $grid->rows(function (Grid\Row $row) {
                $row->column('number', $row->number);
            });
            $grid->number('STT');
            $grid->id('Mã môn học')->sortable();
            $grid->name('Tên môn học')->display(function ($name){
                return  '<a href="/admin/subject/' . $this->id . '/details">'.$name.'</a>';
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
                            return  "<span class='label label-primary'>$nameSemester</span>"  ;
                        } else {
                            return "<span class='label label-info'>{$nameSemester} - {$nameYear}</span>"  ;
                        }
                    } else {
                        if($nameSemester == 'Học kỳ hè') {
                            return  "<span class='label label-primary'>$nameSemester</span>"  ;
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
            })->sortable();

            $grid->id_rate('Tỷ lệ chuyên cần')->display(function ($rate){
                if($rate){
                    return Rate::find($rate)->attendance;
                } else {
                    return '';
                }
            })->sortable();
            $grid->column('Tỷ lệ giữa kì')->display(function (){
                if($this->id_rate) {
                    return Rate::find($this->id_rate)->mid_term;
                } else {
                    return '';
                }
            })->sortable();
            $grid->column('Tỷ lệ cuối kì')->display(function (){
                if($this->id_rate) {
                    return Rate::find($this->id_rate)->end_term;
                } else {
                    return '';
                }
            })->sortable();
            $grid->created_at('Tạo vào lúc')->sortable();
            $grid->updated_at('Cập nhật vào lúc')->sortable();
            //action
            $grid->actions(function ($actions) {
                $actions->disableEdit();
                $actions->disableDelete();
                $actions->append('<a href="/admin/subjects/' . $actions->getKey() . '/edit"><i class="fa fa-edit" ></i></a>');
                $actions->append('<a href="/admin/subject/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            //disable
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();
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
        });
    }
}
