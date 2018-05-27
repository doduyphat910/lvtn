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

            $content->header('header');
            $content->description('description');

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
            $grid->id('ID')->sortable();
            $grid->name('Tên nhóm môn')->display(function ($name){
                return  '<a href="/admin/subject_group/' . $this->id . '/details">'.$name.'</a>';
            });
            $grid->actions(function ($actions) {
                $actions->append('<a href="/admin/subject_group/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->created_at();
            $grid->updated_at();
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
            $form->text('name', 'Nhóm môn học');
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
            $grid->model()->whereIn('id', $idSubject);
            $grid->id('ID')->sortable();
            $grid->subject_code('Mã môn học');
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
                return join('&nbsp;', $name);});
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
                $actions->append('<a href="/admin/subject/' . $actions->getKey() . '/edit"><i class="fa fa-edit" ></i></a>');
                $actions->append('<a href="/admin/subject/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            //disable
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableFilter();
        });
    }
}
