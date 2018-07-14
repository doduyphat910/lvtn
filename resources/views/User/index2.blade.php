<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Đăng ký môn học  </title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="stylesheet" href="{{ admin_asset("/vendor/laravel-admin/AdminLTE/bootstrap/css/bootstrap.min.css") }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ admin_asset("/vendor/laravel-admin/font-awesome/css/font-awesome.min.css") }}">

    <!-- Theme style -->
    <link rel="stylesheet" href="{{ admin_asset("/vendor/laravel-admin/AdminLTE/dist/css/skins/" . config('admin.skin') .".min.css") }}">
    
    {!! Admin::css() !!}
    <link rel="stylesheet" href="{{ admin_asset("/vendor/laravel-admin/laravel-admin/laravel-admin.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("/vendor/laravel-admin/nprogress/nprogress.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("/vendor/laravel-admin/sweetalert/dist/sweetalert.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("/vendor/laravel-admin/nestable/nestable.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("/vendor/laravel-admin/toastr/build/toastr.min.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("/vendor/laravel-admin/bootstrap3-editable/css/bootstrap-editable.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("/vendor/laravel-admin/google-fonts/fonts.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("/vendor/laravel-admin/AdminLTE/dist/css/AdminLTE.min.css") }}">

  {{--   <link rel="stylesheet" href="{{ admin_asset("/vendor/laravel-admin/AdminLTE/plugins/iCheck/all.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("/vendor/laravel-admin/AdminLTE/plugins/colorpicker/bootstrap-colorpicker.min.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("/vendor/laravel-admin/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("/vendor/laravel-admin/bootstrap-fileinput/css/fileinput.min.css?v=4.3.7") }}"> --}}
    <link rel="stylesheet" href="{{ admin_asset("/vendor/laravel-admin/AdminLTE/plugins/select2/select2.min.css") }}">
   {{--  <link rel="stylesheet" href="{{ admin_asset("/vendor/laravel-admin/AdminLTE/plugins/ionslider/ion.rangeSlider.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("/vendor/laravel-admin/AdminLTE/plugins/ionslider/ion.rangeSlider.skinNice.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("/vendor/laravel-admin/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("/vendor/laravel-admin/fontawesome-iconpicker/dist/css/fontawesome-iconpicker.min.css") }}">
    <link rel="stylesheet" href="{{ admin_asset("/vendor/laravel-admin/bootstrap-duallistbox/dist/bootstrap-duallistbox.min.css") }}"> --}}



    <!-- REQUIRED JS SCRIPTS -->
    <script src="{{ admin_asset ("/vendor/laravel-admin/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js") }}"></script>
    <script src="{{ admin_asset ("/vendor/laravel-admin/AdminLTE/bootstrap/js/bootstrap.min.js") }}"></script>
    <script src="{{ admin_asset ("/vendor/laravel-admin/AdminLTE/plugins/slimScroll/jquery.slimscroll.min.js") }}"></script>
    <script src="{{ admin_asset ("/vendor/laravel-admin/AdminLTE/dist/js/app.min.js") }}"></script>
    <script src="{{ admin_asset ("/vendor/laravel-admin/jquery-pjax/jquery.pjax.js") }}"></script>
    <script src="{{ admin_asset ("/vendor/laravel-admin/nprogress/nprogress.js") }}"></script>

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
<style>
    body {
        font-family: "Times New Roman";
    }
    .bg-aqua, .callout.callout-info, .alert-info, .label-info, .modal-info .modal-body {
        background-color: #3c8dbc !important;
        font-size: 1.3rem;

    }
    .bg-light-blue, .label-primary, .modal-primary .modal-body {
        background-color: #3b5998 !important;
        color: black;
        font-size: 1.3rem;
    }
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
      vertical-align: middle;
    }
    .label-success{
        background-color: #00a657cc !important;
        font-size: 1.3rem;
    }
    .btnTotal {
         font-size: 1.3rem;
    }
    .select2-container--default .select2-selection--single, .select2-selection .select2-selection--single {
    border: 1px solid #d2d6de;
    border-radius: 0;
    padding: 6px 12px;
    height: 34px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #3c8dbc;
    border-color: #367fa9;
    padding: 1px 10px;
    color: #fff;
    }
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #d2d6de;
        border-radius: 0;
    }
    .indam{
        font-weight: bold;
        font-family: Times New Roman;
    }
    .note{
        font-weight: bold;
        font-family: Times New Roman;
    }
    .notetime{
        font-size: 20px;
        font-weight: bold;
        font-family: Times New Roman;
    }
    .colorth{
        background-color: #3c8dbc;
        color: white;
    }
    #backtotop{
        position: fixed;
        width: 50px;
        height: 50px;
        background-color: #00a657cc;
        right: 1%;
        bottom: 5%;
        border-radius: 50%;
        cursor: pointer;
        display: none;
    }
    .iconbacktop {
    position: absolute;
    top: 10%;
    left: 17%;
    font-size: 2.5rem;
    color: white;
    padding: 5px;
}
</style>
</head>

