<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Import</h3>

                    <div class="box-tools">
                        <div class="btn-group pull-right" style="margin-right: 10px">
                            <a href="../admin/student_user"
                               class="btn btn-sm btn-default"><i class="fa fa-list"></i>&nbsp;Danh sách</a>
                        </div>
                        <div class="btn-group pull-right" style="margin-right: 10px">
                            <a href="../admin/student_user"
                               class="btn btn-sm btn-default form-history-back"><i class="fa fa-arrow-left"></i>&nbsp;Trở về</a>
                        </div>
                    </div>
                </div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{$router_target}}"
                          enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('csv_file') ? ' has-error' : '' }}">
                            <label for="csv_file" class="col-md-4 control-label">Chọn CSV file</label>

                            <div class="col-md-6">
                                <input id="csv_file" type="file" class="form-control" name="csv_file" required>

                                @if ($errors->has('csv_file'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('csv_file') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{--<div class="form-group">--}}
                            {{--<div class="col-md-6 col-md-offset-4">--}}
                                {{--<div class="checkbox">--}}
                                    {{--<label>--}}
                                        {{--<input type="checkbox" name="header" checked> File contains header row?--}}
                                    {{--</label>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Upload File
                                </button>
                            </div>
                        </div>
                    </form>
                    <a href="{{asset('storage/Danh sách SV.xlsx')}}" download src="file" target="_blank">
                        Tải mẫu danh sách
                    </a>
                </div>
            </div>

        </div>
    </div>

</section>