<?php
namespace App\Admin\Controllers;
use App\Http\Controllers\Controller;
use App\Models\CSVData;
use App\Models\ResultRegister;
use App\Models\StudentUser;
use App\Models\SubjectRegister;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportPointController extends Controller
{
    public function attendance($idSubjectRegister)
    {
        return Admin::content(function (Content $content) use ($idSubjectRegister) {

            $content->header('Điểm chuyên cần');
            $subjectRegister = SubjectRegister::find($idSubjectRegister);
            $codeSubjectRegister = $subjectRegister->code_subject_register;
            $content->description('Import '.$codeSubjectRegister);
            $content->body(view('admin.ImportPoint.import', ['router_target' => '/admin/teacher/import-attendance/review', 'idSubjectRegister' => $idSubjectRegister]));

        });
    }


    public function reviewAttendance(Request $request) {
        $path = $request->file('csv_file')->getRealPath();
        $data = Excel::load($path, function ($reader) {
        })->get()->toArray();
        $idSubjectRegister = $request->idSubjectRegister;
        if(count($data) > 0 ) {
            foreach($data as $key => $value) {
                $csv_data_field[] = $key;
            }
            $csv_data = array_slice($data, 0, 2);
            $csv_data_file = new CSVData();
            $csv_data_file->file_name = $request->file('csv_file')->getClientOriginalName();
            $csv_data_file->model = 'attendance_point';
            $csv_data_file->data = json_encode($data);
            $csv_data_file->save();
        }
        return Admin::content(function (Content $content) use ($csv_data, $csv_data_file, $idSubjectRegister) {
            $content->header('Điểm chuyên cần');
            $subjectRegister = SubjectRegister::find($idSubjectRegister);
            $codeSubjectRegister = $subjectRegister->code_subject_register;
            $content->description('Import ' . $codeSubjectRegister);
            $content->body(view('admin.ImportPoint.import_review',
                [
                    'csv_data' => $csv_data,
                    'csv_data_file' => $csv_data_file,
                    'idSubjectRegister' =>$idSubjectRegister,
                    'code_subject_regiister'=>$codeSubjectRegister
                ]
            ));

        });
    }

    public function parseAttendance (Request $request){
        $csv_data = CsvData::find($request->csv_data_file_id);
        $data = json_decode($csv_data->data, true);
        $idSubjectRegister = $request->idSubjectRegister;
        $row_error = 0 ;
        $row_add_successs = 0;
        $error_logs = [];
        foreach($data as $key => $row) {
            if (empty($row ['mssv']) ||  empty($row ['diem_chuyen_can']) ) {
                $row_error += 1;
                $error_logs[$key] = $row['mssv'] . ' trống dữ liệu';
            } else {
                $idUserSubject = ResultRegister::where('id_subject_register', $idSubjectRegister)->pluck('id_user_student');
                $arrCodeStudent = StudentUser::find($idUserSubject)->pluck('code_number')->toArray();
                if (!in_array($row['mssv'], $arrCodeStudent)) {
                    $row_error += 1;
                    $error_logs[$key] = $row['mssv'] .', mssv không có trong lớp này';
                }
                if($row['diem_chuyen_can'] < 0 || $row['diem_chuyen_can'] > 10) {
                    $row_error += 1;
                    $error_logs[$key] = $row['mssv'] .', cột điểm chuyên cần sai';
                }
            }
        }
        foreach($data as $key => $row) {
            if ($row_error == 0) {
                $idUser = StudentUser::where('code_number', $row['mssv'])->pluck('id');
                $userSubject = ResultRegister::where('id_user_student', $idUser)->where('id_subject_register',$idSubjectRegister)->first();
                $userSubject->attendance = $row['diem_chuyen_can'];
                if($userSubject->save()) {
                    $row_add_successs += 1;
                }
            } else {
                $row_error += 1;
            }
        }
        $subjectRegister = SubjectRegister::find($idSubjectRegister);
        $codeSubjectRegister = $subjectRegister->code_subject_register;
        return Admin::content(function (Content $content) use ($row_error, $error_logs, $row_add_successs, $codeSubjectRegister) {
            $content->header('Điểm chuyên cần');
            $content->description('Import '. $codeSubjectRegister);
            $content->body(view('admin.ImportPoint.import_parse',
                [
                    'row_error' => $row_error,
                    'error_logs' => $error_logs,
                    'row_add_successs' => $row_add_successs
                ]
            ));

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
            $excel->sheet('Lớp '.$codeSubjectRegister, function($sheet) use ($resultRegisters, $codeSubjectRegister) {
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