  <head>
    <script src="{{ admin_asset("/vendor/laravel-admin/chartjs/chart.js")}}"></script>
      {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>--}}
  </head>
<style>
   .title {
   font-size: 50px;
   color: #636b6f;
   font-family: 'Raleway', sans-serif;
   font-weight: 100;
   display: block;
   text-align: center;
   margin: 0px 0 10px 0px;
   }
   .links {
   text-align: center;
   margin-bottom: 20px;
   }
   .links > a {
   color: #636b6f;
   padding: 0 25px;
   font-size: 12px;
   font-weight: 600;
   letter-spacing: .1rem;
   text-decoration: none;
   text-transform: uppercase;
   }
   a,
   a:hover,
   a:active,
   a:focus {
   color: #34495e;
   }
   .circle-tile-heading .fa {
   line-height: 80px;
   }
   .circle-tile-description {
   text-transform: uppercase;
   }
   .circle-tile-heading {
   position: relative;
   width: 80px;
   height: 80px;
   margin: 0 auto -40px;
   border: 3px solid rgba(255,255,255,0.3);
   border-radius: 100%;
   color: #fff;
   transition: all ease-in-out .3s;
   }
   .circle-tile {
   margin-bottom: 15px;
   text-align: center;
   }
   .circle-tile-content {
   padding-top: 50px;
   }
   .text-faded {
   color: rgba(255,255,255,0.7);
   }
   .circle-tile-footer:hover {
   text-decoration: none;
   color: rgba(255,255,255,0.5);
   background-color: rgba(0,0,0,0.2);
   }
   .circle-tile-number {
   padding: 5px 0 15px;
   font-size: 26px;
   font-weight: 700;
   line-height: 1;
   }
   .circle-tile-heading {
   position: relative;
   width: 80px;
   height: 80px;
   margin: 0 auto -40px;
   border: 3px solid rgba(255,255,255,0.3);
   border-radius: 100%;
   color: #fff;
   transition: all ease-in-out .3s;
   }
   .circle-tile-footer {
   display: block;
   padding: 5px;
   color: rgba(255,255,255,0.5);
   background-color: rgba(0,0,0,0.1);
   transition: all ease-in-out .3s;
   }
   @media(min-width:768px) {
   .tile {
   margin-bottom: 30px;
   }
   .circle-tile {
   margin-bottom: 30px;
   }
   }
   /*icon user*/
   .dark-blue {
   background-color: #34495e;
   }
   /*icon menoy*/
   .green {
   background-color: #16a085;
   }
   .blue {
   background-color: #2980b9;
   }
   .orange {
   background-color: #f39c12;
   }
   .red {
   background-color: #e74c3c;
   }
   .purple {
   background-color: #8e44ad;
   }
   /*đồng hồ*/


a, a:visited {
  outline:none;
  color:#389dc1;
}

a:hover{
  text-decoration:none;
}

section, footer, header, aside{
  display: block;
}


/*-------------------------
  The clocks
--------------------------*/


#clock{
  width:325px;
  padding:40px;
  margin:0px auto 20px;
  position:relative;
}

#clock:after{
  content:'';
  position:absolute;
  width:400px;
  height:20px;
  border-radius:100%;
  left:50%;
  margin-left:-200px;
  bottom:2px;
  z-index:-1;
}


#clock .display{
  text-align:center;
  padding: 30px 0px 0px;
  border-radius:6px;
  /*position:relative;*/
  height: 120px;
}


/*-------------------------
  Light color theme
--------------------------*/


#clock.light{
  background-color:#f3f3f3;
  color:#272e38;
}

#clock.light:after{
  box-shadow:0 4px 10px rgba(0,0,0,0.15);
}

#clock.light .digits div span{
  background-color:#272e38;
  border-color:#272e38; 
}

#clock.light .digits div.dots:before,
#clock.light .digits div.dots:after{
  background-color:#272e38;
}
/*
#clock.light .alarm{
  background:url('../img/alarm_light.jpg');
}*/

#clock.light .display{
  background-color:#dddddd;
  box-shadow:0 1px 1px rgba(0,0,0,0.08) inset, 0 1px 1px #fafafa;
}


/*-------------------------
  Dark color theme
--------------------------*/


#clock.dark{
  background-color:#272e38;
  color:#cacaca;
}

