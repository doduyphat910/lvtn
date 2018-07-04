<?php

namespace App\Admin\Controllers;
use App\Models\Rate;

use App\Models\Semester;
use App\Models\SubjectGroup;
use App\Models\Subjects;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\MessageBag;

class RateController extends Controller
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
                $content->header('Học kỳ');
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
            $grid->id('Mã môn học');
            $grid->name('Tên môn học')->display(function ($name){
                return  '<a href="/admin/subject/' . $this->id . '/details">'.$name.'</a>';
            });
            $grid->credits('Số tín chỉ');
            $grid->credits_fee('Số tín chỉ học phí');
            $grid->id_semester('Học kỳ')->display(function ($idSemester) {
                if($idSemester) {
                    return Semester::find($idSemester)->name;
                } else {
                    return '';
                }
            });
            $grid->id_subject_group('Nhóm môn')->display(function ($id) {
                if($id) {
                    return SubjectGroup::find($id)->name;
                } else {
                    return '';
                }
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
                    return Rate::find($this->id_rate)->mid_term;
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
            $grid->created_at();
            $grid->updated_at();
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
            $grid->disableFilter();
        });
    }
}
