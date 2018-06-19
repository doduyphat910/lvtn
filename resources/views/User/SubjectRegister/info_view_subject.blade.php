<?php
use App\Models\ResultRegister;use App\Models\SubjectRegister;use App\Models\Subjects;use App\Models\TimeRegister;use App\Models\TimeStudy;$idUser = Auth::user()->id;
$timeRegister = TimeRegister::where('status', 1)->orderBy('id', 'DESC')->first();
$idTimeRegister = $timeRegister->id;
$idSubjectRegister = ResultRegister::where('id_user_student', $idUser)->where('time_register', $idTimeRegister)->pluck('id_subject_register');
$timeStudys = TimeStudy::whereIn('id_subject_register', $idSubjectRegister)->get()->toArray();

$arrDays = ["Thứ 2", "Thứ 3", "Thứ 4", "Thứ 5", "Thứ 6", "Thứ 7", "Chủ nhật"];
$arrPeriods = [
    ["start" => '6:30', 'end' => '7:15'],
    ["start" => '7:20', 'end' => '8:05'],
    ["start" => '08:15', 'end' => '09:00'],
    ["start" => '09:05', 'end' => '09:50'],
    ["start" => '10:00', 'end' => '10:45'],
    ["start" => '10:50', 'end' => '11:35'],
    ["start" => '12:30', 'end' => '13:15'],
    ["start" => '13:20', 'end' => '14:05'],
    ["start" => '14:15', 'end' => '15:00'],
    ["start" => '15:05', 'end' => '15:50'],
    ["start" => '16:00', 'end' => '16:45'],
    ["start" => '16:50', 'end' => '17:35'],
    ["start" => '17:40', 'end' => '18:25'],
    ["start" => '18:25', 'end' => '19:10'],
    ["start" => '19:15', 'end' => '20:00'],
];
$monhoc = [
    [
        "name" => "toan",
        "day" => 5,
        "start_time" => "09:00",
        "end_time" => "11:00",
    ]
]

?>
<h1>TKB</h1>
<table>
    <thead>
    <tr>
        <th></th>
        <?php
        foreach ($arrDays as $item) {
            echo "<th style='text-align: center'>" . $item . "</th>";
        }
        ?>
    </tr>
    </thead>
    <tbody>
    <?php
    $arrayTable = [];
    foreach ($arrPeriods as $periodKey => $arrPeriod) {
        $start = strtotime($arrPeriod["start"]);
        $end = strtotime($arrPeriod["end"]);
        foreach ($arrDays as $key => $day) {
            foreach ($timeStudys as $timeStudy) {
                $startTime = strtotime($timeStudy['time_study_start']);
                $endTime = strtotime($timeStudy['time_study_end']);
                if ($timeStudy['day'] == ($key + 2) && $start >= $startTime && $end <= $endTime) {
                    $idSubject = SubjectRegister::where('id', $timeStudy['id_subject_register'])->first();
                    if (!empty($idSubject)) {
                        $idSubject = $idSubject->id_subjects;
                        $isExisted = false;
                        foreach ($arrayTable[$key] as $pSubKey => $item) {
                            if (isset($item[$idSubject])) {
                                $arrayTable[$key][$pSubKey][$idSubject] = $arrayTable[$key][$pSubKey][$idSubject] + 1;
                                $isExisted = true;
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
        echo "<td>Tiết " . ($periodKey + 1) . "</td>";

        foreach ($arrDays as $dayKey => $day) {
            if(isset($arrayTable[$dayKey][$periodKey])) {
                if ($arrayTable[$dayKey][$periodKey] && count($arrayTable[$dayKey][$periodKey]) > 0) {
                        $count = 1;
                        $subjectId = array_keys($arrayTable[$dayKey][$periodKey])[0];
                        $count = array_values($arrayTable[$dayKey][$periodKey])[0];
                        $nameSubject = Subjects::where("id", $subjectId)->first();
                        echo "<td rowspan='$count' style='background-color:red;border-color:Gray;border-width:1px;border-style:solid;height:22px;width:110px;'>$nameSubject->name</td>";
                    } else if(is_array($arrayTable[$dayKey][$periodKey])){// nếu như là array thì render
                    echo "<td rowspan='1' style='border-color:Gray;border-width:1px;border-style:solid;height:22px;width:110px;'></td>";
                }
            } else {
                echo "<td rowspan='1' style='border-color:Gray;border-width:1px;border-style:solid;height:22px;width:110px;'></td>";
            }
        }
        echo "</tr>";
    }
    ?>
    </tbody>
</table>

