<?php

namespace App\Admin\Controllers;

use App\Models\TimeTable;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\MessageBag;

class TimeTableController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('Phòng, tiết học');
            $content->description('Tiết học');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $timeTable = TimeTable::find($id);
            $content->header('Tiết học');
            $content->description($timeTable->period);

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(TimeTable::class, function (Grid $grid) {

//            $grid->id('ID')->sortable();
            $grid->rows(function (Grid\Row $row) {
                $row->column('number', $row->number);
            });
            $grid->number('STT');
            $grid->period('Tiết học')->sortable();
            $grid->time_start('Thời gian bắt đầu')->sortable();
            $grid->time_end('Thời gian kết thúc')->sortable();
            $grid->created_at('Tạo vào lúc')->sortable();
            $grid->updated_at('Cập nhật vào lúc')->sortable();
            $grid->filter(function ($filter){
                $filter->disableIdFilter();
                $filter->like('period', 'Tiết học');
                $filter->between('time_start','TG bắt đầu')->time();
                $filter->between('time_end','TG kết thúc')->time();
                $filter->between('created_at', 'Tạo vào lúc')->datetime();
            });
            $grid->disableExport();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(TimeTable::class, function (Form $form) {
//            $form->display('id', 'ID');
            $form->number('period', 'Tiết học')->rules('integer|min:1');
            $form->timeRange('time_start', 'time_end', 'Thời gian');
            $form->saving(function (Form $form) {
                $form->period = 'Tiết '.$form->period;
                $countPeriod = TimeTable::where('period', $form->period)->get()->count();
                if($countPeriod > 0) {
                    $error = new MessageBag([
                        'title'   => 'Lỗi',
                        'message' => 'Tiết học này đã tồn tại',
                    ]);
                    return back()->with(compact('error'));
                }
            });
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
