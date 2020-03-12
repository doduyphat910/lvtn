<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <!-- /.box-header -->
            <!-- form start -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#tab-form-1" data-toggle="tab" aria-expanded="true">
                            Lớp <i class="fa fa-exclamation-circle text-red hide"></i>
                        </a>
                    </li>
                    {{--<li class="">--}}
                        {{--<a href="#tab-form-2" data-toggle="tab" aria-expanded="false">--}}
                            {{--Bartender <i class="fa fa-exclamation-circle text-red hide"></i>--}}
                        {{--</a>--}}
                    {{--</li>--}}
                </ul>
                <div class="tab-content fields-group">
                    <div class="tab-pane active" id="tab-form-1">
                        @include('admin.Department.table_class')
                    </div>
                    {{--<div class="tab-pane" id="tab-form-2">--}}
                        {{--@include('area.table_bartender')--}}
                    {{--</div>--}}
                </div>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>