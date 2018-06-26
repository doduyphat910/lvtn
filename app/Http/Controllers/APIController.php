<?php
namespace App\Http\Controllers;


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

}

