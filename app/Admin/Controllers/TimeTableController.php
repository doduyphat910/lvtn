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

            $content->header('header');
            $content->description('description');

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

            $grid->id('ID')->sortable();
            $grid->period('Tiết học')->display(function ($period) {
                return '<a href="#" >'.$period.'</a>';
            });
            $grid->time_start('Thời gian bắt đầu');
            $grid->time_end('Thời gian kết thúc');
            $grid->created_at();
            $grid->updated_at();
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

            $form->display('id', 'ID');
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
