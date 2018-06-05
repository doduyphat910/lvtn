<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->


    <!-- Header Navbar -->
    
        
        
            
        

        

        
        
            

                

                
                
                    
                    
                        
                        
                        
                        
                    
                    
                        
                        
                            

                            
                                
                                
                            
                        
                        
                            
                                
                            
                            
                                
                            
                        
                    
                
                
                
                    
                
            
        
    

    <style>
        .navbar-default .navbar-toggle .icon-bar {background-color: white;}
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
            <a href="<?php echo e(url('user/student')); ?>" class="logo" style="width: auto;">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini"><?php echo config('admin.logo-mini', config('admin.name')); ?></span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg"><?php echo config('admin.logo', config('admin.name')); ?></span>
            </a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">Trang chủ <span class="sr-only">(current)</span></a></li>
                    <li><a href="<?php echo e(url('user/subjectregister')); ?>">Đăng ký môn học</a></li>
                    <li><a href="#">Xem điểm</a></li>
                    <li><a href="<?php echo e(url('user/subjectparallel')); ?>">Xem môn song song</a></li>
                    <li><a href="<?php echo e(url('user/subjectbeforeafter')); ?>">Xem môn tiên quyết</a></li>
                    <li><a href="<?php echo e(url('user/comments')); ?>">Góp ý kiến</a></li>

                    <?php if(Auth::check()): ?>
                    
                    
                    </ul>

                      <ul class="nav navbar-nav navbar-right" style="margin-right: 0px;">

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Xin chào <?php echo e(Auth::User()->last_name); ?><img src="" class="user-image"><span class="caret"></span></a>
                             <ul class="dropdown-menu">
                                    <li><a href="<?php echo e(url('user/information')); ?>">Thông tin cá nhân</a></li>
                                   <li><a href="<?php echo e(url('logout')); ?>">Đăng xuất</a></li>
                            </ul>
                         </li>
                      </ul>
                    <?php endif; ?>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
</header>