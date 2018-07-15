<style>
    .fa-fw {
        color: #2f3542;
    }

</style>
<div class="box">
    <div class="box-header">

        <h3 class="box-title"></h3>

        <div class="pull-right">
            {!! $grid->renderFilter() !!}
            {!! $grid->renderExportButton() !!}
            {!! $grid->renderCreateButton() !!}
        </div>
        <div class="pull-left">
            <span>
                {!! $grid->renderHeaderTools() !!}
                <?php $show = 0; ?>
                    @foreach($grid->rows() as $row)
                        @foreach($grid->columnNames as $name)
                            @if($name == 'Sô tín chỉ hiện tại' && $show == 0)
                                <?php $show++; ?>
                                Tổng số TC: {!! $row->column($name) !!}
                            @endif
                        @endforeach
                    @endforeach
            </span>
        </div>


    </div>
    <!-- /.box-header -->
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover table-bordered table-striped">
            <tr>
                @foreach($grid->columns() as $column)
                    @if($column->getLabel() != 'Sô tín chỉ hiện tại' && $column->getLabel() != 'Điểm TK ALL' )
                    <th style="background-color: #3c8dbc;color:white;">{{$column->getLabel()}}{!! $column->sorter() !!}</th>
                    @endif
                @endforeach
            </tr>

            @foreach($grid->rows() as $row)
            <tr {!! $row->getRowAttributes() !!}>
                @foreach($grid->columnNames as $name)
                    @if($name != 'Sô tín chỉ hiện tại' && $name != 'Điểm TK ALL')
                <td {!! $row->getColumnAttributes($name) !!}>
                    {!! $row->column($name) !!}
                </td>
                    @endif
                @endforeach
            </tr>
            @endforeach

            {!! $grid->renderFooter() !!}

        </table>
    </div>
    <div class="box-footer clearfix">
        {!! $grid->paginator() !!}<br><br>
        <div class="pull-left">
            <?php $showPoint = 0; ?>
            @foreach($grid->rows() as $row)
                @foreach($grid->columnNames as $name)
                    @if($name == 'Điểm TK ALL' && $showPoint == 0)
                        <?php $showPoint++; ?>
                        Điểm TK: {!! $row->column($name) !!}
                    @endif
                @endforeach
            @endforeach
        </div>
    </div>
    <!-- /.box-body -->
</div>
