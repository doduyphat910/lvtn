<?php

namespace App\Admin\Controllers;
use App\Admin\Extensions\ModelFormCustom;
use App\Models\Rate;

use App\Models\Semester;
use App\Models\SubjectGroup;
use App\Models\Subjects;
use App\Models\Year;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\MessageBag;

class RateController extends Controller
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

            $content->header('Tỷ lệ điểm');
            $content->description('Danh sách tỷ lệ điểm');

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

            $rate = Rate::findOrFail($id);
            $content->header('Tỷ lệ điểm');
            $content->description($rate->name);

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

            $content->header('Tỷ lệ điểm');
            $content->description('Thêm tỷ lệ điểm');

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
        return Admin::grid(Rate::class, function (Grid $grid) {
            $grid->rows(function (Grid\Row $row) {
                $row->column('number', $row->number);
            });
            $grid->number('STT');
//            $grid->id('ID')->sortable();
            $grid->name('Tỷ lệ')->display(function ($name){
                return  '<a href="/admin/rate/' . $this->id . '/details">'.$name.'</a>';
            })->sortable();
            $grid->attendance('Chuyên cần')->sortable();
            $grid->mid_term('Giữa kì')->sortable();
            $grid->end_term('Cuối kì')->sortable();
            $grid->actions(function ($actions) {
                $actions->append('<a href="/admin/rate/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->created_at('Tạo vào lúc')->sortable();
            $grid->updated_at('Cập nhạt vào lúc')->sortable();
            $grid->filter(function($filter) {
                $filter->disableIdFilter();
                $filter->like('name', 'Tên');
                $filter->equal('attendance', 'Chuyên cần');
                $filter->equal('mid_term', 'Giữa kì');
                $filter->equal('end_term', 'Cuối kì');
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
        return Admin::form(Rate::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('name', 'Tên tỷ lệ')->rules('required');
            $form->number('attendance', 'Tỉ lệ điểm chuyên cần')->rules('integer|max:30')->rules('integer|min:0');
            $form->number('mid_term', 'Tỉ lệ điểm giữa kì')->rules('integer|max:50')->rules('integer|min:0');
            $form->number('end_term', 'Tỉ lệ điểm cuối kì')->rules('integer|max:100')->rules('integer|min:50');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
            $form->saving(function (Form $form){
                if($form->attendance + $form->mid_term + $form->end_term != 100){
                    $error = new MessageBag([
                        'title'   => 'Lỗi',
                        'message' => 'Tỷ lệ điểm không đủ 100%',
                    ]);
                    return back()->with(compact('error'));
                }
            });
        });
    }

    //details
    public function details($id){
        return Admin::content(
            function (Content $content) use ($id) {
                $rate = Rate::findOrFail($id);
                $content->header('Tỷ lệ điểm');
                $content->description($rate->name);
                $content->body($this->detailsView($id));
            });
    }

    public function detailsView($id){
        $form = $this->form()->view($id);
        $gridSubject = $this->gridSubject($id)->render();
        return view('vendor.details',
            [
                'template_body_name' => 'admin.Rate.info',
                'form' => $form,
                'gridSubject' => $gridSubject,

            ]
        );
    }

    protected function gridSubject($idRate)
    {
        return Admin::grid(Subjects::class, function (Grid $grid) use ($idRate) {
            $grid->model()->where('id_rate',$idRate);
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
                    $year = Semester::find($arraySemester)->year()->get();
                    if(!empty($year['0'])) {
                        $nameYear = $year['0']->name;
                    } else {
                        return '';
                    }
                    if($year['0']->id % 2 == 0){
                        return "<span class='label label-info'>{$nameSemester} - {$nameYear}</span>"  ;
                    } else {
                        return "<span class='label label-success'>{$nameSemester} - {$nameYear}</span>"  ;
                    }
                }, $arraySemester);
                return join('&nbsp;', $name);
            })->sortable();
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
                $actions->append('<a href="/admin/subject/' . $actions->getKey() . '/edit"><i class="fa fa-edit" ></i></a>');
                $actions->append('<a href="/admin/subject/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });

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
                    $nameYear = Year::where('id', $semester['id_year'])->first();
                    if($semester['name'] == 0) {
                        $semester['name'] = 'hè';
                    }
                    if(!empty($nameYear)){
                        $optionSemesters += [$semester['id'] => 'Học kỳ '. $semester['name']. ' - ' . $nameYear->name];
                    } else {
                        $optionSemesters += [$semester['id'] => 'Học kỳ '. $semester['name']];
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
