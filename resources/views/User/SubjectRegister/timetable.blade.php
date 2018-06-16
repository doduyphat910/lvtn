<?php
$arrDay = ["Thứ 2", "Thứ 2", "Thứ 2", "Thứ 2", "Thứ 2", "Thứ 2", "Thứ 2", "Thứ 2"];
$arrTietHoc = [
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
<table>
    <thead>
    <tr>
        <th></th>
        <?php
        foreach ($arrDay as $item) {
            echo "<th>" . $item . "</th>";
        }
        ?>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($arrTietHoc as $key => $item) {
        $start = strtotime($item["start"]);
        $end = strtotime($item["end"]);
        echo "<tr>";
        echo "<td>Tiết " . ($key + 1) . "</td>";
        foreach ($arrDay as $day => $ngay) {
            echo "<td>";
            foreach ($monhoc as $mon) {
                $start_time = strtotime($mon["start_time"]);
                $end_time = strtotime($mon["end_time"]);
//                var_dump([$start, $end, $start_time, $end_time]);
                if($mon["day"] == $day && $start >= $start_time && $end <= $end_time){
                    echo "<span style='color:red'>". $mon["name"]."</span>";
                }
            }
            echo "</td>";
        }
        echo "</tr>";
    }
    ?>
    </tbody>
</table>
