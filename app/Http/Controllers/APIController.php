<?php
namespace App\Http\Controllers;


use App\Models\Rate;
use App\Models\ResultRegister;
use App\Models\SubjectRegister;
use App\Models\Subjects;
use App\Models\TimeRegister;
use App\Models\TimeStudy;
use App\Models\UserAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class APIController extends Controller {

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
        if($countSubject >= 1) {
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

        //kiểm tra giờ học



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
            $resultRegister->final = null;
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

}

