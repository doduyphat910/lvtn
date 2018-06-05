<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <!-- /.box-header -->
            <!-- form start -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#tab-form-1" data-toggle="tab" aria-expanded="true">
                            Lớp học phần <i class="fa fa-exclamation-circle text-red hide"></i>
                        </a>
                    </li>
                    <li >
                        <a href="#tab-form-2" data-toggle="tab" aria-expanded="false">
                            Môn học song song <i class="fa fa-exclamation-circle text-red hide"></i>
                        </a>
                    </li>
                    <li >
                        <a href="#tab-form-3" data-toggle="tab" aria-expanded="false">
                            Môn học tiên quyết <i class="fa fa-exclamation-circle text-red hide"></i>
                        </a>
                    </li>
                </ul>
                <div class="tab-content fields-group">
                    <div class="tab-pane active" id="tab-form-1">
                        @include('admin.Subject.table_subject_register')
                    </div>
                    <div class="tab-pane" id="tab-form-2">
                        @include('admin.Subject.table_subject_parallel')
                    </div>
                    <div class="tab-pane" id="tab-form-3">
                        @include('admin.Subject.table_subject_after_before')
                    </div>
                </div>

            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>