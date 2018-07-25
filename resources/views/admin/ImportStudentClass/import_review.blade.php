<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Bảng dữ liệu Import</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
                    <form class="form-horizontal" method="POST" action="/admin/import-student-class/parse">
                        {{ csrf_field() }}
                        <input type="hidden" value="{{$idClass}}" name="idClass">
                        <input type="hidden" name="csv_data_file_id" value="{{ $csv_data_file->id }}"/>
                        <table class="table table-hover">
                            @if (isset($csv_header_fields))
                                <tr>
                                    @foreach ($csv_header_fields as $csv_header_field)
                                        <th>{{ $csv_header_field }}</th>
                                    @endforeach
                                </tr>
                            @endif
                            @foreach ($csv_data as $row)
                                <tr>
                                    @foreach ($row as $key => $value)
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                            <tr>
                                @foreach ($csv_data[0] as $key => $value)
                                    <td>
                                        <select name="fields[{{ $key }}]">
                                            @foreach (config('admin.student_user_fields') as $db_field)
                                                <option value="{{ (\Request::has('header')) ? $db_field : $loop->index }}"
                                                        @if ($key === $db_field) selected @endif>{{ $db_field }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                @endforeach
                            </tr>
                        </table>

                        <button type="submit" class="btn btn-primary">
                            Import Dữ liệu
                        </button>
                    </form>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>

</section>
