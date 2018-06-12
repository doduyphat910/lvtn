<?php

namespace App\Admin\Controllers;

use App\Models\StudentUser;
use App\Models\TimeRegister;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\Semester;
use Illuminate\Support\Facades\Route;

class TimeRegisterController extends Controller
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

            $content->header('TG Đăng ký');
            $content->description('DS TG Đăng ký');

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

            $content->header('TG Đăng ký');
            $content->description('Thêm TG Đăng ký');

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
        return Admin::grid(TimeRegister::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->name('Tên');
            $grid->time_register_start('Thời gian bắt đầu');
            $grid->time_register_end('Thời gian kết thúc');
            $grid->semester('Học kỳ')->display(function ($semester) {
                switch ($semester) {
                    case 0: return "<span class='label label-info'>Học kỳ hè</span>";
                    break;
                    case 1: return "<span class='label label-info'>Học kỳ 1</span>";
                    break;
                    case 2: return "<span class='label label-info'>Học kỳ 2</span>";
                    break;
                    default:
                        return '';
                }
            });
            $grid->credits_max('Số TC tối đa');
            $grid->credits_min('Số TC tối thiểu');
            $grid->school_year('Khóa được ĐK')->display(function ($schoolYears){
                $schoolYears = array_map(function ($schoolYears){
                    if($schoolYears == 'All') {
                        return "<span class='label label-primary'>Tất cả</span>";
                    } elseif ($schoolYears ) {
//                        $arraySchoolYear = StudentUser::distinct('school_year')->orderBy('school_year', 'DESC')->limit(6)->pluck('school_year')->toArray();
//                        array_unshift($arraySchoolYear, 'Tất cả');
                        return "<span class='label label-primary'>{$schoolYears}</span>";
                    }
                    else {
                        return '';
                    }
                }, $schoolYears);
                return join('&nbsp;', $schoolYears);

            });
            $grid->status('Trạng thái')->display(function ($status){
                if($status == 1){
                    return "<span class='label label-success'>Đang mở</span>";
                } else {
                    return "<span class='label label-danger'>Đang đóng</span>";

                }
            });
            $grid->created_at('Tạo vào lúc');
            $grid->updated_at('Cập nhật vào lúc');
            $grid->actions(function ($actions) {
                $actions->append('<a href="/admin/time-register/'.$actions->getKey().'/details"><i class="fa fa-eye"></i></a>');
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(TimeRegister::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('name', 'Tên')->rules('required');
            $form->datetimeRange('time_register_start', 'time_register_end', 'Thời gian đăng ký')
                ->rules('required');
            $options = [0 => 'Học kỳ hè', 1 => 'Học kỳ 1', 2 => 'Học kỳ 2'];
            $form->select('semester', 'Học kỳ')->options($options);
            $schoolYear = StudentUser::distinct('school_year')->orderBy('school_year', 'DESC')->limit(6)->pluck('school_year', 'school_year')->toArray();
            $schoolYear['0'] = "Tất cả";
            ksort($schoolYear);
//            dd($schoolYear);
            $form->multipleSelect('school_year','Khóa đăng ký')->options($schoolYear);
            $states = [
                'on'  => ['value' => 1, 'text' => 'Mở', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => 'Đóng', 'color' => 'danger'],
            ];
            $form->switch('status', 'Trạng thái')->states($states)->default('0');
            $form->saving(function (Form $form) {
                if($form->school_year['0'] == "0" || $form->school_year['0'] == null  ) {
                    $form->school_year = 'All';
                }
            });
            $currentPath = Route::getFacadeRoot()->current()->uri();
            if($currentPath == "admin/time-register/{time_register}/edit") {
                $form->number('credits_max', 'Số tín chỉ lớn nhất')->rules('integer|max:28');
                $form->number('credits_min', 'Số tín chỉ nhỏ nhất')->rules('integer|min:1');
            } else {
                $form->hidden('credits_min')->value(10);
                $form->hidden('credits_max')->value(28);
            }
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');

        });
    }

    protected function details($id){
        return Admin::content(function (Content $content) use ($id) {
            $time = TimeRegister::findOrFail($id);
            $content->header('TG Đăng ký');
            $content->description($time->name);
            $content->body($this->detailsView($id));
        });
    }

    protected function detailsView($id)
    {
        $form = $this->form()->view($id);
        return view('vendor.details',
            [
                'template_body_name' => 'admin.TimeRegister.info',
                'form' => $form,
            ]
        );
    }

}