#clock.dark:after{
  box-shadow:0 4px 10px rgba(0,0,0,0.3);
}

#clock.dark .digits div span{
  background-color:#cacaca;
  border-color:#cacaca; 
}

/*#clock.dark .alarm{
  background:url('../img/alarm_dark.jpg');
}
*/
#clock.dark .display{
  background-color:#0f1620;
  box-shadow:0 1px 1px rgba(0,0,0,0.08) inset, 0 1px 1px #2d3642;
}

#clock.dark .digits div.dots:before,
#clock.dark .digits div.dots:after{
  background-color:#cacaca;
}


/*-------------------------
  The Digits
--------------------------*/


#clock .digits div{
  text-align:left;
  position:relative;
  width: 28px;
  height:50px;
  display:inline-block;
  margin:0 4px;
}

#clock .digits div span{
  opacity:0;
  position:absolute;

  -webkit-transition:0.25s;
  -moz-transition:0.25s;
  transition:0.25s;
}

#clock .digits div span:before,
#clock .digits div span:after{
  content:'';
  position:absolute;
  width:0;
  height:0;
  border:5px solid transparent;
}

#clock .digits .d1{     height:5px;width:16px;top:0;left:6px;}
#clock .digits .d1:before{  border-width:0 5px 5px 0;border-right-color:inherit;left:-5px;}
#clock .digits .d1:after{ border-width:0 0 5px 5px;border-left-color:inherit;right:-5px;}

#clock .digits .d2{     height:5px;width:16px;top:24px;left:6px;}
#clock .digits .d2:before{  border-width:3px 4px 2px;border-right-color:inherit;left:-8px;}
#clock .digits .d2:after{ border-width:3px 4px 2px;border-left-color:inherit;right:-8px;}

#clock .digits .d3{     height:5px;width:16px;top:48px;left:6px;}
#clock .digits .d3:before{  border-width:5px 5px 0 0;border-right-color:inherit;left:-5px;}
#clock .digits .d3:after{ border-width:5px 0 0 5px;border-left-color:inherit;right:-5px;}

#clock .digits .d4{     width:5px;height:14px;top:7px;left:0;}
#clock .digits .d4:before{  border-width:0 5px 5px 0;border-bottom-color:inherit;top:-5px;}
#clock .digits .d4:after{ border-width:0 0 5px 5px;border-left-color:inherit;bottom:-5px;}

#clock .digits .d5{     width:5px;height:14px;top:7px;right:0;}
#clock .digits .d5:before{  border-width:0 0 5px 5px;border-bottom-color:inherit;top:-5px;}
#clock .digits .d5:after{ border-width:5px 0 0 5px;border-top-color:inherit;bottom:-5px;}

#clock .digits .d6{     width:5px;height:14px;top:32px;left:0;}
#clock .digits .d6:before{  border-width:0 5px 5px 0;border-bottom-color:inherit;top:-5px;}
#clock .digits .d6:after{ border-width:0 0 5px 5px;border-left-color:inherit;bottom:-5px;}

#clock .digits .d7{     width:5px;height:14px;top:32px;right:0;}
#clock .digits .d7:before{  border-width:0 0 5px 5px;border-bottom-color:inherit;top:-5px;}
#clock .digits .d7:after{ border-width:5px 0 0 5px;border-top-color:inherit;bottom:-5px;}


/* 1 */

#clock .digits div.one .d5,
#clock .digits div.one .d7{
  opacity:1;
}

/* 2 */

#clock .digits div.two .d1,
#clock .digits div.two .d5,
#clock .digits div.two .d2,
#clock .digits div.two .d6,
#clock .digits div.two .d3{
  opacity:1;
}

/* 3 */

#clock .digits div.three .d1,
#clock .digits div.three .d5,
#clock .digits div.three .d2,
#clock .digits div.three .d7,
#clock .digits div.three .d3{
  opacity:1;
}

/* 4 */

#clock .digits div.four .d5,
#clock .digits div.four .d2,
#clock .digits div.four .d4,
#clock .digits div.four .d7{
  opacity:1;
}

/* 5 */

#clock .digits div.five .d1,
#clock .digits div.five .d2,
#clock .digits div.five .d4,
#clock .digits div.five .d3,
#clock .digits div.five .d7{
  opacity:1;
}

