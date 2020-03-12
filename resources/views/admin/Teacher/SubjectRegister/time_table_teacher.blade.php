<?php
use App\Models\SubjectRegister;
use App\Models\Subjects;
use App\Models\TimeStudy;
use Illuminate\Support\Facades\DB;

          $idUser = Admin::user()->id;
          $subjectRegister = SubjectRegister::where('id_user_teacher', $idUser)->orderBy('id_time_register', 'DESC')->first();
          if(!empty($subjectRegister)) {
              $idSubjectRegister = SubjectRegister::where('id_user_teacher', $idUser)->where('id_time_register', $subjectRegister->id_time_register)->pluck('id');
              $timeStudys = TimeStudy::whereIn('id_subject_register', $idSubjectRegister)->get()->toArray();
          } else {
              $subjectRegister = '';
              $timeStudys = [];
          }


          $arrDays = ["Thứ 2", "Thứ 3", "Thứ 4", "Thứ 5", "Thứ 6", "Thứ 7", "Chủ nhật"];
          $arrPeriods =DB::table('time_table')->select('time_start', 'time_end')->get();
          $arrPeriods = collect($arrPeriods)->map(function($x){ return (array) $x; })->toArray();

          ?>
          {{--</div>--}}
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
                                                  if (isset($item[$timeStudy['id_subject_register']]) ) {
                                                      $arrayTable[$key][$pSubKey][$timeStudy['id_subject_register']] = $arrayTable[$key][$pSubKey][$timeStudy['id_subject_register']] + 1;
                                                      $isExisted = true;
                                                  }
                                              }
                                          }
                                          if (!$isExisted) {
                                              $arrayTable[$key][$periodKey][$timeStudy['id_subject_register']] = 1;
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
                                      $subjectRegisterId = array_keys($arrayTable[$dayKey][$periodKey])[0];
                                      $count = array_values($arrayTable[$dayKey][$periodKey])[0];
                                      $idSubject = SubjectRegister::where("id", $subjectRegisterId)->first()->id_subjects;
                                      $nameSubject = Subjects::where('id',$idSubject)->first();
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
?>