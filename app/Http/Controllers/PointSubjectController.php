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

class PointSubjectController extends Controller
{
    use ModelForm;

    public function index()
    {
        return User::content(function (ContentUser $content) {

            $content->header('Điểm');
            $content->description('Danh sách môn học');
            $content->breadcrumb(
                ['text' => 'Điểm', 'url' => '../user/point-subject']
            );
            $content->body($this->grid());
        });
    }
   protected function grid()
    {
        return User::grid(ResultRegister::class, function (Grid $grid) {
            $user = Auth::user();
            $timeRegister = TimeRegister::orderBy('id', 'DESC')->first();
            $grid->model()->where('id_user_student', $user->id);

            $grid->column('Mã MH')->display(function(){
                $subjetRegister = Subjects::find($this->id_subject);
                if($subjetRegister->subject_code) {
                    return $subjetRegister->subject_code;
                } else {
                    return '';
                }
            });
            $grid->id_subject('Tên môn học')->display(function ($id) {
            	$subject = Subjects::find($id);
            	if($subject->name) {
            		return $subject->name;
            	} else {
            		return '';
            	}
            });

            $grid->column('Số tín chỉ')->display(function () {
            	$subject = Subjects::find($this->id_subject);
            	if($subject->credits) {
            		return $subject->credits;
            	} else {
            		return '';
            	}
            });

            $grid->column('Năm')->display(function () {
                $subject = TimeRegister::find($this->time_register);
                $id = $subject->id;
               if($id % 2 == 0)
                    {
                        return "<span class='label label-info'>{$subject->name}</span>";    
                    } else {
                        return "<span class='label label-success'>{$subject->name}</span>";    
                    }
            });
            $grid->column('%QT')->display(function () {
                return $this->rate_attendance;
            });
            $grid->column('%GK')->display(function () {
                return $this->rate_mid_term;
            });
            $grid->column('%CK')->display(function () {
                return $this->rate_mid_term;
            });
            $grid->column('Điểm QT')->display(function () {
                if(!empty($this->attendance))
                {
                    return $this->attendance;
                }
                else{ return "0"; }
            });
            $grid->column('Điểm GK')->display(function () {
                if(!empty($this->mid_term))
                {
                    return $this->mid_term;
                }
                else{ return "0"; }
            });
            $grid->column('Điểm QT')->display(function () {
                if(!empty($this->mid_term))
                {
                    return $this->mid_term;
                }
                else{ return "0"; }
            });
            $grid->column('Điểm TK')->display(function () {
                 $final = (($this->attendance * $this->rate_attendance) +
                                ($this->mid_term * $this->rate_mid_term) +
                                ($this->end_term * $this->rate_end_term)) / 100;
                 return "<b>{$final}</b>";
            });
            $grid->column('Kết quả')->display(function () {
                 $final = (($this->attendance * $this->rate_attendance) +
                                ($this->mid_term * $this->rate_mid_term) +
                                ($this->end_term * $this->rate_end_term)) / 100;
                 if($final < 5){
                    return "<b>X</b>";
                 }
                 else
                 {
                    return "<b>Đạt</b>";
                 }
                 
            });

            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableActions();
 			
    	});
    }
}
