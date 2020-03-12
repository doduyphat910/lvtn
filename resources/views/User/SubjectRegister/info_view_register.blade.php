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
                </ul>
                <div class="tab-content fields-group">
                    <div class="tab-pane active" id="tab-form-1">
                        @include('User.SubjectRegister.table_subject_register')
                        @include('User.SubjectRegister.info_view_subject')
                    </div>
                </div>

            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>