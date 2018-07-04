<?php

namespace App\Http\Controllers;

use App\Http\Extensions\Facades\User;
use app\Http\Extensions\LayoutUser\ContentUser;
use App\Models\SubjectBeforeAfter;
use Illuminate\Http\Request;
use App\Models\Subjects;
use Encore\Admin\Form;
use App\Http\Extensions\GridUser;

use Encore\Admin\Facades\Admin;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Auth;

class SubjectBeforeAfterController extends Controller
{
    use ModelForm;
    public function index()
    {
        return User::content(function (ContentUser $content) {

            $content->header('Môn học tiên quyết');
            $content->description('Danh sách môn học tiên quyết');
            $content->breadcrumb(
                ['text' => 'Môn học tiên quyết', 'url' => '../user/subject-before-after']
            );
            $content->body($this->grid());
            

        });
    }
    protected function grid()
    {
        return User::GridUser(SubjectBeforeAfter::class, function (GridUser $grid) {

            // $grid->id('ID')->sortable();
            $grid->id_subject_before('Môn học trước')->display(function ($idSubject1){
                $name = Subjects::find($idSubject1)->name;
                return $name;
            });
            $grid->id_subject_after('Môn học sau')->display(function ($idSubject2){
                $name = Subjects::find($idSubject2)->name;
                return $name;
            });
            $grid->disableActions();
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableFilter();
        });
    }
    
}
