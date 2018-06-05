<?php
namespace App\Admin\Controllers;
use App\Http\Controllers\Controller;
use App\Models\ClassSTU;
use App\Models\CSVData;
use App\Models\Status;
use App\Models\StudentUser;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class ImportStudentController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('Sinh viên');
            $content->description('Import sinh viên');
            $content->body(view('admin.ImportStudent.import', ['router_target' => '/admin/import_student/review']));

        });
    }

    public function review(Request $request) {
        $path = $request->file('csv_file')->getRealPath();
        $data = Excel::load($path, function ($reader) {
        })->get()->toArray();
        if(count($data) > 0 ) {
            foreach($data as $key => $value) {
                $csv_data_field[] = $key;
            }
            $csv_data = array_slice($data, 0, 2);
            $csv_data_file = new CSVData();
            $csv_data_file->file_name = $request->file('csv_file')->getClientOriginalName();
            $csv_data_file->model = 'student_user';
            $csv_data_file->data = json_encode($data);
            $csv_data_file->save();
        }
        return Admin::content(function (Content $content) use ($csv_data, $csv_data_file) {
            $content->header('Sinh viên');
            $content->description('Import sinh viên');
            $content->body(view('admin.ImportStudent.import_review', [ 'csv_data' => $csv_data, 'csv_data_file' => $csv_data_file]));

        });
    }

    public function parse (Request $request){
        $csv_data = CsvData::find($request->csv_data_file_id);
        $data = json_decode($csv_data->data, true);
        $row_error = 0 ;
        $row_add_successs = 0;
        $error_logs = [];
        foreach($data as $key => $row) {
                if (empty($row ['mssv']) || empty($row ['ho']) || empty($row ['ten']) || empty($row ['lop'])
                    || empty($row ['nam_nhap_hoc']) || empty($row ['trinh_do']) || empty($row ['trang_thai']) ) {
                    $row_error += 1;
                    $error_logs[$key] = $row['mssv'] . ' không có dữ liệu';
                    break;
                } else {
                    $studentUser = new StudentUser();
                    $idClass = ClassSTU::where('name', $row['lop'])->pluck('id')->first();
                    if ($idClass == null) {
                        $row_error += 1;
                        $error_logs[$key] = 'Cột lớp không có trong cơ sở dữ liệu';
                        break;
                    }
                    if ($row['trinh_do'] != 'CD' && $row['trinh_do'] != 'DH') {
                        $row_error += 1;
                        $error_logs[$key] = 'Cột trình độ không được khác CD hoặc DH';
                        break;
                    }
                    if ($row['nam_nhap_hoc'] < 2000 || $row['nam_nhap_hoc'] > ((int)date("Y"))) {
                        $row_error += 1;
                        $error_logs[$key] = 'Cột năm nhập học sai dữ liệu';
                        break;
                    }
                    $status = Status::all()->pluck('ids')->toArray();
                    if (in_array($row['trang_thai'], $status) == false) {
                        $row_error += 1;
                        $error_logs[$key] = 'Trạng thái không có trong CSDL';
                        break;
                    }
                    $arrayCodeNumber = StudentUser::all()->pluck('code_number')->toArray();
                    if (in_array($row['mssv'], $arrayCodeNumber) == true) {
                        $row_error += 1;
                        $error_logs[$key] = $row['mssv'] . ' đã tồn tại';
                        break;
                    }
                    $studentUser->id_class = $idClass;
                    $studentUser->code_number = $row['mssv'];
                    $studentUser->first_name = $row['ho'];
                    $studentUser->last_name = $row['ten'];
                    $studentUser->email = $row['email'];
                    $studentUser->school_year = $row['nam_nhap_hoc'];
                    $studentUser->level = $row['trinh_do'];
                    $studentUser->id_status = $row['trang_thai'];
//                    $codeNumber = StudentUser::orderBy('code_number', 'DESC')->where('level', $row['level'])->where('school_year', $row['school_year'])
//                        ->pluck('code_number')->first();
//                    if (!$codeNumber) {
//                        $count = 0;
//                    } else {
//                        $count = substr($codeNumber, strlen($codeNumber) - 1, 1);
//                    }
//                    $year = substr($row['school_year'], 2, 2);
//                    $codeNumber = $row['level'] . '5' . $year . '00' . ((int)$count + 1);
//                    $studentUser->code_number = $codeNumber;
                    $studentUser->password = $studentUser->code_number;
                    if ($row_error == 0) {
                        $studentUser->save();
                        $row_add_successs += 1;
                    } else {
                        $row_error += 1;
                    }
                }
        }
            return Admin::content(function (Content $content) use ($row_error, $error_logs, $row_add_successs) {
                $content->header('Sinh viên');
                $content->description('Import sinh viên');
                $content->body(view('admin.ImportStudent.import_parse',
                    [
                        'row_error' => $row_error,
                        'error_logs' => $error_logs,
                        'row_add_successs' => $row_add_successs
                    ]
                ));

            });

    }


}