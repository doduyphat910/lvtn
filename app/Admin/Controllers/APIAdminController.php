<?php
namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
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
            $grid->rows(function (Grid\Row $row) {
                $row->column('number', $row->number);
            });
            $grid->number('STT');
            $grid->id('Mã học phần')->display(function ($name) {
                return '<a href="/admin/teacher/subject-register/' . $this->id . '/details">' . $name . '</a>';
            })->sortable();
            $grid->id_subjects('Môn học')->display(function ($idSubject) {
                if ($idSubject) {
                    $name = Subjects::find($idSubject)->name;
                    return "<span class='label label-info'>{$name}</span>";
                } else {
                    return '';
                }
            })->sortable();
            $grid->column('Phòng')->display(function () {
                $idClassroom = TimeStudy::where('id_subject_register', $this->id)->pluck('id_classroom')->toArray();
                $classRoom = Classroom::whereIn('id', $idClassroom)->pluck('name')->toArray();
                $classRoom = array_map(function ($classRoom) {
                    return "<span class='label label-success'>{$classRoom}</span>";
                }, $classRoom);
                return join('&nbsp;', $classRoom);
            })->sortable();
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
            })->sortable();
            $grid->column('Thời gian học')->display(function () {
                $timeStart = TimeStudy::where('id_subject_register', $this->id)->pluck('time_study_start')->toArray();
                $timeEnd = TimeStudy::where('id_subject_register', $this->id)->pluck('time_study_end')->toArray();
                $time = array_map(function ($timeStart, $timeEnd) {
                    return "<span class='label label-success'>{$timeStart} - {$timeEnd}</span>";
                }, $timeStart, $timeEnd);
                return join('&nbsp;', $time);
            })->sortable();
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
            })->sortable();
            $grid->qty_current('Số lượng hiện tại')->sortable();
    //            $grid->qty_min('Số lượng tối thiểu');
    //            $grid->qty_max('Số lượng tối đa');
            $grid->date_start('Ngày bắt đầu')->sortable();
            $grid->date_end('Ngày kết thúc')->sortable();
            $grid->created_at('Tạo vào lúc')->sortable();
            $grid->updated_at('Cập nhật vào lúc')->sortable();
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
}

