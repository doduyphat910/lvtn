<head>
    <script src="<?php echo e(admin_asset("/vendor/laravel-admin/chartjs/chart.js")); ?>"></script>
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
   /*Lich*/
    
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
            <a href="#">
               <div class="circle-tile-heading dark-blue">
                  <i class="fa fa-users fa-fw fa-3x"></i>
               </div>
            </a>
            <div class="circle-tile-content dark-blue">
               <div class="circle-tile-description text-faded">
                  Users
               </div>
               <div class="circle-tile-number text-faded">
                  265
                  <i class="fa fa-bar-chart" aria-hidden="true"></i>                                    
               </div>
               <a href="#" class="circle-tile-footer">More Info <i class="fa fa-chevron-circle-right"></i></a>
            </div>
         </div>
      </div>
      <div class="col-lg-2 col-sm-6 col-md-6">
         <div class="circle-tile">
            <a href="#">
               <div class="circle-tile-heading green">
                  <i class="fa fa-money fa-fw fa-3x"></i>
               </div>
            </a>
            <div class="circle-tile-content green">
               <div class="circle-tile-description text-faded">
                  Revenue
               </div>
               <div class="circle-tile-number text-faded">
                  $32,384
                  <i class="fa fa-money" aria-hidden="true"></i>
               </div>
               <a href="#" class="circle-tile-footer">More Info <i class="fa fa-chevron-circle-right"></i></a>
            </div>
         </div>
      </div>
      <div class="col-lg-2 col-sm-6 col-md-6">
         <div class="circle-tile">
            <a href="#">
               <div class="circle-tile-heading orange">
                  <i class="fa fa-bell fa-fw fa-3x"></i>
               </div>
            </a>
            <div class="circle-tile-content orange">
               <div class="circle-tile-description text-faded">
                  Alerts
               </div>
               <div class="circle-tile-number text-faded">
                  9 New
                  <i class="fa fa-bell" aria-hidden="true"></i>
               </div>
               <a href="#" class="circle-tile-footer">More Info <i class="fa fa-chevron-circle-right"></i></a>
            </div>
         </div>
      </div>
      <div class="col-lg-2 col-sm-6 col-md-6">
         <div class="circle-tile">
            <a href="#">
               <div class="circle-tile-heading blue">
                  <i class="fa fa-tasks fa-fw fa-3x"></i>
               </div>
            </a>
            <div class="circle-tile-content blue">
               <div class="circle-tile-description text-faded">
                  Tasks
               </div>
               <div class="circle-tile-number text-faded">
                  10
                  <i class="fa fa-tasks" aria-hidden="true"></i>
               </div>
               <a href="#" class="circle-tile-footer">More Info <i class="fa fa-chevron-circle-right"></i></a>
            </div>
         </div>
      </div>
      <div class="col-lg-2 col-sm-6 col-md-6">
         <div class="circle-tile">
            <a href="#">
               <div class="circle-tile-heading red">
                  <i class="fa fa-shopping-cart fa-fw fa-3x"></i>
               </div>
            </a>
            <div class="circle-tile-content red">
               <div class="circle-tile-description text-faded">
                  Orders
               </div>
               <div class="circle-tile-number text-faded">
                  24
                  <i class="fa fa-shopping-cart" aria-hidden="true"></i>
               </div>
               <a href="#" class="circle-tile-footer">More Info <i class="fa fa-chevron-circle-right"></i></a>
            </div>
         </div>
      </div>
      <div class="col-lg-2 col-sm-6 col-md-6">
         <div class="circle-tile">
            <a href="#">
               <div class="circle-tile-heading purple">
                  <i class="fa fa-comments fa-fw fa-3x"></i>
               </div>
            </a>
            <div class="circle-tile-content purple">
               <div class="circle-tile-description text-faded">
                  Mentions
               </div>
               <div class="circle-tile-number text-faded">
                  96
                  <i class="fa fa-area-chart" aria-hidden="true"></i>                                  
               </div>
               <a href="#" class="circle-tile-footer">More Info <i class="fa fa-chevron-circle-right"></i></a>
            </div>
         </div>
      </div>
   </div>
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
        <div class="col-lg-6 col-sm-6 col-md-6">
              <canvas id="myChart"></canvas>   
        </div>
         <div class="col-lg-6 col-sm-6 col-md-6">
              <canvas id="myLineChart"></canvas>   
        </div>
   </div>
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
        <div class="col-lg-6 col-sm-6 col-md-6">
                
        </div>
         <div class="col-lg-6 col-sm-6 col-md-6">
                
        </div>
   </div>
</div>        
<script>

var ctx = document.getElementById("myChart").getContext('2d');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
        datasets: [{
            label: '# of Votes',
            data: [12, 19, 3, 5, 2, 3],
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
        labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
        datasets: [{
            label: '# of Votes',
            data: [12, 19, 3, 5, 2, 3],
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
</script>