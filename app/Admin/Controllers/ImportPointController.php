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
    #region attendance
    public function attendance($idSubjectRegister)
    {
        return Admin::content(function (Content $content) use ($idSubjectRegister) {

            $content->header('Điểm chuyên cần');
            $subjectRegister = SubjectRegister::find($idSubjectRegister);
            $codeSubjectRegister = $subjectRegister->id;
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
            $csv_data_file->id_user = Admin::user()->id;
            $csv_data_file->save();
        }
        return Admin::content(function (Content $content) use ($csv_data, $csv_data_file, $idSubjectRegister) {
            $content->header('Điểm chuyên cần');
            $subjectRegister = SubjectRegister::find($idSubjectRegister);
            $codeSubjectRegister = $subjectRegister->id;
            $content->description('Import ' . $codeSubjectRegister);
            $arrFields = [
                'mssv',
                'ho',
                'ten',
                'diem_chuyen_can'
            ];
            $content->body(view('admin.ImportPoint.import_review',
                [
                    'csv_data' => $csv_data,
                    'csv_data_file' => $csv_data_file,
                    'idSubjectRegister' =>$idSubjectRegister,
                    'code_subject_regiister'=>$codeSubjectRegister,
                    'arrFields' => $arrFields,
                    'routerTarget' => '/admin/teacher/import-attendance/parse'

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
                if (empty($row ['mssv']) ||  (empty($row ['diem_chuyen_can'])&& $row ['diem_chuyen_can'] != 0) ) {
                    $row_error += 1;
                    $error_logs[$key] = $row['mssv'] . ' trống dữ liệu';
                }
             else {
                $idUserSubject = ResultRegister::where('id_subject_register', $idSubjectRegister)->pluck('id_user_student');
                $arrCodeStudent = StudentUser::find($idUserSubject)->pluck('code_number')->toArray();
                if (!in_array($row['mssv'], $arrCodeStudent)) {
                    $row_error += 1;
                    $error_logs[$key] = $row['mssv'] .', sv không có trong lớp này';
                }
                if($row['diem_chuyen_can'] < 0 || $row['diem_chuyen_can'] > 10) {
                    $row_error += 1;
                    $error_logs[$key] = $row['mssv'] .', cột điểm chuyên cần sai';
                }
                if(!is_numeric($row['diem_chuyen_can'])){
                    $row_error += 1;
                    $error_logs[$key] = $row['mssv'] .', cột điểm chuyên cần sai';
                }
            }
        }
        foreach($data as $key => $row) {
            if ($row_error == 0) {
                $idUser = StudentUser::where('code_number', $row['mssv'])->pluck('id');
                $userSubject = ResultRegister::where('id_user_student', $idUser)->where('id_subject_register',$idSubjectRegister)->first();
                $userSubject->is_learned = 1;
                $userSubject->attendance = $row['diem_chuyen_can'];
                if($userSubject->save()) {
                    $row_add_successs += 1;
                }
            } else {
                $row_error += 1;
            }
        }
        $subjectRegister = SubjectRegister::find($idSubjectRegister);
        $codeSubjectRegister = $subjectRegister->id;
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
    $codeSubjectRegister = $subjectRegister->id;

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
    #endregion


    #region midterm
    public function midTerm($idSubjectRegister)
    {
        return Admin::content(function (Content $content) use ($idSubjectRegister) {

            $content->header('Điểm giữa kì');
            $subjectRegister = SubjectRegister::find($idSubjectRegister);
            $codeSubjectRegister = $subjectRegister->id;
            $content->description('Import '.$codeSubjectRegister);
            $content->body(view('admin.ImportPoint.import', ['router_target' => '/admin/teacher/import-midterm/review', 'idSubjectRegister' => $idSubjectRegister]));

        });
    }

    public function reviewMidterm(Request $request) {
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
            $csv_data_file->model = 'mid_term_point';
            $csv_data_file->data = json_encode($data);
            $csv_data_file->id_user = Admin::user()->id;
            $csv_data_file->save();
        }
        return Admin::content(function (Content $content) use ($csv_data, $csv_data_file, $idSubjectRegister) {
            $content->header('Điểm giữa kì');
            $subjectRegister = SubjectRegister::find($idSubjectRegister);
            $codeSubjectRegister = $subjectRegister->id;
            $content->description('Import ' . $codeSubjectRegister);
            $arrFields = [
                'mssv',
                'ho',
                'ten',
                'diem_giua_ki'
            ];
            $content->body(view('admin.ImportPoint.import_review',
                [
                    'csv_data' => $csv_data,
                    'csv_data_file' => $csv_data_file,
                    'idSubjectRegister' =>$idSubjectRegister,
                    'code_subject_regiister'=>$codeSubjectRegister,
                    'arrFields' => $arrFields,
                    'routerTarget' => '/admin/teacher/import-midterm/parse'
                ]
            ));
        });
    }

    public function parseMidterm (Request $request){
        $csv_data = CsvData::find($request->csv_data_file_id);
        $data = json_decode($csv_data->data, true);
        $idSubjectRegister = $request->idSubjectRegister;
        $row_error = 0 ;
        $row_add_successs = 0;
        $error_logs = [];
        foreach($data as $key => $row) {
                if (empty($row ['mssv']) ||  (empty($row ['diem_giua_ki'])&& $row ['diem_giua_ki'] != 0) ) {
                $row_error += 1;
                $error_logs[$key] = $row['mssv'] . ' trống dữ liệu';
                }
             else {
                $idUserSubject = ResultRegister::where('id_subject_register', $idSubjectRegister)->pluck('id_user_student');
                if(!$idUserSubject) {
                    $row_error += 1;
                    $error_logs[$key] = $row['mssv'] .', không tồn tại';
                }
                $arrCodeStudent = StudentUser::find($idUserSubject)->pluck('code_number')->toArray();
                if (!in_array($row['mssv'], $arrCodeStudent)) {
                    $row_error += 1;
                    $error_logs[$key] = $row['mssv'] .', sv không có trong lớp này';
                }
                if($row['diem_giua_ki'] < 0 || $row['diem_giua_ki'] > 10) {
                    $row_error += 1;
                    $error_logs[$key] = $row['mssv'] .', cột điểm chuyên cần sai';
                }
                if(!is_numeric($row['diem_giua_ki'])){
                    $row_error += 1;
                    $error_logs[$key] = $row['mssv'] .', cột điểm chuyên cần sai';
                }
            }
        }
        foreach($data as $key => $row) {
            if ($row_error == 0) {
                $idUser = StudentUser::where('code_number', $row['mssv'])->pluck('id');
                $userSubject = ResultRegister::where('id_user_student', $idUser)->where('id_subject_register',$idSubjectRegister)->first();
                $userSubject->is_learned = 1;
                $userSubject->mid_term = $row['diem_giua_ki'];
                if($userSubject->save()) {
                    $row_add_successs += 1;
                }
            } else {
                $row_error += 1;
            }
        }
        $subjectRegister = SubjectRegister::find($idSubjectRegister);
        $codeSubjectRegister = $subjectRegister->id;
        return Admin::content(function (Content $content) use ($row_error, $error_logs, $row_add_successs, $codeSubjectRegister) {
            $content->header('Điểm giữa kì');
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



    public function exportMidterm($idSubjectRegister) {

        $resultRegisters = ResultRegister::where('id_subject_register', $idSubjectRegister)->get()->toArray();
        $subjectRegister = SubjectRegister::where('id',$idSubjectRegister)->first();
        $codeSubjectRegister = $subjectRegister->id;

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
                        'Điểm giữa kì'
                    ]
                ]);
                foreach($resultRegisters as $resultRegister) {
                    if(!empty($resultRegister['id_user_student'])){
                        $student = StudentUser::find($resultRegister['id_user_student']);
                        if(!empty($student))
                            $codeNumber = $student->code_number;
                        $firstName = $student->first_name;
                        $lastName = $student->last_name;
                        $midterm = $resultRegister['mid_term'];
                    } else {
                        $codeNumber = '';
                        $firstName = '';
                        $lastName = '';
                        $midterm = '';
                    }



                    $sheet->rows([
                        [
                            $codeNumber,
                            $firstName,
                            $lastName,
                            $midterm
                        ]
                    ]);
                }
            });

        })->download('xlsx');
    }
    #endregion


    #region end-term
    public function endTerm($idSubjectRegister)
    {
        return Admin::content(function (Content $content) use ($idSubjectRegister) {

            $content->header('Điểm cuối kì');
            $subjectRegister = SubjectRegister::find($idSubjectRegister);
            $codeSubjectRegister = $subjectRegister->id;
            $content->description('Import '.$codeSubjectRegister);
            $content->body(view('admin.ImportPoint.import', ['router_target' => '/admin/teacher/import-endterm/review', 'idSubjectRegister' => $idSubjectRegister]));

        });
    }

    public function reviewEndterm(Request $request) {
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
            $csv_data_file->model = 'end_term_point';
            $csv_data_file->data = json_encode($data);
            $csv_data_file->id_user = Admin::user()->id;
            $csv_data_file->save();
        }
        return Admin::content(function (Content $content) use ($csv_data, $csv_data_file, $idSubjectRegister) {
            $content->header('Điểm giữa kì');
            $subjectRegister = SubjectRegister::find($idSubjectRegister);
            $codeSubjectRegister = $subjectRegister->id;
            $content->description('Import ' . $codeSubjectRegister);
            $arrFields = [
                'mssv',
                'ho',
                'ten',
                'diem_cuoi_ki'
            ];
            $content->body(view('admin.ImportPoint.import_review',
                [
                    'csv_data' => $csv_data,
                    'csv_data_file' => $csv_data_file,
                    'idSubjectRegister' =>$idSubjectRegister,
                    'code_subject_regiister'=>$codeSubjectRegister,
                    'arrFields' => $arrFields,
                    'routerTarget' => '/admin/teacher/import-endterm/parse'
                ]
            ));
        });
    }

    public function parseEndterm (Request $request){
        $csv_data = CsvData::find($request->csv_data_file_id);
        $data = json_decode($csv_data->data, true);
        $idSubjectRegister = $request->idSubjectRegister;
        $row_error = 0 ;
        $row_add_successs = 0;
        $error_logs = [];
        foreach($data as $key => $row) {
                if (empty($row ['mssv']) || (empty($row ['diem_cuoi_ki'])&& $row ['diem_cuoi_ki'] != 0)) {
                    $row_error += 1;
                    $error_logs[$key] = $row['mssv'] . ' trống dữ liệu';
                }
             else {
                $idUserSubject = ResultRegister::where('id_subject_register', $idSubjectRegister)->pluck('id_user_student');
                $arrCodeStudent = StudentUser::find($idUserSubject)->pluck('code_number')->toArray();
                if (!in_array($row['mssv'], $arrCodeStudent)) {
                    $row_error += 1;
                    $error_logs[$key] = $row['mssv'] .', sv không có trong lớp này';
                }
                if($row['diem_cuoi_ki'] < 0 || $row['diem_cuoi_ki'] > 10) {
                    $row_error += 1;
                    $error_logs[$key] = $row['mssv'] .', cột điểm cuối kì sai';
                }
                if(!is_numeric($row['diem_cuoi_ki'])){
                    $row_error += 1;
                    $error_logs[$key] = $row['mssv'] .', cột điểm cuối kì sai';
                }
            }
        }
        foreach($data as $key => $row) {
            if ($row_error == 0) {
                $idUser = StudentUser::where('code_number', $row['mssv'])->pluck('id');
                $userSubject = ResultRegister::where('id_user_student', $idUser)->where('id_subject_register',$idSubjectRegister)->first();
                $userSubject->end_term = $row['diem_cuoi_ki'];
                $userSubject->is_learned = 1;
                if($userSubject->save()) {
                    $row_add_successs += 1;
//                    if($userSubject->mid_term && $userSubject->attendance) {
//                        $final = (($userSubject->attendance * $userSubject->rate_attendance) +
//                        ($userSubject->mid_term * $userSubject->rate_mid_term) +
//                        ($userSubject->end_term * $userSubject->rate_end_term))/100;
//                        if($final >= 4.5) {
//                            $userSubject->is_learned = 1;
//                            $userSubject->save();
//                        } else {
//                            $userSubject->is_learned = 0;
//                            $userSubject->save();
//                        }
//                    }
                }
            } else {
                $row_error += 1;
            }
        }
        $subjectRegister = SubjectRegister::find($idSubjectRegister);
        $codeSubjectRegister = $subjectRegister->id;
        return Admin::content(function (Content $content) use ($row_error, $error_logs, $row_add_successs, $codeSubjectRegister) {
            $content->header('Điểm cuối cần');
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

    public function exportEndterm($idSubjectRegister) {

        $resultRegisters = ResultRegister::where('id_subject_register', $idSubjectRegister)->get()->toArray();
        $subjectRegister = SubjectRegister::where('id',$idSubjectRegister)->first();
        $codeSubjectRegister = $subjectRegister->id;

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
                        'Điểm cuối kì'
                    ]
                ]);
                foreach($resultRegisters as $resultRegister) {
                    if(!empty($resultRegister['id_user_student'])){
                        $student = StudentUser::find($resultRegister['id_user_student']);
                        if(!empty($student))
                            $codeNumber = $student->code_number;
                        $firstName = $student->first_name;
                        $lastName = $student->last_name;
                        $endterm = $resultRegister['end_term'];
                    } else {
                        $codeNumber = '';
                        $firstName = '';
                        $lastName = '';
                        $endterm = '';
                    }



                    $sheet->rows([
                        [
                            $codeNumber,
                            $firstName,
                            $lastName,
                            $endterm
                        ]
                    ]);
                }
            });

        })->download('xlsx');
    }
    #endregion

    public function all($idSubjectRegister)
    {
        return Admin::content(function (Content $content) use ($idSubjectRegister) {

            $content->header('Điểm');
            $subjectRegister = SubjectRegister::find($idSubjectRegister);
            $codeSubjectRegister = $subjectRegister->id;
            $content->description('Import '.$codeSubjectRegister);
            $content->body(view('admin.ImportPoint.import', ['router_target' => '/admin/teacher/import-all/review', 'idSubjectRegister' => $idSubjectRegister]));

        });
    }

    public function reviewAll(Request $request) {
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
            $csv_data_file->model = 'all_point';
            $csv_data_file->data = json_encode($data);
            $csv_data_file->id_user = Admin::user()->id;
            $csv_data_file->save();
        }
        return Admin::content(function (Content $content) use ($csv_data, $csv_data_file, $idSubjectRegister) {
            $content->header('Điểm');
            $subjectRegister = SubjectRegister::find($idSubjectRegister);
            $codeSubjectRegister = $subjectRegister->id;
            $content->description('Import ' . $codeSubjectRegister);
            $arrFields = [
                'mssv',
                'ho',
                'ten',
                'diem_chuyen_can',
                'diem_giua_ki',
                'diem_cuoi_ki'
            ];
            $content->body(view('admin.ImportPoint.import_review',
                [
                    'csv_data' => $csv_data,
                    'csv_data_file' => $csv_data_file,
                    'idSubjectRegister' =>$idSubjectRegister,
                    'code_subject_regiister'=>$codeSubjectRegister,
                    'arrFields' => $arrFields,
                    'routerTarget' => '/admin/teacher/import-all/parse'
                ]
            ));
        });
    }

    public function parseAll (Request $request){
        $csv_data = CsvData::find($request->csv_data_file_id);
        $data = json_decode($csv_data->data, true);
        $idSubjectRegister = $request->idSubjectRegister;
        $row_error = 0 ;
        $row_add_successs = 0;
        $error_logs = [];
        foreach($data as $key => $row) {
            if(!isset($row ['mssv']) || !isset($row ['diem_chuyen_can'])
                || !isset($row ['diem_giua_ki'])|| !isset($row ['diem_cuoi_ki']) ){
                $row_error += 1;
                $error_logs[$key] = ' dữ liệu sai';
                break;
            }
                if (empty($row ['mssv'])||  (empty($row ['diem_chuyen_can'])&& $row ['diem_chuyen_can'] != 0) ||
                    (empty($row ['diem_giua_ki'])&& $row ['diem_giua_ki'] != 0) ||
                    (empty($row ['diem_cuoi_ki'])&& $row ['diem_cuoi_ki'] != 0) ) {
                    $row_error += 1;
                    $error_logs[$key] = $row['mssv'] . ' trống dữ liệu';
                }
             else {
                $idUserSubject = ResultRegister::where('id_subject_register', $idSubjectRegister)->pluck('id_user_student');
                $arrCodeStudent = StudentUser::find($idUserSubject)->pluck('code_number')->toArray();
                if (!in_array($row['mssv'], $arrCodeStudent)) {
                    $row_error += 1;
                    $error_logs[$key] = $row['mssv'] .', sv không có trong lớp này';
                }
//                if($row['diem_chuyen_can'] < 0 || $row['diem_chuyen_can'] > 10) {
//                    $row_error += 1;
//                    $error_logs[$key] = $row['mssv'] .', cột điểm chuyên cần sai';
//                }
//                if($row['diem_giua_ki'] < 0 || $row['diem_giua_ki'] > 10) {
//                    $row_error += 1;
//                    $error_logs[$key] = $row['mssv'] .', cột điểm giữa kì sai';
//                }
//                if($row['diem_cuoi_ki'] < 0 || $row['diem_cuoi_ki'] > 10) {
//                    $row_error += 1;
//                    $error_logs[$key] = $row['mssv'] .', cột điểm cuối kì sai';
//                }
                switch (true){
                    case($row['diem_chuyen_can'] < 0 || $row['diem_chuyen_can'] > 10):
                        $row_error += 1;
                        $error_logs[$key] = $row['mssv'] .', cột điểm chuyên cần sai';
                        break;
                    case($row['diem_giua_ki'] < 0 || $row['diem_giua_ki'] > 10):
                        $row_error += 1;
                        $error_logs[$key] = $row['mssv'] .', cột điểm giữa kì sai';
                        break;
                    case($row['diem_cuoi_ki'] < 0 || $row['diem_cuoi_ki'] > 10):
                        $row_error += 1;
                        $error_logs[$key] = $row['mssv'] .', cột điểm cuối kì sai';
                        break;
                }
//                if(!is_numeric($row['diem_cuoi_ki'])){
//                    $row_error += 1;
//                    $error_logs[$key] = $row['mssv'] .', cột điểm cuối kì sai';
//                }
                switch (true) {
                    case (!is_numeric($row['diem_chuyen_can'])):
                        $row_error += 1;
                        $error_logs[$key] = $row['mssv'] .', cột điểm chuyên cần sai';
                        break;
                    case (!is_numeric($row['diem_giua_ki'])):
                        $row_error += 1;
                        $error_logs[$key] = $row['mssv'] .', cột điểm giữa kì sai';
                        break;
                    case (!is_numeric($row['diem_cuoi_ki'])):
                        $row_error += 1;
                        $error_logs[$key] = $row['mssv'] .', cột điểm cuối kì sai';
                        break;
                }

            }
        }
        foreach($data as $key => $row) {
            if ($row_error == 0) {
                $idUser = StudentUser::where('code_number', $row['mssv'])->pluck('id');
                $userSubject = ResultRegister::where('id_user_student', $idUser)->where('id_subject_register',$idSubjectRegister)->first();
                $userSubject->attendance = $row['diem_chuyen_can'];
                $userSubject->mid_term = $row['diem_giua_ki'];
                $userSubject->end_term = $row['diem_cuoi_ki'];
                $userSubject->is_learned = 1;
                if($userSubject->save()) {
                    $row_add_successs += 1;
//                    if($userSubject->attendance &&$userSubject->mid_term && $userSubject->end_term) {
//                        $final = (($userSubject->attendance * $userSubject->rate_attendance) +
//                                ($userSubject->mid_term * $userSubject->rate_mid_term) +
//                                ($userSubject->end_term * $userSubject->rate_end_term))/100;
//                        if($final >= 4.5) {
//                            $userSubject->is_learned = 1;
//                            $userSubject->save();
//                        } else {
//                            $userSubject->is_learned = 0;
//                            $userSubject->save();
//                        }
//                    }
                }
            } else {
                $row_error += 1;
            }
        }
        $subjectRegister = SubjectRegister::find($idSubjectRegister);
        $codeSubjectRegister = $subjectRegister->id;
        return Admin::content(function (Content $content) use ($row_error, $error_logs, $row_add_successs, $codeSubjectRegister) {
            $content->header('Điểm cuối cần');
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


    public function exportAll($idSubjectRegister) {

        $resultRegisters = ResultRegister::where('id_subject_register', $idSubjectRegister)->get()->toArray();
        $subjectRegister = SubjectRegister::where('id',$idSubjectRegister)->first();
        $codeSubjectRegister = $subjectRegister->id;

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
                        'Điểm chuyên cần',
                        'Điểm giữa kì',
                        'Điểm cuối kì'
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
                        $midterm = $resultRegister['mid_term'];
                        $endterm = $resultRegister['end_term'];
                    } else {
                        $codeNumber = '';
                        $firstName = '';
                        $lastName = '';
                        $attendance = '';
                        $midterm = '';
                        $endterm = '';
                    }



                    $sheet->rows([
                        [
                            $codeNumber,
                            $firstName,
                            $lastName,
                            $attendance,
                            $midterm,
                            $endterm
                        ]
                    ]);
                }
            });

        })->download('xlsx');
    }

}