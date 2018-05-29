<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Extensions\Facades\User;
use app\Http\Extensions\LayoutUser\ContentUser;
use App\Models\ClassSTU;
use App\Models\StudentUser;
use Illuminate\Http\Request;
use App\Models\Department;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Auth;

class StudentInformationController extends Controller
{
    use ModelForm;
    public function index()
    {
        return User::content(function (ContentUser $content) {

            $content->header('header');
            $content->description('description');

            // $content->body($this->grid());
        });
    }
    protected function grid()
    {
        return User::grid(StudentUser::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->created_at();
            $grid->updated_at();
            
        });
    }
    protected function form()
    {
        return Admin::form(StudentUser::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
