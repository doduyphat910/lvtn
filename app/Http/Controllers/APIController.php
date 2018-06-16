<?php
namespace App\Http\Controllers;


use App\Models\SubjectRegister;
use App\Models\UserAdmin;
use Illuminate\Http\Request;

class APIController extends Controller {

    Protected function getListSubjectRegister (Request $request) {
        $idSubject = $request->id;
        $subjectRegister = SubjectRegister::where('id_subjects', $idSubject)->get()->toArray();
        array_unshift($subjectRegister, '');
        foreach ($subjectRegister as $key => $sr) {
                if($key != 0) {
                    $teacher = UserAdmin::find($sr['id_user_teacher']);
                    if($teacher != null) {
                        $nameTeacher = $teacher->name;
                    } else {
                        $nameTeacher = '';
                    }
                    echo "<option value='".$sr['id']."'>" ."Mã HP: ".$sr['code_subject_register'].
                        " - SLHT: ". $sr['qty_current']." - SLTD: ". $sr['qty_max']." - GV: ". $nameTeacher."</option>";
                } else {
                    $a = '--------------------------------------------------------';
                    echo "<option value='' style='display: none'>".$a."</option>";
                }
        }
    }
}