/* 6 */

#clock .digits div.six .d1,
#clock .digits div.six .d2,
#clock .digits div.six .d4,
#clock .digits div.six .d3,
#clock .digits div.six .d6,
#clock .digits div.six .d7{
  opacity:1;
}


/* 7 */

#clock .digits div.seven .d1,
#clock .digits div.seven .d5,
#clock .digits div.seven .d7{
  opacity:1;
}

/* 8 */

#clock .digits div.eight .d1,
#clock .digits div.eight .d2,
#clock .digits div.eight .d3,
#clock .digits div.eight .d4,
#clock .digits div.eight .d5,
#clock .digits div.eight .d6,
#clock .digits div.eight .d7{
  opacity:1;
}

/* 9 */

#clock .digits div.nine .d1,
#clock .digits div.nine .d2,
#clock .digits div.nine .d3,
#clock .digits div.nine .d4,
#clock .digits div.nine .d5,
#clock .digits div.nine .d7{
  opacity:1;
}

/* 0 */

#clock .digits div.zero .d1,
#clock .digits div.zero .d3,
#clock .digits div.zero .d4,
#clock .digits div.zero .d5,
#clock .digits div.zero .d6,
#clock .digits div.zero .d7{
  opacity:1;
}


/* The dots */

#clock .digits div.dots{
  width:5px;
}

#clock .digits div.dots:before,
#clock .digits div.dots:after{
  width:5px;
  height:5px;
  content:'';
  position:absolute;
  left:0;
  top:14px;
}

#clock .digits div.dots:after{
  top:34px;
}


/*-------------------------
  The Alarm
--------------------------*/


#clock .alarm{
  width:16px;
  height:16px;
  bottom:20px;
  background:url('../img/alarm_light.jpg');
  position:absolute;
  opacity:0.2;
}

#clock .alarm.active{
  opacity:1;
}


/*-------------------------
  Weekdays
--------------------------*/


#clock .weekdays{
  font-size:12px;
  position:absolute;
  width:100%;
  top:10px;
  left:0;
  text-align:center;
}


#clock .weekdays span{
  opacity:0.2;
  padding:0 10px;
}

#clock .weekdays span.active{
  opacity:1;
}


/*-------------------------
    AM/PM
--------------------------*/


#clock .ampm{
  position:absolute;
  bottom:115px;
  right:20px;
  font-size:12px;
}


/*-------------------------
    Button
--------------------------*/


.button-holder{
  text-align:center;
}

a.button{
  background-color:rgba(54, 162, 235, 0.2);
  
  background-image:-webkit-linear-gradient(top, rgba(54, 162, 235, 0.2), rgba(54, 162, 235, 0.2));
  background-image:-moz-linear-gradient(top, rgba(54, 162, 235, 0.2), rgba(54, 162, 235, 0.2));
  background-image:linear-gradient(top, rgba(54, 162, 235, 0.2), rgba(54, 162, 235, 0.2));

  border:1px solid rgba(54, 162, 235, 0.2);
  border-radius:2px;

  box-shadow:0 2px 2px #ccc;

  color:#fff;
  text-decoration: none !important;
  padding:15px 20px;
  display:inline-block;
  cursor:pointer;
}

a.button:hover{
  opacity:0.9;
}

/*Lịch*/
html {
      box-sizing: border-box;
      font-size: 10px;
    }

    body {
      font-family: 'Raleway', sans-serif";
      color: #333;
      
      background-color: #FAFAFA;
      -webkit-font-smoothing: antialiased;
    }

    .container1 {
      width: 96%;
      /*margin: 1.6rem auto;*/
      max-width: 42rem;
      text-align: center;
    }

    .demo-picked {
      font-size: 1.2rem;
      text-align: center;
    }

    .demo-picked span {
      font-weight: bold;
    }
    #v-cal *,#v-cal :after,#v-cal :before {
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
}

#v-cal {
  background-color: #fff;
  border-radius: 0;
  border: 1px solid #e7e9ed;
  -webkit-box-shadow: 0 4px 22px 0 rgba(0,0,0,.05);
  box-shadow: 0 4px 22px 0 rgba(0,0,0,.05);
  margin: 0 auto;
  overflow: hidden;
  width: 100%;
}

