<?php
namespace App\Http\Controllers;
use App\Http\Extensions\Facades\User;
use App\Http\Extensions\GridUser;
use app\Http\Extensions\LayoutUser\ContentUser;
use Encore\Admin\Widgets\Alert;
use Encore\Admin\Widgets\Callout;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Models\Classroom;

use App\Models\Rate;
use App\Models\ResultRegister;
use App\Models\SubjectRegister;
use App\Models\Subjects;
use App\Models\TimeRegister;
use App\Models\SubjectBeforeAfter;
use App\Models\SubjectParallel;
use App\Models\TimeStudy;
use App\Models\UserAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use PhpParser\Node\Expr\Array_;

class APIController extends Controller {

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resultRegister(Request $request){
        $idSubjecRegister = $request->id;
        $user = Auth::user();
        $idUser = $user->id;
        $timeRegister = TimeRegister::where('status', 1)->orderBy('id', 'DESC')->first();
        $idTimeRegister = $timeRegister->id;

        //get qty current
        $subjecRegister = SubjectRegister::find($idSubjecRegister);
        $qtyCurrent = $subjecRegister->qty_current;
        $qtyMax = $subjecRegister->qty_max;


        //nếu đã đăng kí rồi thì không được đăng kí nữa
        $idSubjects = SubjectRegister::where('id',$idSubjecRegister)->pluck('id_subjects')->toArray();
        $countSubject = ResultRegister::where('id_subject', $idSubjects['0'])->where('id_user_student', $idUser)->where('time_register', $idTimeRegister)->get()->count();
            if($countSubject >= 1)
            {
            return response()->json([
                'status'  => false,
                'message' => trans('Bạn đã đăng kí môn học này'),
            ]);
        }

        //lấy số lượng tín chỉ được đăng kí tối đa
        $creditsMax = $timeRegister->credits_max;
        $idSubject = ResultRegister::where('id_user_student', $idUser)->where('time_register', $idTimeRegister)->where('is_learned', 2)->pluck('id_subject');
        $creditCurrentUser = Subjects::find($idSubject)->pluck('credits')->sum();
        $idSubjects = SubjectRegister::where('id',$idSubjecRegister)->pluck('id_subjects');
        $creditSubject = Subjects::find($idSubjects)->pluck('credits')->toArray();
        if(($creditCurrentUser + $creditSubject['0']) > $creditsMax) {
            return response()->json([
                'status'  => false,
                'message' => trans('Bạn đã đăng kí tối đa số tín chỉ'),
            ]);
        }

        //kiểm tra giờ học trùng
        $arrIdSubjectRegiter = ResultRegister::where('id_user_student',$idUser)->where('time_register', $idTimeRegister)->pluck('id_subject_register')->toArray();
        $arrResultRegiter = TimeStudy::whereIn('id_subject_register', $arrIdSubjectRegiter)->get()->toArray();
        $arrTimeStudyUser = TimeStudy::where('id_subject_register',$idSubjecRegister)->get()->toArray();

                foreach ($arrResultRegiter as $dayAll){
                    foreach ($arrTimeStudyUser as $dayUser){
                        if ($dayAll['day'] == $dayUser['day'] ) {
                            if (
                                ($dayAll['time_study_end'] > $dayUser['time_study_start'] && $dayAll['time_study_end'] <= $dayUser['time_study_end']) ||
                                ($dayAll['time_study_start'] >= $dayUser['time_study_start'] && $dayAll['time_study_start'] < $dayUser['time_study_end']) ||
                                ($dayAll['time_study_start'] >= $dayUser['time_study_start'] && $dayAll['time_study_end'] <= $dayUser['time_study_end'])  ||
                                ($dayAll['time_study_start'] <= $dayUser['time_study_start'] && $dayAll['time_study_end'] >= $dayUser['time_study_end'])
                            )
                            {
                                return response()->json([
                                    'status'  => false,
                                    'message' => trans('Bạn đã đăng kí giờ học này'),
                                ]);
                            }
                        }
                    }
                }

//        dd(ResultRegister::all()->toArray());


//        $arrayTime = Array();
//        $timeResultRegister=ResultRegister::all();
//        foreach ($timeResultRegister as $value){
//            $arrayTime[]=$value->id_subject_register;
//        }
//        $t = TimeStudy::all();
//        $arrayTime_Day = Array();
//        $i=0;
//        foreach ($t as $value){
//            foreach ($arrayTime as $val){
//                if($value->id_subject_register ==  $val){
//                    $arrayTime_Day[$i]['time_study_start']= $value->time_study_start;
//                    $arrayTime_Day[$i]['time_study_end']= $value->time_study_end;
//                    $arrayTime_Day[$i]['day']= $value->day;
//                    $i=$i+1;
//                }
//            }
//        }
//        //dd($arrayTime_Day);




        //nếu số lượng hiện tại lớn hơn số lượng max thì không được đăng kí
        if($qtyCurrent >= $qtyMax) {
            return response()->json([
                'status'  => false,
                'message' => trans('Học phần đã hết chỗ'),
            ]);
        } else {
            $resultRegister = new ResultRegister;
            $resultRegister->id_user_student = $idUser;
            $resultRegister->id_subject_register = $idSubjecRegister;
            $idSubjects = SubjectRegister::find($idSubjecRegister)->id_subjects;
            $resultRegister->id_subject = $idSubjects;
            $resultRegister->is_learned = 2;// lưu bằng 2 để không show ra bảng điểm
            $resultRegister->attendance = null;
            $resultRegister->mid_term = null;
            $resultRegister->end_term = null;
//            $resultRegister->final = null;
            //get rate now
            $subjectRegister = SubjectRegister::find($idSubjecRegister);
            $subjectId = $subjectRegister->id_subjects;
            $idRate = Subjects::find($subjectId)->id_rate;
            $rate = Rate::find($idRate);
            $resultRegister->rate_attendance = $rate->attendance;
            $resultRegister->rate_mid_term = $rate->mid_term;
            $resultRegister->rate_end_term = $rate->end_term;
            $resultRegister->time_register = $idTimeRegister;
            if($resultRegister->save()) {
                $subjecRegister->qty_current = $qtyCurrent + 1;
                if($subjecRegister->save()) {
                    return response()->json([
                        'status'  => true,
                        'message' => trans('Đăng ký thành công'),
                    ]);
                }else {
                    return response()->json([
                        'status'  => false,
                        'message' => trans('Đăng ký không thành công'),
                    ]);
                }
            }
        }
    }
    public function Register(Request $request){
        $idSubjecRegister = $request->id;
        $user = Auth::user();
        $idUser = $user->id;
        $timeRegister = TimeRegister::where('status', 1)->orderBy('id', 'DESC')->first();
        $idTimeRegister = $timeRegister->id;

        //get qty current
        $subjecRegister = SubjectRegister::find($idSubjecRegister);
        $qtyCurrent = $subjecRegister->qty_current;
        $qtyMax = $subjecRegister->qty_max;


        //nếu đã đăng kí rồi thì không được đăng kí nữa
        $idSubjects = SubjectRegister::where('id',$idSubjecRegister)->pluck('id_subjects')->toArray();
        $countSubject = ResultRegister::where('id_subject', $idSubjects['0'])->where('id_user_student', $idUser)->where('time_register', $idTimeRegister)->get()->count();
            if($countSubject >= 1)
            {
            return response()->json([
                'status'  => false,
                'message' => trans('Bạn đã đăng kí môn học này'),
            ]);
        }

        //lấy số lượng tín chỉ được đăng kí tối đa
        $creditsMax = $timeRegister->credits_max;
        $idSubject = ResultRegister::where('id_user_student', $idUser)->where('time_register', $idTimeRegister)->where('is_learned', 0)->pluck('id_subject');
        $creditCurrentUser = Subjects::find($idSubject)->pluck('credits')->sum();
        $idSubjects = SubjectRegister::where('id',$idSubjecRegister)->pluck('id_subjects');
        $creditSubject = Subjects::find($idSubjects)->pluck('credits')->toArray();
        if(($creditCurrentUser + $creditSubject['0']) > $creditsMax) {
            return response()->json([
                'status'  => false,
                'message' => trans('Bạn đã đăng kí tối đa số tín chỉ'),
            ]);
        }

        //kiểm tra giờ học trùng
        $arrIdSubjectRegiter = ResultRegister::where('id_user_student',$idUser)->where('time_register', $idTimeRegister)->pluck('id_subject_register')->toArray();
        $arrResultRegiter = TimeStudy::whereIn('id_subject_register', $arrIdSubjectRegiter)->get()->toArray();
        $arrTimeStudyUser = TimeStudy::where('id_subject_register',$idSubjecRegister)->get()->toArray();

                foreach ($arrResultRegiter as $dayAll){
                    foreach ($arrTimeStudyUser as $dayUser){
                        if ($dayAll['day'] == $dayUser['day'] ) {
                            if (
                                ($dayAll['time_study_end'] > $dayUser['time_study_start'] && $dayAll['time_study_end'] <= $dayUser['time_study_end']) ||
                                ($dayAll['time_study_start'] >= $dayUser['time_study_start'] && $dayAll['time_study_start'] < $dayUser['time_study_end']) ||
                                ($dayAll['time_study_start'] >= $dayUser['time_study_start'] && $dayAll['time_study_end'] <= $dayUser['time_study_end'])  ||
                                ($dayAll['time_study_start'] <= $dayUser['time_study_start'] && $dayAll['time_study_end'] >= $dayUser['time_study_end'])
                            )
                            {
                                return response()->json([
                                    'status'  => false,
                                    'message' => trans('Bạn đã đăng kí giờ học này'),
                                ]);
                            }
                        }
                    }
                }

//        dd(ResultRegister::all()->toArray());


//        $arrayTime = Array();
//        $timeResultRegister=ResultRegister::all();
//        foreach ($timeResultRegister as $value){
//            $arrayTime[]=$value->id_subject_register;
//        }
//        $t = TimeStudy::all();
//        $arrayTime_Day = Array();
//        $i=0;
//        foreach ($t as $value){
//            foreach ($arrayTime as $val){
//                if($value->id_subject_register ==  $val){
//                    $arrayTime_Day[$i]['time_study_start']= $value->time_study_start;
//                    $arrayTime_Day[$i]['time_study_end']= $value->time_study_end;
//                    $arrayTime_Day[$i]['day']= $value->day;
//                    $i=$i+1;
//                }
//            }
//        }
//        //dd($arrayTime_Day);




        //nếu số lượng hiện tại lớn hơn số lượng max thì không được đăng kí
        if($qtyCurrent >= $qtyMax) {
            return response()->json([
                'status'  => false,
                'message' => trans('Học phần đã hết chỗ'),
            ]);
        } else {
            $resultRegister = new ResultRegister;
            $resultRegister->id_user_student = $idUser;
            $resultRegister->id_subject_register = $idSubjecRegister;
            $idSubjects = SubjectRegister::find($idSubjecRegister)->id_subjects;
            $resultRegister->id_subject = $idSubjects;
            $resultRegister->is_learned = 2;// lưu bằng 2 để không show ra bảng điểm
            $resultRegister->attendance = null;
            $resultRegister->mid_term = null;
            $resultRegister->end_term = null;
//            $resultRegister->final = null;
            //get rate now
            $subjectRegister = SubjectRegister::find($idSubjecRegister);
            $subjectId = $subjectRegister->id_subjects;
            $idRate = Subjects::find($subjectId)->id_rate;
            $rate = Rate::find($idRate);
            $resultRegister->rate_attendance = $rate->attendance;
            $resultRegister->rate_mid_term = $rate->mid_term;
            $resultRegister->rate_end_term = $rate->end_term;
            $resultRegister->time_register = $idTimeRegister;
            if($resultRegister->save()) {
                $subjecRegister->qty_current = $qtyCurrent + 1;
                if($subjecRegister->save()) {
                    return response()->json([
                        'status'  => true,
                        'message' => trans('Đăng ký thành công'),
                    ]);
                }else {
                    return response()->json([
                        'status'  => false,
                        'message' => trans('Đăng ký không thành công'),
                    ]);
                }
            }
        }
    }
    public function deleteRegister(Request $request){
            $idSubjectRegister = $request->id;
             $user = Auth::user();
            $idUser = $user->id;
            $deleteSubject = ResultRegister::where('id_user_student',$idUser)->where('id_subject_register', $idSubjectRegister)->first();
            $subjectRegister = SubjectRegister::find($idSubjectRegister);
        if($deleteSubject->delete()) {
            $qtyCurrent = $subjectRegister->qty_current;
            $subjectRegister->qty_current = $qtyCurrent -1;
                if($subjectRegister->save()) {
                    return response()->json([
                        'status'  => true,
                        'message' => trans('Hủy đăng ký thành công'),
                    ]);
                }else {
                    return response()->json([
                        'status'  => false,
                        'message' => trans('Hủy đăng ký không thành công'),
                    ]);
                }
            }
    }
    public function checkBeforeAtfer(Request $request){
        $idSubject = $request->id;
        $user = Auth::user();
        $idUser = $user->id;
        $idSubjectBefore=SubjectBeforeAfter::where('id_subject_after',$idSubject)->pluck('id_subject_before')->toArray();
        if(count($idSubjectBefore) >0) {
                    $nameSubjectBefore=Subjects::where('id',$idSubjectBefore)->first();
                   $countSubjectBefore=ResultRegister::where('id_user_student', $idUser)->where('id_subject',$idSubjectBefore)->where('is_learned', 1)->get()->count();
            if($countSubjectBefore==0){
             return response()->json([
                        'status'  => false,
                        'message' => trans('Bạn phải học '.$nameSubjectBefore->name.' trước'),
                    ]);
            } 
        }
        
    }
    public function checkParallel(Request $request){
        $idSubject = $request->id;
        $user = Auth::user();
        $idUser = $user->id;
        $idSubject1=SubjectParallel::where('id_subject2',$idSubject)->pluck('id_subject1')->toArray();
        if(count($idSubject1) >0) {
            $nameSubjectParallel=Subjects::where('id',$idSubject1)->first();
            $countIsLearned2 = ResultRegister::where('id_user_student', $idUser)->where('id_subject',$idSubject1)->where('is_learned', 2)->get()->count();
            $countIsLearned1 = ResultRegister::where('id_user_student', $idUser)->where('id_subject',$idSubject1)->where('is_learned', 1)->get()->count();
            if($countIsLearned2 > 0 || $countIsLearned1 > 0 ){

            } else {
                return response()->json([
                    'status'  => false,
                    'message' => trans('Bạn phải đăng ký môn '.$nameSubjectParallel->name.' trước'),
                ]);
            }
        }
    }



