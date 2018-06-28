<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->


    <!-- Header Navbar -->
    {{--<nav class="navbar navbar-static-top" role="navigation">--}}
        {{--<!-- Sidebar toggle button-->--}}
        {{--<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">--}}
            {{--<span class="sr-only">Toggle navigation</span>--}}
        {{--</a>--}}

        {{--{!! Admin::getNavbar()->render('left') !!}--}}

        {{--<!-- Navbar Right Menu -->--}}
        {{--<div class="navbar-custom-menu">--}}
            {{--<ul class="nav navbar-nav">--}}

                {{--{!! Admin::getNavbar()->render() !!}--}}

                {{--<!-- User Account Menu -->--}}
                {{--<li class="dropdown user user-menu">--}}
                    {{--<!-- Menu Toggle Button -->--}}
                    {{--<a href="#" class="dropdown-toggle" data-toggle="dropdown">--}}
                        {{--<!-- The user image in the navbar-->--}}
                        {{--<img src="{{ Admin::user()->avatar }}" class="user-image" alt="User Image">--}}
                        {{--<!-- hidden-xs hides the username on small devices so only the image appears. -->--}}
                        {{--<span class="hidden-xs">{{ Admin::user()->name }}</span>--}}
                    {{--</a>--}}
                    {{--<ul class="dropdown-menu">--}}
                        {{--<!-- The user image in the menu -->--}}
                        {{--<li class="user-header">--}}
                            {{--<img src="{{ Admin::user()->avatar }}" class="img-circle" alt="User Image">--}}

                            {{--<p>--}}
                                {{--{{ Admin::user()->name }}--}}
                                {{--<small>Member since admin {{ Admin::user()->created_at }}</small>--}}
                            {{--</p>--}}
                        {{--</li>--}}
                        {{--<li class="user-footer">--}}
                            {{--<div class="pull-left">--}}
                                {{--<a href="{{ admin_base_path('auth/setting') }}" class="btn btn-default btn-flat">{{ trans('admin.setting') }}</a>--}}
                            {{--</div>--}}
                            {{--<div class="pull-right">--}}
                                {{--<a href="{{ admin_base_path('auth/logout') }}" class="btn btn-default btn-flat">{{ trans('admin.logout') }}</a>--}}
                            {{--</div>--}}
                        {{--</li>--}}
                    {{--</ul>--}}
                {{--</li>--}}
                {{--<!-- Control Sidebar Toggle Button -->--}}
                {{--<li>--}}
                    {{--<a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>--}}
                {{--</li>--}}
            {{--</ul>--}}
        {{--</div>--}}
    {{--</nav>--}}
{{--     <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="#">Trang chủ <span class="sr-only">(current)</span></a></li>
                    <li><a href="#">Đăng ký môn học</a></li>
                    <li><a href="#">Xem điểm</a></li>
                    <li><a href="#">Xem môn song song</a></li>
                    <li><a href="#">Xem môn tiên quyết</a></li>
                    <li><a href="#">Góp ý kiến</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><img src="" class="user-image" alt="User Image"> <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Thông tin cá nhân</a></li>
                            <li><a href="#">Đăng xuất</a></li>
                        </ul>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav> --}}
    <style>
        .navbar-default .navbar-toggle .icon-bar {background-color: white;}
        /*.active{
            background-color: blue;*/
        .
        }
    </style>
    <nav class="navbar navbar-default navbar-fixed-top" style="margin-left: 0px;">
      <div class="container-flud">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
            <a href="{{ url('user/student') }}" class="logo" style="width: auto;">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini">{!! config('admin.logo-mini', config('admin.name')) !!}</span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg">{!! config('admin.logo', config('admin.name')) !!}</span>
            </a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="{{ url('user/student') }}" >Trang chủ <span class="sr-only">(current)</span></a></li>
                    <li class="dropdown-register">
                         <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">Đăng ký <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ url('user/subject-register') }}"><i class="glyphicon glyphicon-pencil"></i>Đăng ký môn học</a></li>
                            <li><a href="javascript:void(0);"><i class="glyphicon glyphicon-pencil"></i>Đăng ký học cải thiện, học lại</a></li>
                            <li><a href="{{ url('user/user-subject') }}"><i class="glyphicon glyphicon-pencil"></i>Đăng ký ngoài kế hoạch</a></li>
                            
                        </ul>
                       
                    </li>
                    <li><a href="{{ url('user/result-register') }}">Kết quả đăng ký</a></li>
                    <li><a href="#">Xem điểm</a></li>
                    <li><a href="{{ url('user/subject-parallel') }}">Xem môn song song</a></li>
                    <li><a href="{{ url('user/subject-before-after') }}">Xem môn tiên quyết</a></li>
                    <li><a href="{{ url('user/comments') }}">Góp ý kiến</a></li>

                    @if(Auth::check())
                    </ul>

                      <ul class="nav navbar-nav navbar-right" style="margin-right: 0px;">

                        <li class="dropdown-register">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Xin chào {{Auth::User()->last_name}}<img src="" class="user-image"><span class="caret"></span></a>
                             <ul class="dropdown-menu">
                                 @php $id = Auth::User()->id @endphp
                                    <li><a href="{{ url('user/information/'. $id.'/edit') }}"><i class="glyphicon glyphicon-user"></i>Thông tin cá nhân</a></li>
                                   <li><a href="{{ url('logout') }}"><i class="glyphicon glyphicon-log-out"></i>Đăng xuất</a></li>
                            </ul>
                         </li>
                      </ul>
                    @endif
        </div><!--/.nav-collapse -->
      </div>
    </nav>
</header>

{{-- <script>
    $(document).ready(function(){
        // $('.nav.navbar-nav li').on('click', function(){
        //      $('#navbar a').removeClass('active');
        // });
        
        var url = window.location.pathname, 
        urlRegExp = new RegExp(url.replace(/\/$/,'') + "$"); // create regexp to match current url pathname and remove trailing slash if present as it could collide with the link in navigation in case trailing slash wasn't present there
        // now grab every link from the navigation
        $('#navbar a').each(function(){

            // and test its normalized href against the url pathname regexp
            if(urlRegExp.test(this.href.replace(/\/$/,''))){
                $(this).addClass('active');
                $(this).parent().previoussibling().find('a').removeClass('active');
            }
        });

    });
</script> --}}
<script type="text/javascript">
    $('li.dropdown-register').hover(function() {
  $(this).find('.dropdown-menu').stop(true, true).fadeIn(500);
}, function() {
  $(this).find('.dropdown-menu').stop(true, true).fadeOut(500);
});
</script>