{{--@php--}}
    {{--echo $form;--}}
{{--@endphp--}}
{{--<script>--}}
    {{--$.ajax({--}}
        {{--type:'get',--}}
        {{--url:'/user/timetable',--}}
        {{--data:{_token: "{{ csrf_token() }}"--}}
        {{--},--}}
        {{--success: function( msg ) {--}}
            {{--var timeRegister = JSON.parse(msg);--}}

        {{--}--}}
    {{--});--}}

{{--</script>--}}
<?php
use App\Models\ResultRegister;use App\Models\SubjectRegister;use App\Models\Subjects;use App\Models\TimeRegister;use App\Models\TimeStudy;$idUser = Auth::user()->id;
$timeRegister = TimeRegister::where('status', 1)->orderBy('id', 'DESC')->first();
$idTimeRegister = $timeRegister->id;
$idSubjectRegister = ResultRegister::where('id_user_student', $idUser)->where('time_register', $idTimeRegister)->pluck('id_subject_register');
$timeStudys = TimeStudy::whereIn('id_subject_register',$idSubjectRegister)->get()->toArray();

$arrDay = ["Thứ 2", "Thứ 3", "Thứ 4", "Thứ 5", "Thứ 6", "Thứ 7", "Chủ nhật"];
$arrPeriod = [
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
<table >
    <thead   >
    <tr>
        <th></th>
        <?php
        foreach ($arrDay as $item) {
            echo "<th style='text-align: center'>" . $item . "</th>";
        }
        ?>
    </tr>
    </thead>
    <tbody>
    <?php

    foreach ($arrPeriod as $key => $item) {
        $start = strtotime($item["start"]);
        $end = strtotime($item["end"]);
        echo "<tr>";
        echo "<td>Tiết " . ($key + 1) . "</td>";
        $timeStart =  strtotime("9:00");
        $timeEnd = strtotime("11:00");

        foreach ($arrDay as $days => $day) {
        ?>
        <td  style='border-color:Gray;border-width:1px;border-style:solid;height:22px;width:110px;'>
        <?php
            foreach ($timeStudys as $timeStudy) {
                $startTime = strtotime($timeStudy['time_study_start']);
                $endTime = strtotime($timeStudy['time_study_end']);
                if($timeStudy['day'] == ($days + 2)  && $start >= $startTime && $end <= $endTime){
                    $idSubject = SubjectRegister::where('id',$timeStudy['id_subject_register'])->pluck('id_subjects');
                    $nameSubject = Subjects::find($idSubject)->pluck('name')->toArray();
                    echo "<span style='background-color:red'>". $nameSubject['0']."</span>";

                }
            }
//            $rowSpan = 0;<?= ($rowSpan !== false ? "rowSpan='".$rowSpan."'" : "")

            echo "</td>";
        }
        echo "</tr>";
    }
    ?>
    </tbody>
</table>
<br>

<?php
$times = array('00:00','00:30', '01:00', '01:30','02:00','02:30','03:00');
$days = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'); // 0 = Sun ... 6 = Sat


$shows = array();

$shows[] =   array(
    "day"=>0,
    "time_start"=>'00:00',
    "time_end"=>'00:30',
    "name"=>"Show A"
);

$shows[] = array(
    "day"=>1,
    "time_start"=>'00:00',
    "time_end"=>'00:30',
    "name"=>"Show B"
);

$shows[] = array(
    "day"=>3,
    "time_start"=>'00:00',
    "time_end"=>'00:30',
    "name"=>"Show C"
);

$shows[] = array(
    "day"=>4,
    "time_start"=>'00:00',
    "time_end"=>'00:30',
    "name"=>"Show D"
);

$shows[] = array(
    "day"=>5,
    "time_start"=>'00:00',
    "time_end"=>'00:30',
    "name"=>"Show E"
);

$shows[] = array(
    "day"=>6,
    "time_start"=>'00:00',
    "time_end"=>'00:30',
    "name"=>"Show F"
);

$shows[] = array(
    "day"=>1,
    "time_start"=>'00:30',
    "time_end"=>'01:30',
    "name"=>"Show G"
);

$shows[] = array(
    "day"=>2,
    "time_start"=>'00:30',
    "time_end"=>'01:00',
    "name"=>"Show H"
);
$shows[] = array(
    "day"=>3,
    "time_start"=>'00:30',
    "time_end"=>'01:00',
    "name"=>"Show I"
);
$shows[] = array(
    "day"=>4,
    "time_start"=>'00:30',
    "time_end"=>'01:00',
    "name"=>"Show J"
);
$shows[] = array(
    "day"=>1,
    "time_start"=>'01:00',
    "time_end"=>'01:30',
    "name"=>"Show K"
);

$shows[] = array(
    "day"=>2,
    "time_start"=>'01:00',
    "time_end"=>'02:00',
    "name"=>"Show L"
);

$shows[] = array(
    "day"=>1,
    "time_start"=>'01:30',
    "time_end"=>'02:00',
    "name"=>"Show M"
);

$shows[] = array(
    "day"=>2,
    "time_start"=>'01:30',
    "time_end"=>'02:00',
    "name"=>"Show N"
);

$parsedShow = array();

foreach ($shows as  $show) {

    $start_index =  array_search($show['time_start'], $times); // $key = 2;
    $end_index =  array_search($show['time_end'], $times); // $key = 1;
    if($end_index - $start_index > 1){
        //NEED SPAN
        $show['span'] = (($end_index - $start_index));
    }else{
        $show['span'] = false;
    }
    $parsedShow[$show['time_start']][] = $show;
}


?>
<html>
<table border="1">
    <tr>
        <td>
            Time
        </td>
        <?php
        foreach ($days as $day) {
            echo "<td>$day</td>";
        }
        ?>

    </tr>
    <?php
    foreach ($times as $time) {
    ?>
    <tr>
        <?php
        if(!isset($parsedShow[$time])){
            continue;
        }
        echo "<td>$time</td>";

        foreach ($parsedShow[$time] as $show) {
        ?>
        <td <?= ($show['span'] !== false ? "rowSpan='".$show['span']."'" : "")?>><?= $show['name'] ?></td>
        <?php }
        ?>
    </tr>
    <?php }

    ?>
</table>
</html>
<?php
//                    echo "<table cellpadding='0' border='0' cellspacing='0' style='text-align:left;width:90px;cursor:pointer'
//                     class='textTable'><tbody><tr><td width='90px'>
//                        <span style='color:Teal'>". $nameSubject['0']."</span>
//                        </td></tr><tr><td width='90px'>
//                        </td></tr></tbody></table>";
?>