#v-cal .vcal-btn {
  -moz-user-select: none;
  -ms-user-select: none;
  -webkit-appearance: button;
  background: none;
  border: 0;
  color: inherit;
  cursor: pointer;
  font: inherit;
  line-height: normal;
  min-width: 27px;
  outline: none;
  overflow: visible;
  padding: 0;
  text-align: center;
}

#v-cal .vcal-btn:active {
  border-radius: 0;
  -webkit-box-shadow: 0 0 0 2px rgba(16,152,158,.1);
  box-shadow: 0 0 0 2px rgba(16,152,158,.1);
}

#v-cal .vcal-header {
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  padding: 19.2px 22.4px;
  padding: 1.2rem 1.4rem;
}

#v-cal .vcal-header svg {
  fill: #10989e;
}

#v-cal .vcal-header__label {
  font-weight: 700;
  text-align: center;
  width: 100%;
}

#v-cal .vcal-week {
  background-color: #e7e9ed;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -ms-flex-wrap: wrap;
  flex-wrap: wrap;
}

#v-cal .vcal-week span {
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  -ms-flex-direction: column;
  flex-direction: column;
  -webkit-box-flex: 0;
  -ms-flex: 0 0 14.28%;
  flex: 0 0 14.28%;
  font-size: 19.2px;
  font-size: 1.2rem;
  font-weight: 700;
  max-width: 14.28%;
  padding: 19.2px 22.4px;
  padding: 1.2rem 1.4rem;
  text-align: center;
  text-transform: uppercase;
}

#v-cal .vcal-body {
  background-color: rgba(231,233,237,.3);
  -ms-flex-wrap: wrap;
  flex-wrap: wrap;
}

#v-cal .vcal-body,#v-cal .vcal-date {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
}

#v-cal .vcal-date {
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  background-color: #fff;
  border-radius: 0;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  -ms-flex-direction: column;
  flex-direction: column;
  -webkit-box-flex: 0;
  -ms-flex: 0 0 14.28%;
  flex: 0 0 14.28%;
  max-width: 14.28%;
  padding: 19.2px 0;
  padding: 1.2rem 0;
}

#v-cal .vcal-date--active {
  cursor: pointer;
}

#v-cal .vcal-date--today {
  background-color: #10989e;
  color: #fff;
}

#v-cal .vcal-date--selected {
  background-color: #e7e9ed;
  color: #333;
}

#v-cal .vcal-date--disabled {
  border-radius: 0;
  cursor: not-allowed;
  opacity: .5;
}
</style>
<div class="title">
   <b>QUẢN LÝ ĐĂNG KÝ MÔN HỌC VÀ ĐIỂM</b>
