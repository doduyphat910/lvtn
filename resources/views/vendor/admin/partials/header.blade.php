<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="{{ admin_base_path('/') }}" class="logo">
        {{-- <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini">{!! config('admin.logo-mini', config('admin.name')) !!}</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">{!! config('admin.logo', config('admin.name')) !!}</span> --}}
         <span class="logo-mini"><img src="../../../../uploads/images/logo_2.png" height="50px;"></span>
         <span class="logo-lg"><img src="../../../../uploads/images/logo_2.png" height="50px;"></span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        {!! Admin::getNavbar()->render('left') !!}

        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                {!! Admin::getNavbar()->render() !!}

                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- The user image in the navbar-->
                        @php $admin = Admin::user();  @endphp
                        {{--<img src="{{ Admin::user()->avatar }}" class="user-image" alt="User Image">--}}
                    <img src="../../../../uploads/<?php
                        if(empty($admin->image)) {
                            echo 'images/user2-160x160.jpg';
                        }
                        else {
                         echo $admin->image;
                        }
                    ?>" class="user-image" alt="User Image">
                    <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        <span class="hidden-xs">{{ Admin::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- The user image in the menu -->
                        <li class="user-header">
{{--                            <img src="{{ Admin::user()->avatar }}" class="img-circle" alt="User Image">--}}
                            <img src="../../../../uploads/<?php
                            if(empty($admin->image)) {
                                echo 'images/user2-160x160.jpg';
                            }
                            else {
                                echo $admin->image;
                            }
                            ?>" class="img-circle" alt="User Image">
                            <p>
                                {{ Admin::user()->name }}
                                <small>Thành viên từ ngày {{ Admin::user()->created_at }}</small>
                            </p>
                        </li>
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{ admin_base_path('auth/setting') }}" class="btn btn-default btn-flat">{{ trans('admin.setting') }}</a>
                            </div>
                            <div class="pull-right">
                                <a href="{{ admin_base_path('auth/logout') }}" class="btn btn-default btn-flat">{{ trans('admin.logout') }}</a>
                            </div>
                        </li>
                    </ul>
                </li>
                <!-- Control Sidebar Toggle Button -->
                {{--<li>--}}
                    {{--<a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>--}}
                {{--</li>--}}
            </ul>
        </div>
    </nav>
</header>