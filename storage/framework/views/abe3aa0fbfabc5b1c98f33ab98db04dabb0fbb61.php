<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="<?php echo e(admin_base_path('/')); ?>" class="logo">
        
         <span class="logo-mini"><img src="../../../uploads/images/logo_2.png" height="50px;"></span>
         <span class="logo-lg"><img src="../../../uploads/images/logo_2.png" height="50px;"></span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <?php echo Admin::getNavbar()->render('left'); ?>


        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                <?php echo Admin::getNavbar()->render(); ?>


                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- The user image in the navbar-->
                        <img src="<?php echo e(Admin::user()->avatar); ?>" class="user-image" alt="User Image">
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        <span class="hidden-xs"><?php echo e(Admin::user()->name); ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- The user image in the menu -->
                        <li class="user-header">
                            <img src="<?php echo e(Admin::user()->avatar); ?>" class="img-circle" alt="User Image">

                            <p>
                                <?php echo e(Admin::user()->name); ?>

                                <small>Member since admin <?php echo e(Admin::user()->created_at); ?></small>
                            </p>
                        </li>
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="<?php echo e(admin_base_path('auth/setting')); ?>" class="btn btn-default btn-flat"><?php echo e(trans('admin.setting')); ?></a>
                            </div>
                            <div class="pull-right">
                                <a href="<?php echo e(admin_base_path('auth/logout')); ?>" class="btn btn-default btn-flat"><?php echo e(trans('admin.logout')); ?></a>
                            </div>
                        </li>
                    </ul>
                </li>
                <!-- Control Sidebar Toggle Button -->
                
                    
                
            </ul>
        </div>
    </nav>
</header>