</div>
<div class="container-fluid box box-default">
   <div class="box-header with-border">
      <h3 class="box-title">Thống kê</h3>
      <div class="box-tools pull-right">
         <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
         </button>
         <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
      </div>
   </div>
   <div class="row box-body">
      <div class="col-lg-2 col-sm-6 col-md-6">
         <div class="circle-tile">
            <a href="/admin/student_user" target="_blank">
               <div class="circle-tile-heading dark-blue">
                  <i class="fa fa-users fa-fw fa-3x"></i>
               </div>
            </a>
            <div class="circle-tile-content dark-blue">
               <div class="circle-tile-description text-faded">
                  Users SV
               </div>
               <div class="circle-tile-number text-faded">
                  {{--265--}}
                   {{$countUserStudent}}
                  <i class="fa fa-users" aria-hidden="true"></i>
               </div>
               <a href="/admin/student_user" target="_blank" class="circle-tile-footer">Xem chi tiết <i class="fa fa-chevron-circle-right"></i></a>
            </div>
         </div>
      </div>
      <div class="col-lg-2 col-sm-6 col-md-6">
         <div class="circle-tile">
            <a  href="/admin/teacher_user" target="_blank" >
               <div class="circle-tile-heading green">
                  <i class="fa fa-users fa-fw fa-3x"></i>
               </div>
            </a>
            <div class="circle-tile-content green">
               <div class="circle-tile-description text-faded">
                   Users GV
               </div>
               <div class="circle-tile-number text-faded">
                   {{$countTeacher}}
                  <i class="fa fa-users" aria-hidden="true"></i>
               </div>
               <a href="/admin/teacher_user" target="_blank" class="circle-tile-footer">Xem chi tiết <i class="fa fa-chevron-circle-right"></i></a>
            </div>
         </div>
      </div>
      <div class="col-lg-2 col-sm-6 col-md-6">
         <div class="circle-tile">
            <a href="/admin/user_admin" target="_blank" >
               <div class="circle-tile-heading orange">
                  <i class="fa fa-users fa-fw fa-3x"></i>
               </div>
            </a>
            <div class="circle-tile-content orange">
               <div class="circle-tile-description text-faded">
                  User QT
               </div>
               <div class="circle-tile-number text-faded">
                  {{$countAdmin}}
                  <i class="fa fa-users" aria-hidden="true"></i>
               </div>
               <a href="/admin/user_admin" target="_blank" class="circle-tile-footer">Xem chi tiết <i class="fa fa-chevron-circle-right"></i></a>
            </div>
         </div>
      </div>
      <div class="col-lg-2 col-sm-6 col-md-6">
         <div class="circle-tile">
            <a href="/admin/class" target="_blank">
               <div class="circle-tile-heading blue">
                  <i class="fa fa-book fa-fw fa-3x"></i>
               </div>
            </a>
            <div class="circle-tile-content blue">
               <div class="circle-tile-description text-faded">
                  Lớp
               </div>
               <div class="circle-tile-number text-faded">
                  {{$countClass}}
                  <i class="fa fa-book" aria-hidden="true"></i>
               </div>
               <a href="/admin/class" target="_blank" class="circle-tile-footer">Xem chi tiết <i class="fa fa-chevron-circle-right"></i></a>
            </div>
         </div>
      </div>
      <div class="col-lg-2 col-sm-6 col-md-6">
         <div class="circle-tile">
            <a href="/admin/time-register" target="_blank">
               <div class="circle-tile-heading red">
                  <i class="fa fa-pencil-square-o fa-fw fa-3x"></i>
               </div>
            </a>
            <div class="circle-tile-content red">
               <div class="circle-tile-description text-faded">
                  Đợt ĐK
               </div>
               <div class="circle-tile-number text-faded">
                  {{$countTimeRegister}}
                  <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
               </div>
               <a href="/admin/time-register" target="_blank" class="circle-tile-footer">Xem chi tiết <i class="fa fa-chevron-circle-right"></i></a>
            </div>
         </div>
      </div>
      <div class="col-lg-2 col-sm-6 col-md-6">
         <div class="circle-tile">
            <a href="/admin/subject_register" target="_blank">
               <div class="circle-tile-heading purple">
                  <i class="fa fa-bookmark fa-fw fa-3x"></i>
               </div>
            </a>
            <div class="circle-tile-content purple">
               <div class="circle-tile-description text-faded">
                  Lớp HP
               </div>
               <div class="circle-tile-number text-faded">
                  {{$countSubjectRegister}}
                  <i class="fa fa-bookmark" aria-hidden="true"></i>
               </div>
               <a href="/admin/subject_register" target="_blank" class="circle-tile-footer">Xem chi tiết <i class="fa fa-chevron-circle-right"></i></a>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="container-fluid box box-default">
   <div class="box-header with-border">
      <h3 class="box-title">Biểu đồ</h3>
      <div class="box-tools pull-right">
         <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
         </button>
         <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
      </div>
   </div>
   <div class="row box-body">
        <div class="col-lg-6 col-sm-6 col-md-6">
              <canvas id="myChart"></canvas><br>
            <div style="text-align: center; font-weight: bold; font-size: large">Biểu đồ số lượng sinh viên</div>
        </div>
         <div class="col-lg-6 col-sm-6 col-md-6">
              <canvas id="myLineChart"></canvas><br>
             <div style="text-align: center; font-weight: bold; font-size: large">Biểu đồ số lượt đăng ký</div>
         </div>
   </div>
