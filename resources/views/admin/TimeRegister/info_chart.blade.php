<head>
    <script src="{{ admin_asset("/vendor/laravel-admin/chartjs/chart.js")}}"></script>
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>--}}
</head>
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
            <div class="row">
                <div class="col-lg-12 col-sm-12 col-md-12 " >
                    <strong style="font-size: 20px"> Chú thích </strong> <br>
                    @foreach($nameSubjects as $key => $nameSubject )
                        Mã: {{$key}} <i class="fa fa-arrow-right"></i>  {{$nameSubject}} <br>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-sm-6 col-md-6">
            <canvas id="myLineChart"></canvas>
        </div>
    </div>
</div>



<script>
    //chart 1
    var ctx = document.getElementById("myChart").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            // labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
            labels: <?php echo $subject ?>,
            datasets: [{
                label: 'Số lượng yêu cầu',
                // data: [12, 19, 3, 5, 2, 3],
                data: <?php echo $student ?>,
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

    //chart 2
    var ctx = document.getElementById("myLineChart").getContext('2d');
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            // labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
            labels: <?php echo $class ?>,
            datasets: [{
                label: 'Lượt đăng ký',
                // data: [12, 19, 3, 5, 2, 3],
                data: <?php echo $countClass ?>,
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