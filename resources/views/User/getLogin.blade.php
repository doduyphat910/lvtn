<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title>Ela - Bootstrap Admin Dashboard Template</title>
    <base href="{{asset('')}}">
    <!-- Bootstrap Core CSS -->
    <link href="student/css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="student/css/helper.css" rel="stylesheet">
    <link href="student/css/style.css" rel="stylesheet">
</head>

<body class="fix-header fix-sidebar"  style="background-color: whitesmoke;">
    <!-- Preloader - style you can find in spinners.css -->
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
			<circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> </svg>
    </div>
    <!-- Main wrapper  -->
    <div class="container-fluid">
        <div class="row">
            
                
                <div class="col-lg-2 col-md-12 col-sm-12" style="margin-top: auto;margin-bottom: auto;">
                    <div class="row card">
                    <img src="../uploads/images/logo.png" style="height: 200px;width:100%">
                    </div>
                </div>
                <div class="col-lg-10 card col-md-12 col-sm-12" >
                    <img src="../uploads/images/banner-top.png" style="height: 200px;width:100%">
                </div>
            
   
        </div>
</div>
            <div class="container-fluid">
                <div class="row" style="margin-top: -30px;">
                    <div class="col-lg-9">
                        
                            <div class="login-content card">
                          
                                
                                <h1 class="text-center"><b>THÔNG BÁO</b></h1>
                                    <table class="table table-hover table-bordered">
                                        <thead class="thead-dark">
                                            <tr align="center">
                                                <th>Tên thông báo</th>
                                                <th>Mô tả thông báo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                          
                                             @foreach($list as $value)
                                            <tr>
                                               
                                                <td>{{$value->name}}</td>
                                                <td><a href="{{$value->url}}"</a> {{$value->description}}</td>
                                                
                                            </tr>
                                            @endforeach
                                        </tbody>

                                    </table>
                                    <div class="pagination" style="justify-content: flex-end;">
                                        
                                                {{$list->links()}}
                                     </div>
                          
                        </div>
                        
                    </div>
                    <div class="col-lg-3">
                        <div class="login-content card" style="margin-right: -15px;">
                            <div class="login-form">
                                <h4><b>Đăng nhập</b></h4>

                                <form action="postLogin" method="POST">
                                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                                    <div class="form-group">
                                        <label>Tài khoản</label>
                                        <input type="text" name="code_number" class="form-control" placeholder="Mã số sinh viên">
                                    </div>
                                    <div class="form-group">
                                        <label>Mật khẩu</label>
                                        <input type="password" name="password" class="form-control" placeholder="Mật khẩu">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-flat m-b-30 m-t-30">Đăng nhập</button>
                                </form>
                                    @if(count($errors)>0)
                                    <div class="alert alert-danger text-center">
                                        @foreach($errors->all() as $err)
                                            {{$err}}<br>
                                        @endforeach
                                    </div>
                                    @endif
                                    
                                    @if(session('notification'))  
                                    <div class="alert alert-danger text-center">                             
                                            {{session('notification')}}
                                            </div>                                
                                    @endif
                                    
                            </div>
                        </div>
                    </div>
                </div>
            </div>


    <!-- End Wrapper -->
    <!-- All Jquery -->
    <script src="student/js/lib/jquery/jquery.min.js"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="student/js/lib/bootstrap/js/popper.min.js"></script>
    <script src="student/js/lib/bootstrap/js/bootstrap.min.js"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="student/js/jquery.slimscroll.js"></script>
    <!--Menu sidebar -->
    <script src="student/js/sidebarmenu.js"></script>
    <!--stickey kit -->
    <script src="student/js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>
    <!--Custom JavaScript -->
    <script src="student/js/custom.min.js"></script>
    <script type="text/javascript">
        
    </script>
</body>

</html>