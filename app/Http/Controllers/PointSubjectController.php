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
use App\Models\ClassSTU;
use App\Models\Department;

use App\Http\Extensions\GridUser;

use App\Http\Extensions\Facades\User;
use app\Http\Extensions\LayoutUser\ContentUser;
use Encore\Admin\Widgets\Alert;
use Encore\Admin\Widgets\Callout;
use Illuminate\Http\Request;
use Encore\Admin\Form;

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
            $content->description('Danh sách điểm');
            $content->breadcrumb(
                ['text' => 'Điểm', 'url' => '../user/point-subject']
            );
            $content->body(
                view('vendor.details',
                [
                    'template_body_name' => 'user.PointSubject.info',
                    'form' => $this->form(),
                    'grid' => $this->grid()
                ]));
        });
    }
   protected function grid()
    {
        return User::GridUser(ResultRegister::class, function (GridUser $grid) {
            $user = Auth::user();
            $grid->model()->where('id_user_student', $user->id)->where('is_learned', 1)->orderBy('time_register', 'DESC');
            $grid->column('Mã MH')->style("text-align: center;")->display(function(){
                $subjetRegister = Subjects::find($this->id_subject);
                if($subjetRegister->id) {
                    return $subjetRegister->id;
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

            $grid->column('Số tín chỉ')->style("text-align: center;")->display(function () {
            	$subject = Subjects::find($this->id_subject);
            	if($subject->credits) {
            		return $subject->credits;
            	} else {
            		return '';
            	}
            });

            $grid->column('Năm')->style("text-align: center;")->display(function () {
                $subject = TimeRegister::find($this->time_register);
                $id = $subject->id;
               if($id % 2 == 0)
                    {
                        return "<span class='label label-info'>{$subject->name}</span>";    
                    } else {
                        return "<span class='label label-success'>{$subject->name}</span>";    
                    }
            });
            $grid->column('%QT')->style("text-align: center;")->display(function () {
                return $this->rate_attendance;
            });
            $grid->column('%GK')->style("text-align: center;")->display(function () {
                return $this->rate_mid_term;
            });
            $grid->column('%CK')->style("text-align: center;")->display(function () {
                return $this->rate_end_term;
            });
            $grid->column('Điểm QT')->style("text-align: center;")->display(function () {
                if(!empty($this->attendance))
                {
                    return $this->attendance;
                }
                else{ return "0"; }
            });
            $grid->column('Điểm GK')->style("text-align: center;")->display(function () {
                if(!empty($this->mid_term))
                {
                    return $this->mid_term;
                }
                else{ return "0"; }
            });
            $grid->column('Điểm CK')->style("text-align: center;")->display(function () {
                if(!empty($this->end_term))
                {
                    return $this->end_term;
                }
                else{ return "0"; }
            });
            $grid->column('Điểm TK')->style("text-align: center;")->display(function () {
                 $final = (($this->attendance * $this->rate_attendance) +
                                ($this->mid_term * $this->rate_mid_term) +
                                ($this->end_term * $this->rate_end_term)) / 100;
                 return "<b>{$final}</b>";
            });
            $grid->column('Kết quả')->style("text-align: center;")->display(function () {
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
            $grid->column('Số tín chỉ hiện tại')->display(function () {
                $idUser = Auth::user()->id;
                $idSubject = ResultRegister::where('id_user_student', $idUser)->pluck('id_subject');
                $subjects = Subjects::find($idSubject);
                $sumCredit = 0;
                foreach ($subjects as $subject){
                    $sumCredit+=$subject->credits;
                }
                return $sumCredit;

            });
            $grid->filter(function($filter){
                $filter->disableIdFilter();
                $filter->like('id', 'Mã môn học');
                $filter->where(function ($query){
                    $user = Auth::user();
                    $input = $this->input;
                    $arrSubject = Subjects::where('name', 'like', '%'.$input.'%')->pluck('id')->toArray();
                    $idResult = ResultRegister::where('id_user_student',$user->id )->whereIn('id_subject',$arrSubject)->pluck('id')->toArray();
                    $query->whereIn('id',$idResult);
                }, 'Tên môn học');
                $filter->where(function ($query){
                    $user = Auth::user();
                    $input = $this->input;
                    $arrSubject = Subjects::where('credits', 'like', '%'.$input.'%')->pluck('id')->toArray();
                    $idResult = ResultRegister::where('id_user_student',$user->id )->whereIn('id_subject',$arrSubject)->pluck('id')->toArray();
                    $query->whereIn('id',$idResult);
                }, 'Số tín chỉ');
                $filter->like('rate_attendance', '% Qúa trình');
                $filter->like('rate_mid_term', '% Giữa kỳ');
                $filter->like('rate_end_term', '% Cuối kỳ');
                $filter->like('attendance', 'Điểm quá trình');
                $filter->like('mid_term', 'Điểm giữa kỳ');
                $filter->like('end_term', 'Điểm cuối kỳ');
                $filter->where(function ($query)  {
                    $user = Auth::user();
                    $input = $this->input;
                    $idFinal = ResultRegister::where('id_user_student',$user->id )->whereRaw("((attendance *rate_attendance)+(mid_term*rate_mid_term)+(end_term*rate_end_term))/100 = ".$input)
                        ->pluck('id')->toArray();
                    $query->whereIn('id', $idFinal);
                }, 'Điểm TK');
            });
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableActions();

    	});
    }
    protected function form()
    {
        return User::form(TimeRegister::class, function (Form $form) {
            $form->registerBuiltinFields();
            $id = Auth::User()->id;
            $arrIdTimeRegiter=ResultRegister::where('id_user_student',$id)->distinct()->pluck('time_register')->toArray();
            $options = ['Tất cả'];
            $options += TimeRegister::whereIn('id',$arrIdTimeRegiter)->orderBy('id', 'DESC')->pluck('name', 'id')->toArray();
            $form->select('id_time_register', 'Thời gian')->options($options)->attribute(['id' => 'resultPoint']);
            $form->disableReset();
            $form->disableSubmit();
            $form->tools(function (Form\Tools $tools) {
           
            $tools->disableListButton();
            });

        });
    }
}
