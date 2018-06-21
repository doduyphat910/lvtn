<?php
namespace App\Admin\Controllers;
use App\Http\Controllers\Controller;
use App\Models\ResultRegister;
use App\Models\StudentUser;
use App\Models\SubjectRegister;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Maatwebsite\Excel\Facades\Excel;

class ImportPointController extends Controller
{
    public function attendance($idSubjectRegister)
    {
        return Admin::content(function (Content $content) use ($idSubjectRegister) {

            $content->header('Điểm');
            $content->description('Import điểm chuyên cần');
            $content->body(view('admin.ImportPoint.import', ['router_target' => '/admin/import_student/review', 'idSubjectRegister' => $idSubjectRegister]));

        });
    }

    public function exportAttendance($idSubjectRegister) {

    $resultRegisters = ResultRegister::where('id_subject_register', $idSubjectRegister)->get()->toArray();
    $subjectRegister = SubjectRegister::where('id',$idSubjectRegister)->first();
    $codeSubjectRegister = $subjectRegister->code_subject_register;

    // Generate and return the spreadsheet
            Excel::create('Lớp_'.$codeSubjectRegister, function($excel) use ($resultRegisters,$codeSubjectRegister)  {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Payments');
            $excel->setCreator('Laravel')->setCompany('WJ Gilmore, LLC');
            $excel->setDescription('payments file');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('Lớp '.$codeSubjectRegister, function($sheet) use ($resultRegisters) {
//                $sheet->rows(['123123123']);
                $sheet->rows([
                    [
                        'MSSV',
                        'Họ',
                        'Tên',
                        'Điểm chuyên cần'
                    ]
                ]);
                foreach($resultRegisters as $resultRegister) {
                    if(!empty($resultRegister['id_user_student'])){
                        $student = StudentUser::find($resultRegister['id_user_student']);
                        if(!empty($student))
                        $codeNumber = $student->code_number;
                        $firstName = $student->first_name;
                        $lastName = $student->last_name;
                        $attendance = $resultRegister['attendance'];
                    } else {
                        $codeNumber = '';
                        $firstName = '';
                        $lastName = '';
                        $attendance = '';
                    }



                    $sheet->rows([
                        [
                            $codeNumber,
                            $firstName,
                            $lastName,
                            $attendance
                        ]
                    ]);
                }
            });

        })->download('xlsx');
    }
}