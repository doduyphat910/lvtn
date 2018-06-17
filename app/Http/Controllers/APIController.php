<?php
namespace App\Http\Controllers;


use App\Models\ResultRegister;
use App\Models\SubjectRegister;
use App\Models\TimeRegister;
use App\Models\TimeStudy;
use App\Models\UserAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class APIController extends Controller {

//    Protected function getListSubjectRegister (Request $request) {
//        $idSubject = $request->id;
//        $subjectRegister = SubjectRegister::where('id_subjects', $idSubject)->get()->toArray();
//        array_unshift($subjectRegister, '');
//        foreach ($subjectRegister as $key => $sr) {
//                if($key != 0) {
//                    $teacher = UserAdmin::find($sr['id_user_teacher']);
//                    if($teacher != null) {
//                        $nameTeacher = $teacher->name;
//                    } else {
//                        $nameTeacher = '';
//                    }
//                    echo "<option value='".$sr['id']."'>" ."Mã HP: ".$sr['code_subject_register'].
//                        " - SLHT: ". $sr['qty_current']." - SLTD: ". $sr['qty_max']." - GV: ". $nameTeacher."</option>";
//                } else {
//                    $a = '--------------------------------------------------------';
//                    echo "<option value='' style='display: none'>".$a."</option>";
//                }
//        }
//    }
        Protected function getTimetable (Request $request)
        {
            $idUser = Auth::user()->id;
            $timeRegister = TimeRegister::where('status', 1)->orderBy('id', 'DESC')->first();
            $idTimeRegister = $timeRegister->id;
            $idSubjectRegister = ResultRegister::where('id_user_student', $idUser)->where('time_register', $idTimeRegister)->pluck('id_subject_register');
            $timeStudy = TimeStudy::find($idSubjectRegister);
            return json_encode($timeStudy);

        }

}

