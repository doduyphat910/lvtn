<?php

namespace App\Http\Controllers;

use App\Http\Extensions\Facades\User;
use app\Http\Extensions\LayoutUser\ContentUser;
use Illuminate\Http\Request;
use App\Models\StudentUser;
use App\Models\Comments;
use App\Models\Subjects;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Auth;
use App\Http\Extensions\Comments\FormComments;
use App\Http\Extensions\Comments\UserCommentsFacades;

class CommentsController extends Controller
{
    use ModelForm;
    public function index()
    {
        return User::content(function (ContentUser $content) {

            $content->header('Góp ý kiến');
            $content->description('Ý kiến');
            $content->breadcrumb(
                ['text' => 'Góp ý kiến', 'url' => '../user/comments']
            );
            $content->body($this->form());
        });
    }
    protected function form()
    {
        return UserCommentsFacades::form(Comments::class, function (FormComments $form) {
            $form->registerBuiltinFields();
            $id = Auth::User()->id;
            $form->setAction('/user/comments');
            $form->hidden('status')->value(0);
            $form->hidden('id_user')->value($id);
            $form->hidden('status')->value(0);
            $form->hidden('id');
            $form->textarea('name', 'Tiêu đề')->rows(2);
            $form->textarea('description', 'Nội dung')->rows(10);
            $form->tools(function (Form\Tools $tools) {
                $tools->disableBackButton();
                $tools->disableListButton();
            });
            $form->disableReset();
       });
    }
    
}
