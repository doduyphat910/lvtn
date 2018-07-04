<?php

namespace App\Http\Controllers;

use App\Models\TimeStudy;
use Encore\Admin\Grid\Displayers;
use App\Models\ResultRegister;
use App\Models\StudentUser;
use App\Models\TimeRegister;
use App\Models\UserSubject;
use App\Models\Subjects;
use App\Models\SubjectRegister;
use App\Models\Semester;
use App\Models\SemesterSubjects;
use App\Models\Year;
use App\Models\SubjectGroup;
use App\Models\Rate;
use App\Models\Classroom;
use App\Models\UserAdmin;

use App\Http\Extensions\Facades\User;
use app\Http\Extensions\LayoutUser\ContentUser;
use Encore\Admin\Widgets\Alert;
use Encore\Admin\Widgets\Callout;
use Illuminate\Http\Request;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;

class LearnImprovenmentController extends Controller
{
    use ModelForm;

    public function index()
    {
        return User::content(function (ContentUser $content) {

            $content->header('Đăng ký môn học cải thiện, học lại');
            $content->description('Danh sách môn học');
            $content->breadcrumb(
                ['text' => 'Đăng ký môn học cải thiện, học lại', 'url' => '../user/learn-improvement']
            );
            $content->body($this->grid());
        });
    }
    protected function grid()
    {
        return User::grid(Subjects::class, function (Grid $grid) {
            $grid->registerColumnDisplayer();
            $user = Auth::user();

            $schoolYearUser = $user->school_year;
            //check school year
            $timeRegister = TimeRegister::where('status', 1)->orderBy('id', 'DESC')->first();
//             $schoolYearUser = (string) $schoolYearUser;
            if ($timeRegister) {
//                 //get school year in time register and school year = "ALL"
//                 if(in_array($schoolYearUser, $timeRegister->school_year) || $timeRegister->school_year['0'] == "All") {
//                 } else {
//                     $grid->model()->where('id', -1);
//                 }

                //open subject register follow semester
                $nameSemester = $timeRegister->semester;
                $idSemester = Semester::where('name', $nameSemester)->pluck('id');
                $subjects_id = SemesterSubjects::whereIn('semester_id', $idSemester)->orderBy('semester_id', 'DESC')->pluck('subjects_id')->toArray();
                //sort follow semester
                $field = '';
                foreach ($subjects_id as $id) {
                    $field .= ('"'.$id.'"' . ',');
                }
                $field = substr($field, 0, strlen($field) - 1);
                //get subject user learned
                $idSubjectRegister = ResultRegister::where('id_user_student', $user->id)->where('is_learned', 1)->pluck('id_subject_register')->toArray();
                $idSubjectLearned = SubjectRegister::whereIn('code_subject_register', $idSubjectRegister)->pluck('id_subjects')->toArray();
                //show subject not learned and subjects in semester in time register (hiển thị các môn đã học & trong đợt đăng kí đang mở)
                $grid->model()->whereIn('subject_code', $subjects_id)->whereIn('subject_code', $idSubjectLearned)->orderBy(DB::raw('FIELD(subject_code, ' . $field . ')'));
            }
            //$grid->id('id');
            $grid->subject_code('Mã môn học');
            $grid->name('Tên môn học')->display(function ($name) {
                return '<a href="/user/subject-register/' . $this->id . '/details"  target="_blank" >' . $name . '</a>';
            });

            $grid->credits('Số tín chỉ');
            $grid->credits_fee('Số tín chỉ học phí');
            $grid->column('Nhóm môn')->display(function () {
                $subject = Subjects::find($this->subject_code);
                $nameGroup = $subject->subject_group()->pluck('name')->toArray();
                $groupSubject = array_map(function ($nameGroup){
                    if($nameGroup) {
                        return "<span class='label label-primary'>{$nameGroup}</span>"  ;
                    } else {
                        return '';
                    }
                },$nameGroup);
                return join('&nbsp;', $groupSubject);

            });
            $grid->column('Học kỳ - Năm')->display(function () {
                $id = $this->subject_code;
                $subject = Subjects::find($id);
                $arraySemester = $subject->semester()->pluck('id')->toArray();
                $name = array_map(function ($arraySemester) {
                    $nameSemester = Semester::find($arraySemester)->name;
                    switch ($nameSemester) {
                        case 0 :
                            $nameSemester = 'Học kì hè';
                            break;
                        case 1:
                            $nameSemester = 'Học kì 1';
                            break;
                        case 2:
                            $nameSemester = 'Học kì 2';
                    }
                    $year = Semester::find($arraySemester)->year()->get();
                    $nameYear = $year['0']->name;
                    return "<span class='label label-info'>{$nameSemester} - {$nameYear}</span>";
                }, $arraySemester);
                return join('&nbsp;', $name);
            });
            $grid->column('Đăng ký')->display(function () {
                return '<a href="/user/subject-register/' . $this->subject_code . '/details" data-id='.$this->subject_code.'  target="_blank" class="btn btn-md btnACV" ><i class="glyphicon glyphicon-pencil"></i></a>';
            });
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();

            $grid->disableActions();
        });
    }
}