</div>
<div class="container-fluid box box-default">
   <div class="box-header with-border">
      <h3 class="box-title">Tiện ích</h3>
      <div class="box-tools pull-right">
         <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
         </button>
         <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
      </div>
   </div>
   <div class="row box-body">
        <div class="col-lg-4 col-sm-3 col-md-3">
                
  <div id="clock" class="light">
      <div class="display">
        <div class="weekdays"></div>
        <div class="ampm"></div>
        <div class="alarm"></div>
        <div class="digits"></div>
      </div>
    </div>

    <div class="button-holder">
      <a class="button">Đổi màu</a>
    </div>
        </div>
         <div class="col-lg-4 col-sm-3 col-md-3">
                <div class="container1">
    <div id="v-cal">
      <div class="vcal-header">
        <button class="vcal-btn" data-calendar-toggle="previous">
          <svg height="24" version="1.1" viewbox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
            <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"></path>
          </svg>
        </button>

        <div class="vcal-header__label" data-calendar-label="month">
          March 2017
        </div>


        <button class="vcal-btn" data-calendar-toggle="next">
          <svg height="24" version="1.1" viewbox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
            <path d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z"></path>
          </svg>
        </button>
      </div>
      <div class="vcal-week">
        <span>Mon</span>
        <span>Tue</span>
        <span>Wed</span>
        <span>Thu</span>
        <span>Fri</span>
        <span>Sat</span>
        <span>Sun</span>
      </div>
      <div class="vcal-body" data-calendar-area="month"></div>
    </div>
  </div>
        </div>
        <div class="col-lg-4 col-sm-3 col-md-3">
         <a href="https://www.accuweather.com/vi/vn/ho-chi-minh-city/353981/weather-forecast/353981" class="aw-widget-legal">
<!--
By accessing and/or using this code snippet, you agree to AccuWeather’s terms and conditions (in English) which can be found at https://www.accuweather.com/en/free-weather-widgets/terms and AccuWeather’s Privacy Statement (in English) which can be found at https://www.accuweather.com/en/privacy.
-->
</a><div id="awcc1530794233439" class="aw-widget-current"  data-locationkey="353981" data-unit="c" data-language="vi" data-useip="false" data-uid="awcc1530794233439"></div><script type="text/javascript" src="https://oap.accuweather.com/launch.js"></script>
        </div>
   </div>