<body class="hold-transition {{config('admin.skin')}} {{join(' ', config('admin.layout'))}}">
{{--<div class="wrapper ">--}}

    @include('User.partials.header')

{{--    @include('User.partials.sidebar')--}}

    <div class="container-fluid" id="pjax-container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                    @yield('content')
                    {!! \App\Http\Extensions\Facades\User::script() !!}
            </div>
        </div>
        <div id="backtotop"><i class="fa fa-arrow-up iconbacktop" aria-hidden="true"></i></div>
    </div>

    @include('User.partials.footer')
    
{{--</div>--}}

<!-- ./wrapper -->

<script>
    function LA() {}
    LA.token = "{{ csrf_token() }}";
</script>

<!-- REQUIRED JS SCRIPTS -->
<script src="{{ admin_asset ("/vendor/laravel-admin/nestable/jquery.nestable.js") }}"></script>
<script src="{{ admin_asset ("/vendor/laravel-admin/toastr/build/toastr.min.js") }}"></script>
<script src="{{ admin_asset ("/vendor/laravel-admin/bootstrap3-editable/js/bootstrap-editable.min.js") }}"></script>
<script src="{{ admin_asset ("/vendor/laravel-admin/sweetalert/dist/sweetalert.min.js") }}"></script>

<script src={{ admin_asset ("/vendor/laravel-admin/AdminLTE/plugins/iCheck/icheck.min.js")}}></script>
<script src={{ admin_asset ("/vendor/laravel-admin/AdminLTE/plugins/colorpicker/bootstrap-colorpicker.min.js")}}></script>
<script src={{ admin_asset ("/vendor/laravel-admin/AdminLTE/plugins/input-mask/jquery.inputmask.bundle.min.js")}}></script>
<script src={{ admin_asset ("/vendor/laravel-admin/moment/min/moment-with-locales.min.js")}}></script>
<script src={{ admin_asset ("/vendor/laravel-admin/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js")}}></script>
<script src={{ admin_asset ("/vendor/laravel-admin/bootstrap-fileinput/js/plugins/canvas-to-blob.min.js?v=4.3.7")}}></script>
<script src={{ admin_asset ("/vendor/laravel-admin/bootstrap-fileinput/js/fileinput.min.js?v=4.3.7")}}></script>
<script src={{ admin_asset ("/vendor/laravel-admin/AdminLTE/plugins/select2/select2.full.min.js")}}></script>
<script src={{ admin_asset ("/vendor/laravel-admin/number-input/bootstrap-number-input.js")}}></script>
<script src={{ admin_asset ("/vendor/laravel-admin/AdminLTE/plugins/iCheck/icheck.min.js")}}></script>
<script src={{ admin_asset ("/vendor/laravel-admin/AdminLTE/plugins/ionslider/ion.rangeSlider.min.js")}}></script>
<script src={{ admin_asset ("/vendor/laravel-admin/bootstrap-switch/dist/js/bootstrap-switch.min.js")}}></script>
<script src={{ admin_asset ("/vendor/laravel-admin/fontawesome-iconpicker/dist/js/fontawesome-iconpicker.min.js")}}></script>
<script src={{ admin_asset ("/vendor/laravel-admin/bootstrap-duallistbox/dist/jquery.bootstrap-duallistbox.min.js")}}></script>


{{--{!! \App\Http\Extensions\Facades\User::js() !!}--}}
<script src="{{ admin_asset ("/vendor/laravel-admin/laravel-admin/laravel-admin.js") }}"></script>
<script> $(".grid-refresh").hide();
    $(".box-title").css("color", "white");
</script>
<script type="text/javascript">
    $(window).scroll(function(event) {
        /* Act on the event */
        console.log('Hello');
        if($(this).scrollTop() >= 100){
            $('#backtotop').fadeIn(150);        
        }
        else{
            $('#backtotop').fadeOut(150);
        }
    });
    $('#backtotop').on('click', function(){
                $('html, body').animate({scrollTop: 0}, 1000 );
            });
</script>
</body>
</html>
