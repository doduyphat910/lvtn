<?php

namespace App\Admin\Controllers;

use App\Models\SubjectParallel;

use App\Models\Subjects;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\MessageBag;

class SubjectParallelController extends Controller
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

            $content->header('MH song song');
            $content->description('DS môn song song');

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

            $content->header('MH song song');
            $content->description('Thêm môn song song');

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
        return Admin::grid(SubjectParallel::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->id_subject1('Môn học trước')->display(function ($idSubject1){
                $name = Subjects::find($idSubject1)->name;
                return $name;
            });
            $grid->id_subject2('Môn học song song')->display(function ($idSubject2){
                $name = Subjects::find($idSubject2)->name;
                return $name;
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
        return Admin::form(SubjectParallel::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->select('id_subject1', 'Môn học trước')->options(Subjects::all()->pluck('name', 'id'));
            $form->select('id_subject2', 'Môn học song song')->options(Subjects::all()->pluck('name', 'id'));
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
            $form->saving(function (Form $form){
                if($form->id_subject1 == $form->id_subject2 ) {
                    $error = new MessageBag([
                        'title'   => 'Lỗi',
                        'message' => 'Môn trước và môn sau không được giống nhau',
                    ]);
                    return back()->with(compact('error'));
                }
            });
        });
    }
}