</div>
<script>
var ctx = document.getElementById("myChart").getContext('2d');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        // labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
        labels: {{$arrClass}} ,
        datasets: [{
            label: 'Số lượng',
            // data: [12, 19, 3, 5, 2, 3],
            data: {{$countStudent}},
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
</script>
<script type="text/javascript">
    var ctx = document.getElementById("myLineChart").getContext('2d');
    var myLineChart = new Chart(ctx, {
    type: 'line',
    data: {
        // labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
        labels: <?php echo $timeRegisters; ?>,
        datasets: [{
            label: 'Lượt đăng ký',
            // data: [12, 19, 3, 5, 2, 3],
            data: <?php echo $dataTimeRegister; ?>,
            backgroundColor: [
                
                'rgba(54, 162, 235, 0.2)'
            ],
            borderColor: [
                
                'rgba(54, 162, 235, 1)'
                
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
   $(function(){

    // Cache some selectors

    var clock = $('#clock'),
        alarm = clock.find('.alarm'),
        ampm = clock.find('.ampm');

    // Map digits to their names (this will be an array)
    var digit_to_name = 'zero one two three four five six seven eight nine'.split(' ');

    // This object will hold the digit elements
    var digits = {};

    // Positions for the hours, minutes, and seconds
    var positions = [
        'h1', 'h2', ':', 'm1', 'm2', ':', 's1', 's2'
    ];

    // Generate the digits with the needed markup,
    // and add them to the clock

    var digit_holder = clock.find('.digits');

    $.each(positions, function(){

        if(this == ':'){
            digit_holder.append('<div class="dots">');
        }
        else{

            var pos = $('<div>');

            for(var i=1; i<8; i++){
                pos.append('<span class="d' + i + '">');
            }

            // Set the digits as key:value pairs in the digits object
            digits[this] = pos;

            // Add the digit elements to the page
            digit_holder.append(pos);
        }

    });

    // Add the weekday names

    var weekday_names = 'MON TUE WED THU FRI SAT SUN'.split(' '),
        weekday_holder = clock.find('.weekdays');

    $.each(weekday_names, function(){
        weekday_holder.append('<span>' + this + '</span>');
    });

    var weekdays = clock.find('.weekdays span');

    // Run a timer every second and update the clock

    (function update_time(){

        // Use moment.js to output the current time as a string
        // hh is for the hours in 12-hour format,
        // mm - minutes, ss-seconds (all with leading zeroes),
        // d is for day of week and A is for AM/PM

        var now = moment().format("hhmmssdA");

        digits.h1.attr('class', digit_to_name[now[0]]);
        digits.h2.attr('class', digit_to_name[now[1]]);
        digits.m1.attr('class', digit_to_name[now[2]]);
        digits.m2.attr('class', digit_to_name[now[3]]);
        digits.s1.attr('class', digit_to_name[now[4]]);
        digits.s2.attr('class', digit_to_name[now[5]]);

        // The library returns Sunday as the first day of the week.
        // Stupid, I know. Lets shift all the days one position down, 
        // and make Sunday last

        var dow = now[6];
        dow--;

        // Sunday!
        if(dow < 0){
            // Make it last
            dow = 6;
        }

        // Mark the active day of the week
        weekdays.removeClass('active').eq(dow).addClass('active');

        // Set the am/pm text:
        ampm.text(now[7]+now[8]);

        // Schedule this function to be run again in 1 sec
        setTimeout(update_time, 1000);

    })();

    // Switch the theme

    $('a.button').click(function(){
        clock.toggleClass('light dark');
    });

}); 
</script>
<script type="text/javascript">
    window.addEventListener('load', function () {
      vanillaCalendar.init({
        disablePastDays: true
      });
    })
  </script>
<script type="text/javascript">
  var vanillaCalendar = {
    month: document.querySelectorAll('[data-calendar-area="month"]')[0],
    next: document.querySelectorAll('[data-calendar-toggle="next"]')[0],
    previous: document.querySelectorAll('[data-calendar-toggle="previous"]')[0],
    label: document.querySelectorAll('[data-calendar-label="month"]')[0],
    activeDates: null,
    date: new Date,
    todaysDate: new Date,
    init: function(t) {
        this.options = t, this.date.setDate(1), this.createMonth(), this.createListeners()
    },
    createListeners: function() {
        var t = this;
        this.next.addEventListener("click", function() {
            t.clearCalendar();
            var e = t.date.getMonth() + 1;
            t.date.setMonth(e), t.createMonth()
        }), this.previous.addEventListener("click", function() {
            t.clearCalendar();
            var e = t.date.getMonth() - 1;
            t.date.setMonth(e), t.createMonth()
        })
    },
    createDay: function(t, e, a) {
        var n = document.createElement("div"),
            s = document.createElement("span");
        s.innerHTML = t, n.className = "vcal-date", n.setAttribute("data-calendar-date", this.date), 1 === t && (n.style.marginLeft = 0 === e ? 6 * 14.28 + "%" : 14.28 * (e - 1) + "%"), this.options.disablePastDays && this.date.getTime() <= this.todaysDate.getTime() - 1 ? n.classList.add("vcal-date--disabled") : (n.classList.add("vcal-date--active"), n.setAttribute("data-calendar-status", "active")), this.date.toString() === this.todaysDate.toString() && n.classList.add("vcal-date--today"), n.appendChild(s), this.month.appendChild(n)
    },
    dateClicked: function() {
        var t = this;
        this.activeDates = document.querySelectorAll('[data-calendar-status="active"]');
        for (var e = 0; e < this.activeDates.length; e++) this.activeDates[e].addEventListener("click", function(e) {
            document.querySelectorAll('[data-calendar-label="picked"]')[0].innerHTML = this.dataset.calendarDate, t.removeActiveClass(), this.classList.add("vcal-date--selected")
        })
    },
    createMonth: function() {
        for (var t = this.date.getMonth(); this.date.getMonth() === t;) this.createDay(this.date.getDate(), this.date.getDay(), this.date.getFullYear()), this.date.setDate(this.date.getDate() + 1);
        this.date.setDate(1), this.date.setMonth(this.date.getMonth() - 1), this.label.innerHTML = this.monthsAsString(this.date.getMonth()) + " " + this.date.getFullYear(), this.dateClicked()
    },
    monthsAsString: function(t) {
        return ["January", "Febuary", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"][t]
    },
    clearCalendar: function() {
        vanillaCalendar.month.innerHTML = ""
    },
    removeActiveClass: function() {
        for (var t = 0; t < this.activeDates.length; t++) this.activeDates[t].classList.remove("vcal-date--selected")
    }
};
</script>