<section class="content">
<div class="box-body">
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-check"></i> Success!</h4>
        Thành công : {{$row_add_successs}}

    </div>
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-ban"></i> Error!</h4>
{{--        Thất bại : {{$row_error}}--}}
            Thất bại : {{count($error_logs)}}

        <div class="box collapsed-box" style="background-color: black">
                <div class="box-tools">
                    <button type="button" style="background: black" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
                        <i class="fa fa-plus"></i></button>
                    {{--<button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove">--}}
                        {{--<i class="fa fa-times"></i></button>--}}
                </div>
            <div class="box-body" style="background-color: black">
                @foreach($error_logs as $key=> $error_log)
                    Dòng {{$key+1}} : Có MSSV {{$error_log}}
                    <br>
                @endforeach
            </div>

        </div>
    </div>
    {{--<div class="alert alert-warning alert-dismissible">--}}
        {{--<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>--}}
        {{--<h4><i class="icon fa fa-warning"></i> Duplicated!</h4>--}}
    {{--</div>--}}

</div>
</section>

