<?php

namespace App\Http\Controllers;

use App\Models\UserSubject;
use App\Models\Subjects;
use App\Models\SubjectsRegister;
use App\Models\Semester;
use App\Models\SemesterSubjects;
use App\Models\Year;


use App\Http\Extensions\Facades\User;
use app\Http\Extensions\LayoutUser\ContentUser;
use Illuminate\Http\Request;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Auth;

class SubjectRegisterController extends Controller
{
    use ModelForm;
    public function index()
    {
        return User::content(function (ContentUser $content) {

            $content->header('Đăng ký môn học');
            $content->description('Danh sách môn học');

            $content->body($this->grid());
        });
    }
    protected function grid()
    {
        return User::grid(SemesterSubjects::class, function (Grid $grid) {	
             $semester = Semester::where('status', 1)->pluck('id');
         	$subjects_id = SemesterSubjects::whereIn('semester_id',$semester)->pluck('subjects_id');
			// $grid->model()->whereIn('id', $subjects_id);
			// $grid->column('Mã môn học')->display(function() use($subjects_id){
			// 	return Subjects::whereIn('id', $subjects_id)->pluck('subject_code');
			// });
            // $grid->name('Tên môn học')->display(function ($name){
            //     return  '<a href="/user/subjectregister/' . $this->id . '/details">'.$name.'</a>';
            // });
            // // $grid->credits('Số tín chỉ');
            // // $grid->credits_fee('Số tín chỉ học phí');
            $grid->column('Học kỳ - Năm')->display(function () {
                $id = $this->id;
                $subject = Subjects::find($id);
                $arraySemester = $subject->semester()->pluck('id')->toArray();
                $name = array_map( function ($arraySemester){
                    $nameSemester = Semester::find($arraySemester)->name;
                    if($nameSemester == 0 ) {
                    	 $nameSemester = 'Học kì hè';
                    } 
                    $year = Semester::find($arraySemester)->year()->get();
                    $nameYear = $year['0']->name;
                    return "<span class='label label-info'>{$nameSemester} - {$nameYear}</span>"  ;
                }, $arraySemester);
                return join('&nbsp;', $name);});

           	$grid->disableExport();
            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableActions();
            $grid->disableFilter();
        });
    }

}
