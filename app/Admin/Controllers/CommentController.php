<?php

namespace App\Admin\Controllers;

use App\Models\Comments;
use App\Models\StudentUser;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class CommentController extends Controller
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

            $content->header('Góp ý kiến');
            $content->description('Danh sách ý kiến');

            $content->body($this->grid());
        });
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Comments::class, function (Grid $grid) {
            $grid->model()->orderBy('created_at', 'DESC');
            $grid->rows(function (Grid\Row $row) {
                $row->column('number', $row->number);
            });
            $grid->number('STT');
//            $grid->id('ID')->sortable();
            $grid->name('Tiêu đề')->display(function ($name) {
                return '<a href="/admin/comment/' . $this->id . '/details">'.$name.'</a>';
            });
            $grid->id_user('MSSV')->display(function ($idUser){
                $user = StudentUser::find($idUser);
                if($user->code_number){
                    return $user->code_number;
                } else {
                    return '';
                }
            });
            $grid->column('Họ')->display(function (){
                $user = StudentUser::find($this->id_user);
                if($user->first_name){
                    return $user->first_name;
                } else {
                    return '';
                }
            });
            $grid->column('Tên')->display(function (){
                $user = StudentUser::find($this->id_user);
                if($user->last_name){
                    return $user->last_name;
                } else {
                    return '';
                }
            });
            $grid->status('Trạng thái')->display(function ($status) {
                if($status == 0) {
                    return "<span class='label label-danger'>Chưa xem</span>";
                } else {
                    return "<span class='label label-success'>Đã xem</span>";
                }
            });
            $grid->created_at('Tạo vào lúc');
            $grid->updated_at('Cập nhật vào lúc');
            $grid->actions(function ($actions) {
                $actions->disableEdit();
                $actions->append('<a href="/admin/comment/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->disableExport();
            $grid->disableCreateButton();
            $grid->filter(function ($filter){
                $filter->disableIdFilter();
                $filter->like('name', 'Tiêu đề');
                $filter->where(function ($query){
                    $input = $this->input;
                    $arrUser = StudentUser::where('code_number', 'like', '%'.$input.'%')->pluck('id');
                    $query->whereIn('id_user',$arrUser);
                }, 'MSSV');
                $filter->where(function ($query){
                    $input = $this->input;
                    $arrUser = StudentUser::where('first_name', 'like', '%'.$input.'%')->pluck('id');
                    $query->whereIn('id_user',$arrUser);
                }, 'Họ');
                $filter->where(function ($query){
                    $input = $this->input;
                    $arrUser = StudentUser::where('last_name', 'like', '%'.$input.'%')->pluck('id');
                    $query->whereIn('id_user',$arrUser);
                }, 'Tên');
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
        return Admin::form(Comments::class, function (Form $form) {

//            $form->display('id', 'ID');
            $form->text('name', 'Tiêu đề')->readOnly();
            $form->textarea('description', 'Mô tả')->readOnly();
            $form->display('created_at', 'Tạo vào lúc');
        });
    }

    public function details($id){
        return Admin::content(function (Content $content) use ($id) {
            $comment = Comments::findOrFail($id);
            $comment->status = 1;
            $comment->save();
            $content->header('Góp ý kiến');
            $content->description($comment->name);
            $content->body($this->detailsView($id));
        });
    }

    public function detailsView($id){
        $form = $this->form()->view($id);
        return view('vendor.details',
            [
                'template_body_name' => 'admin.Comment.info',
                'form' => $form,
            ]
        );
    }
}
