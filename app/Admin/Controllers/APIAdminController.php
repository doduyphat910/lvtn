<?php
namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\ResultRegister;
use App\Models\SubjectRegister;
use App\Models\Subjects;
use App\Models\TimeRegister;
use App\Models\TimeStudy;
use App\Models\UserAdmin;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class APIAdminController extends Controller
{
    protected function gridSubjectRegister(Request $request)
    {
        $idTimeRegister = $request->id;
        return Admin::grid(SubjectRegister::class, function (Grid $grid) use ($idTimeRegister)  {
            $user = Admin::user();
            $idUser = $user->id;
            $grid->model()->where('id_time_register', $idTimeRegister)->where('id_user_teacher', $idUser);
//            $grid->rows(function (Grid\Row $row) {
//                $row->column('number', $row->number);
//            });
//            $grid->number('STT');
            $grid->id('Mã học phần')->display(function ($name) {
                return '<a href="/admin/teacher/subject-register/' . $this->id . '/details">' . $name . '</a>';
            });
            $grid->id_subjects('Môn học')->display(function ($idSubject) {
                if ($idSubject) {
                    $name = Subjects::find($idSubject)->name;
                    return "<span class='label label-info'>{$name}</span>";
                } else {
                    return '';
                }
            });
            $grid->column('Phòng')->display(function () {
                $idClassroom = TimeStudy::where('id_subject_register', $this->id)->pluck('id_classroom')->toArray();
                $classRoom = Classroom::whereIn('id', $idClassroom)->pluck('name')->toArray();
                $classRoom = array_map(function ($classRoom) {
                    return "<span class='label label-success'>{$classRoom}</span>";
                }, $classRoom);
                return join('&nbsp;', $classRoom);
            });
            $grid->column('Buổi học')->display(function () {
                $day = TimeStudy::where('id_subject_register', $this->id)->pluck('day')->toArray();
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
                $timeStart = TimeStudy::where('id_subject_register', $this->id)->pluck('time_study_start')->toArray();
                $timeEnd = TimeStudy::where('id_subject_register', $this->id)->pluck('time_study_end')->toArray();
                $time = array_map(function ($timeStart, $timeEnd) {
                    return "<span class='label label-success'>{$timeStart} - {$timeEnd}</span>";
                }, $timeStart, $timeEnd);
                return join('&nbsp;', $time);
            });
            $grid->id_user_teacher('Giảng viên')->display(function ($id_user_teacher) {
                if ($id_user_teacher) {
                    $teacher = UserAdmin::find($id_user_teacher);
                    if ($teacher) {
                        return $teacher->name;
                    } else {
                        return '';
                    }
                } else {
                    return '';
                }
            });
            $grid->qty_current('Số lượng hiện tại');
    //            $grid->qty_min('Số lượng tối thiểu');
    //            $grid->qty_max('Số lượng tối đa');
            $grid->date_start('Ngày bắt đầu');
            $grid->date_end('Ngày kết thúc');
            $grid->created_at('Tạo vào lúc');
            $grid->updated_at('Cập nhật vào lúc');
            //action
            $grid->actions(function ($actions) {
                $actions->disableEdit();
                $actions->disableDelete();
                $actions->append('<a href="/admin/teacher/subject-register/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableFilter();
        });
    }
    protected function timeTable(Request $request)
    {
        $idTimeRegister = $request->id;
        $idUser = Admin::user()->id;
        $idSubjectRegister = SubjectRegister::where('id_user_teacher', $idUser)->where('id_time_register', $idTimeRegister)->pluck('id');
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


    //infomation student

    public function studentRegister (Request $request){
        $idTimeRegister = $request->idTimeRegister;
        $idUser = $request->idUser;
        return Admin::grid(ResultRegister::class, function (Grid $grid) use($idTimeRegister,$idUser) {
            $grid->model()->where('time_register', $idTimeRegister)->where('id_user_student', $idUser);
            $grid->column('Mã học phần')->display(function () {
                $subjectRegister = SubjectRegister::where('id',$this->id_subject_register)->first();
                if (!empty($subjectRegister)) {
                    return $subjectRegister->id;
                } else {
                    return '';
                }
            });
            $grid->id_subject('Môn học')->display(function () {
                $idSubject = $this->id_subject;
                if (!empty($idSubject)) {
                     if(!empty(Subjects::find($idSubject)->name)){
                         return Subjects::find($idSubject)->name;
                     } else {
                         return '';
                     }
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
            $grid->column('Sô tín chỉ hiện tại')->display(function () use ($idUser, $idTimeRegister){
                $idSubject = ResultRegister::where('id_user_student', $idUser)->where('time_register',  $idTimeRegister)->pluck('id_subject');
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
            $grid->disableActions();
        });
    }

    protected function studentPoint(Request $request)
    {
        $idTimeRegister = $request->idTimeRegister;
        $idUser =  $request->idUser;
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
    protected function gridManagePoint(Request $request)
    {
        $idTimeRegister = $request->id;
        return Admin::grid(SubjectRegister::class, function (Grid $grid) use ($idTimeRegister)  {
            $user = Admin::user();
            $idUser = $user->id;
            $grid->model()->where('id_time_register', $idTimeRegister)->where('id_user_teacher', $idUser);
//            $grid->rows(function (Grid\Row $row) {
//                $row->column('number', $row->number);
//            });
//            $grid->number('STT');
            $grid->id('Mã học phần')->display(function ($name) {
                return '<a href="/admin/teacher/manage-point/' . $this->id . '/details">' . $name . '</a>';
            });
            $grid->id_subjects('Môn học')->display(function ($idSubject) {
                if ($idSubject) {
                    $name = Subjects::find($idSubject)->name;
                    return "<span class='label label-info'>{$name}</span>";
                } else {
                    return '';
                }
            });
            $grid->column('Phòng')->display(function () {
                $idClassroom = TimeStudy::where('id_subject_register', $this->id)->pluck('id_classroom')->toArray();
                $classRoom = Classroom::whereIn('id', $idClassroom)->pluck('name')->toArray();
                $classRoom = array_map(function ($classRoom) {
                    return "<span class='label label-success'>{$classRoom}</span>";
                }, $classRoom);
                return join('&nbsp;', $classRoom);
            });
            $grid->column('Buổi học')->display(function () {
                $day = TimeStudy::where('id_subject_register', $this->id)->pluck('day')->toArray();
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
                $timeStart = TimeStudy::where('id_subject_register', $this->id)->pluck('time_study_start')->toArray();
                $timeEnd = TimeStudy::where('id_subject_register', $this->id)->pluck('time_study_end')->toArray();
                $time = array_map(function ($timeStart, $timeEnd) {
                    return "<span class='label label-success'>{$timeStart} - {$timeEnd}</span>";
                }, $timeStart, $timeEnd);
                return join('&nbsp;', $time);
            });
            $grid->id_user_teacher('Giảng viên')->display(function ($id_user_teacher) {
                if ($id_user_teacher) {
                    $teacher = UserAdmin::find($id_user_teacher);
                    if ($teacher) {
                        return $teacher->name;
                    } else {
                        return '';
                    }
                } else {
                    return '';
                }
            });
            $grid->qty_current('Số lượng hiện tại');
            //            $grid->qty_min('Số lượng tối thiểu');
            //            $grid->qty_max('Số lượng tối đa');
            $grid->date_start('Ngày bắt đầu');
            $grid->date_end('Ngày kết thúc');
            $grid->created_at('Tạo vào lúc');
            $grid->updated_at('Cập nhật vào lúc');
            //action
            $grid->actions(function ($actions) {
                $actions->disableEdit();
                $actions->disableDelete();
                $actions->append('<a href="/admin/teacher/manage-point/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableFilter();
        });
    }
    public function resultPoint(Request $request){
        $script = <<<EOT
        $(function () {
            $('.grid-refresh').hide();
        });
EOT;
//        Admin::script($script);
        $idTimeRegister = $request->id;
        $idUser = $request->idUser;
        return Admin::grid(ResultRegister::class, function (Grid $grid)  use($idTimeRegister, $idUser) {
            // $timeRegister = TimeRegister::orderBy('id', 'DESC')->first();
            // $timeRegister = TimeRegister::where('id', $idTimeRegister)->first();

            $grid->model()->where('id_user_student', $idUser)->where('time_register', $idTimeRegister);

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
            $grid->column('Sô tín chỉ hiện tại')->display(function () use ($idUser, $idTimeRegister){
                $idSubject = ResultRegister::where('id_user_student', $idUser)->where('time_register', $idTimeRegister)->pluck('id_subject');
                $subjects = Subjects::find($idSubject);
                $sumCredit = 0;
                foreach ($subjects as $subject){
                    $sumCredit+=$subject->credits;
                }
                return $sumCredit;

            });
            $grid->column('Điểm TK ALL')->display(function ()use ($idUser, $idTimeRegister){
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
}

