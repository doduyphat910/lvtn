<?php
use App\Models\ResultRegister;
use App\Models\SubjectRegister;
use App\Models\Subjects;
use App\Models\TimeRegister;
use App\Models\TimeStudy;
use App\Models\TimeTable;
$idUser = Auth::user()->id;
$time = ResultRegister::where('id_user_student', $idUser)->orderBy('time_register', 'DESC')->first();
if(!empty($time)) {
//    $timeRegister = TimeRegister::where('id', $time->time_register)->first();
    $timeRegister = TimeRegister::where('status',1)->orderBy('id', 'DESC')->first();
    if(empty($timeRegister)){
        //$timeRegister = TimeRegister::orderBy('id', 'DESC')->first();
        $timeRegisterOfStudent = ResultRegister::where('id_user_student', $idUser)->orderBy('time_register', 'DESC')->first();
        $timeRegister = TimeRegister::where('id',$timeRegisterOfStudent->time_register)->first();
    }
    $idTimeRegister = $timeRegister->id;
    $idSubjectRegister = ResultRegister::where('id_user_student', $idUser)->where('time_register', $idTimeRegister)->pluck('id_subject_register');
    $timeStudys = TimeStudy::whereIn('id_subject_register', $idSubjectRegister)->get()->toArray();
} else {
    $timeRegister = [];
    $idTimeRegister = [];
    $idSubjectRegister = [];
    $timeStudys = [];
}


$arrDays = ["Thứ 2", "Thứ 3", "Thứ 4", "Thứ 5", "Thứ 6", "Thứ 7", "Chủ nhật"];
//$arrPeriods = [
//    ["start" => '6:30', 'end' => '7:15'],
//    ["start" => '7:20', 'end' => '8:05'],
//    ["start" => '08:15', 'end' => '09:00'],
//    ["start" => '09:05', 'end' => '09:50'],
//    ["start" => '10:00', 'end' => '10:45'],
//    ["start" => '10:50', 'end' => '11:35'],
//    ["start" => '12:30', 'end' => '13:15'],
//    ["start" => '13:20', 'end' => '14:05'],
//    ["start" => '14:15', 'end' => '15:00'],
//    ["start" => '15:05', 'end' => '15:50'],
//    ["start" => '16:00', 'end' => '16:45'],
//    ["start" => '16:50', 'end' => '17:35'],
//    ["start" => '17:40', 'end' => '18:25'],
//    ["start" => '18:25', 'end' => '19:10'],
//    ["start" => '19:15', 'end' => '20:00']
//
//];
$arrPeriods =DB::table('time_table')->select('time_start', 'time_end')->get();
$arrPeriods = collect($arrPeriods)->map(function($x){ return (array) $x; })->toArray();


?>
{{-- <style type="text/css">
    
</style> --}}
<div class="row">
    <div class="col-sm-8">
        <h1 class="text-center indam" >Thời Khóa Biểu</h1>
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
                        echo "<td rowspan='$count' style='background-color:#ecf0f1;border-color:Gray;border-width:1px;border-style:solid;height:22px;width:110px;color:Teal;text-align:center;font-weight: bold;font-family: Times New Roman;' class='data' value='$subjectId' '>$nameSubject->name</td>";
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
         <h1 class="text-center note">Ghi Chú</h1>
        <ul class="list-unstyled text-center">
            <?php
                $i=0;
                foreach ($arrPeriods as $key => $valuePriods ){
                    //$valuePriods['time_start']." ".$valuePriods['time_end'];
            ?>
            <li class="notetime"> <?php echo "Tiết ". ($key + 1) . ": " .$valuePriods['time_start']." - ".$valuePriods['time_end'];?></li>
            <?php
                }
            ?>

        </ul>
    </div>
</div>
<style type="text/css">
    th.th-object.th-object, td.td-object:first-child, td.td-object:last-child{
        text-align: center;
        background-color: #3c8dbc;
        color: #fff;
    }
    th.th-object.th-object,.table-bordered>tbody>tr>td.td-object{
        border: 1px solid #000;
        border-top: 1px solid #000 !important;
        width: 200px;
    }
    
</style>
{{-- <script type="text/javascript">
    $(".data").hover(function () {
     alert($(this).attr('value'));
});
</script> --}}