    //point 
    public function resultPoint(Request $request){
                    $script = <<<EOT
        $(function () {
            $('.grid-refresh').hide();
        });
EOT;
            User::script($script);
        $idTimeRegister = $request->id;
        return User::GridUser(ResultRegister::class, function (GridUser $grid)  use($idTimeRegister) {
            $user = Auth::user();
            // $timeRegister = TimeRegister::orderBy('id', 'DESC')->first();
            // $timeRegister = TimeRegister::where('id', $idTimeRegister)->first();

            $grid->model()->where('id_user_student', $user->id)->where('time_register', $idTimeRegister);

            $grid->column('Mã MH')->display(function(){
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
            $grid->column('Sô tín chỉ hiện tại')->display(function () use ($idTimeRegister){
                $idUser = Auth::user()->id;
                $idSubject = ResultRegister::where('id_user_student', $idUser)->where('time_register', $idTimeRegister)->pluck('id_subject');
                $subjects = Subjects::find($idSubject);
                $sumCredit = 0;
                foreach ($subjects as $subject){
                    $sumCredit+=$subject->credits;
                }
                return $sumCredit;

            });
            $grid->column('Điểm TK ALL')->display(function ()use ($idTimeRegister){
                $idUser = Auth::user()->id;
                $resultUsers = ResultRegister::where('id_user_student', $idUser)->where('time_register', $idTimeRegister)->get()->toArray();
                $sum = 0;
                $countCredit = 0;
                foreach ($resultUsers as $resultUser){
                    $credits = Subjects::find($resultUser['id_subject'])->credits;
                    $countCredit += $credits;
                }
                foreach ($resultUsers as $resultUser){
                    $credits = Subjects::find($resultUser['id_subject'])->credits;
                    $sum += ((($resultUser['attendance'] * $resultUser['rate_attendance']) +
                        ($resultUser['mid_term'] * $resultUser['rate_mid_term']) +
                        ($resultUser['end_term'] * $resultUser['rate_end_term'])) / 100)*$credits;
                }
                return round($sum/$countCredit, 2);
            });
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableActions();
            $grid->disableFilter();
            
        });
    }

    public function resultTimeRegister (Request $request){
        $idTimeRegister = $request->id;
        return User::gridUser(ResultRegister::class, function (GridUser $grid) use($idTimeRegister) {
            $user = Auth::user();
            $timeRegister = TimeRegister::find($idTimeRegister)->first();
            $grid->model()->where('time_register', $idTimeRegister)->where('id_user_student', $user->id);
               // $grid->id('ID');
            $grid->column('Mã học phần')->display(function () {
                    $subjectRegister = SubjectRegister::where('id',$this->id_subject_register)->first();
                    if (!empty($subjectRegister)) {
                        return $subjectRegister->id;
                    } else {
                        return '';
                    }
                });            
            $grid->id_subjects('Môn học')->display(function () {
                    $idSubject = $this->id_subject;
                    if (!empty($idSubject)) {
                        return Subjects::find($idSubject)->name;
                    } else {
                        return '';
                    }
                });
            $grid->column('Phòng')->display(function () {
                    $idClassroom = TimeStudy::where('id_subject_register', $this->id_subject_register)->pluck('id_classroom')->toArray();
                    $classRoom = Classroom::whereIn('id', $idClassroom)->pluck('name')->toArray();
                    $classRoom = array_map(function ($classRoom) {
                        return "<span class='label label-success'>{$classRoom}</span>";
                    }, $classRoom);
                    return join('&nbsp;', $classRoom);
                });
            $grid->column('Buổi học')->display(function () {
                    $day = TimeStudy::where('id_subject_register', $this->id_subject_register)->pluck('day')->toArray();
                    $day = array_map(function ($day) {
                        switch ($day) {
                            case 2:
                                $day = 'Thứ 2';
                                break;
                            case 3:
                                $day = 'Thứ 3';
                                break;
                            case 4:
                                $day = 'Thứ 4';
                                break;
                            case 5:
                                $day = 'Thứ 5';
                                break;
                            case 6:
                                $day = 'Thứ 6';
                                break;
                            case 7:
                                $day = 'Thứ 7';
                                break;
                            case 8:
                                $day = 'Chủ nhật';
                                break;
                        }

                        return "<span class='label label-success'>{$day}</span>";
                    }, $day);
                    return join('&nbsp;', $day);
                });
            $grid->column('Thời gian học')->display(function () {
                    $timeStart = TimeStudy::where('id_subject_register', $this->id_subject_register)->pluck('time_study_start')->toArray();
                    $timeEnd = TimeStudy::where('id_subject_register', $this->id_subject_register)->pluck('time_study_end')->toArray();
                    $time = array_map(function ($timeStart, $timeEnd) {
                        return "<span class='label label-success'>{$timeStart} - {$timeEnd}</span>";
                    }, $timeStart, $timeEnd);
                    return join('&nbsp;', $time);
                });
                // $grid->id_user_teacher('Giảng viên')->display(function ($id_user_teacher) {
                //     if ($id_user_teacher) {
                //         $teacher = UserAdmin::find($id_user_teacher);
                //         if ($teacher) {
                //             return $teacher->name;
                //         } else {
                //             return '';
                //         }
                //     } else {
                //         return '';
                //     }
                // });
            $grid->column('Giảng viên')->display(function () {
                    $idSubjectRegister = $this->id_subject_register;
                    $subjectRegister = SubjectRegister::where('id',$this->id_subject_register)->first();
                    if (!empty($subjectRegister)) {
                        $teacher = UserAdmin::find($subjectRegister->id_user_teacher);
                        if ($teacher) {
                            return $teacher->name;
                        } else {
                            return '';
                        }
                    } else {
                        return '';
                    }
                });
            $grid->column('Ngày bắt đầu')->display(function (){
                $idSubjectRegister = $this->id_subject_register;
                $subjectRegister = SubjectRegister::find($idSubjectRegister);
                if($subjectRegister->date_start){
                    return $subjectRegister->date_start;
                } else {
                    return '';
                }
            });
            $grid->column('Ngày kết thúc')->display(function (){
                $idSubjectRegister = $this->id_subject_register;
                $subjectRegister = SubjectRegister::find($idSubjectRegister);
                if($subjectRegister->date_end){
                    return $subjectRegister->date_end;
                } else {
                    return '';
                }
            });
                // $grid->qty_current('Số lượng hiện tại');
                // $grid->qty_max('Số lượng tối đa');
                // $grid->date_start('Ngày bắt đầu');
                // $grid->date_end('Ngày kết thúc');
            $grid->column('Sô tín chỉ hiện tại')->display(function () use ($idTimeRegister){
                $idUser = Auth::user()->id;
                $idSubject = ResultRegister::where('id_user_student', $idUser)->where('time_register', $idTimeRegister)->pluck('id_subject');
                $subjects = Subjects::find($idSubject);
                $sumCredit = 0;
                foreach ($subjects as $subject){
                    $sumCredit+=$subject->credits;
                }
                return $sumCredit;

            });
            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableFilter();
            if($timeRegister->status == 0) {
                $grid->disableActions();
            } 
            $grid->actions(function ($actions){
                $actions->disableEdit();
                $actions->disableDelete();
                $actions->append('<a href="javascript:void(0);" data-id="' . $this->row->id_subject_register . '"  class="btn btn-danger btnCancel" style="font-size: 1.5rem"><i class="glyphicon glyphicon-trash"></i> &nbsp Hủy bỏ </a>');
            });
            $cancel = trans('Hủy bỏ');
            $cancelConfirm = trans('Bạn có chắc chắn muốn hủy không?');
            $confirmDelete = trans('Hủy đăng ký');
            $script = <<<SCRIPT
$('.btnCancel').unbind('click').click(function() {
    var id = $(this).data('id');
    swal({
      title: "$cancelConfirm",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#dd4b39",
      confirmButtonText: "$confirmDelete",
      closeOnConfirm: false,
      cancelButtonText: "$cancel"
    },
    function(){
        $.ajax({
            method: 'get',
            url: '/user/subject-register/' + id + '/delete-register',
            data: {
                _method:'deleteRegister',
                _token:LA.token,
            },
            success: function (data) {
                if (typeof data === 'object') {
                    if (data.status) {
                         swal({
                              title: "Hủy thành công", 
                              type: "success"
                             },function() {
                              location.reload();
                             
                         });
                    } else {
                        swal(data.message, '', 'error');
                    }
                }
            }
        });
    });
});
SCRIPT;
                User::script($script);
        });
    }

      protected function resultTimetable(Request $request)
    {
        $idTimeRegister = $request->id;
        $user = Auth::user();
        $idUser =  $user->id;
        $idSubjectRegister = ResultRegister::where('time_register',$idTimeRegister)->where('id_user_student', $idUser)->pluck('id_subject_register')->toArray();
        $timeStudys = TimeStudy::whereIn('id_subject_register', $idSubjectRegister)->get()->toArray();

        $arrDays = ["Thứ 2", "Thứ 3", "Thứ 4", "Thứ 5", "Thứ 6", "Thứ 7", "Chủ nhật"];
        $arrPeriods =DB::table('time_table')->select('time_start', 'time_end')->get();
        $arrPeriods = collect($arrPeriods)->map(function($x){ return (array) $x; })->toArray();

        ?>
        </div>
        <div class="row">
            <div class="col-sm-8">
                <h1 class="text-center">Thời Khóa Biểu</h1>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th style="background-color: transparent; border-top-color: white !important; border-left-color: white; " class="th-object"></th>
                        <?php
                        foreach ($arrDays as $key => $item) {
                            echo "<th style='text-align: center' class='th-object'>" . $item . "</th>";
                        }
                        ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $arrayTable = [];
                    foreach ($arrPeriods as $periodKey => $arrPeriod) {
                        $start = strtotime($arrPeriod["time_start"]);
                        $end = strtotime($arrPeriod["time_end"]);
                        foreach ($arrDays as $key => $day) {
                            foreach ($timeStudys as $timeStudy) {
                                $startTime = strtotime($timeStudy['time_study_start']);
                                $endTime = strtotime($timeStudy['time_study_end']);
                                if ($timeStudy['day'] == ($key + 2) && $start >= $startTime && $end <= $endTime) {
                                    $idSubject = SubjectRegister::where('id', $timeStudy['id_subject_register'])->first();
                                    if (!empty($idSubject)) {
                                        $idSubject = $idSubject->id_subjects;
                                        $isExisted = false;
                                        if(isset($arrayTable[$key])) {
                                            foreach ($arrayTable[$key] as $pSubKey => $item) {
                                                if (isset($item[$idSubject])) {
                                                    $arrayTable[$key][$pSubKey][$idSubject] = $arrayTable[$key][$pSubKey][$idSubject] + 1;
                                                    $isExisted = true;
                                                }
                                            }
                                        }
                                        if (!$isExisted) {
                                            $arrayTable[$key][$periodKey][$idSubject] = 1;
                                        } else {
                                            $arrayTable[$key][$periodKey] = false;
                                        }
                                    }
                                } else if (!isset($arrayTable[$key][$periodKey])) $arrayTable[$key][$periodKey] = array();

                            }
                        }
                    }
                    foreach ($arrPeriods as $periodKey => $item) {
                        echo "<tr>";
                        echo "<td class='td-object'>Tiết " . ($periodKey + 1) . "</td>";

                        foreach ($arrDays as $dayKey => $day) {
                            if(isset($arrayTable[$dayKey][$periodKey])) {
                                if ($arrayTable[$dayKey][$periodKey] && count($arrayTable[$dayKey][$periodKey]) > 0) {
                                    $count = 1;
                                    $subjectId = array_keys($arrayTable[$dayKey][$periodKey])[0];
                                    $count = array_values($arrayTable[$dayKey][$periodKey])[0];
                                    $nameSubject = Subjects::where("id", $subjectId)->first();
                                    echo "<td rowspan='$count' style='background-color:#ecf0f1;border-color:Gray;border-width:1px;border-style:solid;height:22px;width:110px;color:Teal;text-align:center'>$nameSubject->name</td>";
                                } else if(is_array($arrayTable[$dayKey][$periodKey])){// nếu như là array thì render
                                    echo "<td rowspan='1' class='td-object'></td>";
                                }
                            } else {
                                echo "<td rowspan='1' style='border-color:Gray;border-width:1px;border-style:solid;height:22px;width:110px;'></td>";
                            }
                        }
                        echo "<td class='td-object'>Tiết " . ($periodKey + 1) . "</td>";

                        echo "</tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="col-sm-4">
                <h1 class="text-center">Ghi Chú</h1>
                <ul class="list-unstyled text-center">
                    <?php
                    $i=0;
                    foreach ($arrPeriods as $key => $valuePriods ){
                        //$valuePriods['time_start']." ".$valuePriods['time_end'];
                        ?>
                        <li style="font-size: 20px;"> <?php echo "Tiết ". ($key + 1) . ": " .$valuePriods['time_start']." - ".$valuePriods['time_end'];?></li>
                        <?php
                    }
                    ?>

                </ul>
            </div>
        </div>
        <style type="text/css">
            th.th-object.th-object, td.td-object:first-child, td.td-object:last-child{
                text-align: center;
                background-color: #6699CC;
                color: #fff;
            }
            th.th-object.th-object,.table-bordered>tbody>tr>td.td-object{
                border: 1px solid #000;
                border-top: 1px solid #000 !important;
                width: 200px;
            }
        </style>
        <?php
    }

}

