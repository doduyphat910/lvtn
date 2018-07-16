<?php

namespace App\Admin\Controllers;

use App\Models\SubjectBeforeAfter;

use App\Models\Subjects;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\MessageBag;

class SubjectBeforeAfterController extends Controller
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

            $content->header('Môn tiên quyết');
            $content->description('Danh sách môn tiên quyết');

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

            $content->header('Môn học tiên quyết');
//            $content->description('description');

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

            $content->header('Môn tiên quyết');
            $content->description('Thêm môn tiên quyết');

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
        return Admin::grid(SubjectBeforeAfter::class, function (Grid $grid) {
            $grid->rows(function (Grid\Row $row) {
                $row->column('number', $row->number);
            });
            $grid->number('STT');
           // $grid->id('ID')->sortable();
            $grid->id_subject_before('Môn học trước')->display(function ($idSubjectBefore){
                if($idSubjectBefore)
                {
                    $name = Subjects::find($idSubjectBefore)->name;
                    return '<a href="/admin/subject/' . $idSubjectBefore . '/details">'.$name.'</a>';
                } else {
                    return '';
                }
            })->sortable();
            $grid->id_subject_after('Môn học sau')->display(function ($idSubjectAfter){
                if($idSubjectAfter)
                {
                    $name = Subjects::find($idSubjectAfter)->name;
                    return '<a href="/admin/subject/' . $idSubjectAfter . '/details">'.$name.'</a>';
                } else {
                    return '';
                }
            })->sortable();
            // $grid->actions(function($actions){
            //     $actions->disableEdit();
            // });
            $grid->created_at('Tạo vào lúc')->sortable();
            $grid->updated_at('Cập nhật vào lúc')->sortable();
            $grid->filter(function($filter){
                $filter->disableIdFilter();
                $filter->in('id_subject_before', 'Môn học trước')->multipleSelect(Subjects::all()->pluck('name', 'id'));
                $filter->in('id_subject_after', 'Môn học sau')->multipleSelect(Subjects::all()->pluck('name', 'id'));
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
        return Admin::form(SubjectBeforeAfter::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->select('id_subject_before', 'Môn tiên quyết')->options(Subjects::all()->pluck('name', 'id'))->rules('required');
            $form->select('id_subject_after', 'Môn học sau')->options(Subjects::all()->pluck('name', 'id'))->rules('required');
            $form->saving(function (Form $form) {
                if($form->id_subject_before == $form->id_subject_after) {
                    $error = new MessageBag([
                        'title'   => 'Lỗi',
                        'message' => 'Môn tiên quyết và môn sau không được giống nhau',
                    ]);
                    return back()->with(compact('error'));
                }
            });
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
