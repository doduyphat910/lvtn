<?php
namespace App\Http\Controllers;

use App\Http\Extensions\Facades\User;
use app\Http\Extensions\LayoutUser\ContentUser;
use App\Models\TimeRegister;
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
            $content->breadcrumb(
                ['text' => 'Đăng ký ngoài kế hoạch', 'url' => '../user/learn-improvement']
            );
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
//            $form->hidden('id');
            $form->select('id_subject', 'Môn học')->options(Subjects::all()->pluck('name', 'id'))->rules('required');
            $form->disableReset();
            $timeRegister = TimeRegister::where('status', 1)->orderBy('id', 'DESC')->first();
            $idTimeRegister = $timeRegister->id;
            $form->hidden('id_time_register');
            $form->tools(function (Form\Tools $tools) {
            $tools->disableListButton();
            $tools->disableBackButton();
            });
            $form->saving(function (Form $form) use ($idTimeRegister){
                $form->id_time_register = $idTimeRegister;
            });
       });
    }
}