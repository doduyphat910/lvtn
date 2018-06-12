<?php

namespace App\Http\Controllers;

use App\Http\Extensions\Facades\User;
use app\Http\Extensions\LayoutUser\ContentUser;
use App\Models\SubjectParallel;
use Illuminate\Http\Request;
use App\Models\Subjects;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Auth;

class SubjectsParallelController extends Controller
{
    use ModelForm;
    public function index()
    {
        return User::content(function (ContentUser $content) {

            $content->header('Môn học song song');
            $content->description('Danh sách môn học song song');

            $content->body($this->grid());
        });
    }
    protected function grid()
    {
        return User::grid(SubjectParallel::class, function (Grid $grid) {

            //$grid->id('ID')->sortable();
            $grid->id_subject1('Môn học trước')->display(function ($idSubject1){
                $name = Subjects::find($idSubject1)->name;
                return $name;
            });
            $grid->id_subject2('Môn học song song')->display(function ($idSubject2){
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
