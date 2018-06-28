<?php
namespace App\Http\Controllers;

use App\Http\Extensions\Facades\User;
use app\Http\Extensions\LayoutUser\ContentUser;
use App\Models\UserSubject;
use App\Models\StudentUser;
use App\Models\Subjects;
use Illuminate\Http\Request;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Auth;
use App\Http\Extensions\Comments\FormComments;
use App\Http\Extensions\Comments\UserCommentsFacades;

class UserSubjectController extends Controller
{
    use ModelForm;
    public function index()
    {
        return User::content(function (ContentUser $content) {

            $content->header('Đăng ký ngoài kế hoạch');
            $content->description('Danh sách môn học');

            $content->body($this->form());
        });
    }
    
    protected function form()
    {
        return UserCommentsFacades::form(UserSubject::class, function (FormComments $form) {
            $form->registerBuiltinFields();
            $id = Auth::User()->id;
            $form->setAction('/user/user-subject');
            $form->hidden('id_user')->value($id);
            $form->hidden('id');
            $form->select('id_subject', 'Môn học')->options(Subjects::all()->pluck('name', 'id'))->rules('required');
            
       });
    }
}