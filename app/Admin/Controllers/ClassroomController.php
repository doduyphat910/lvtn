<?php

namespace App\Admin\Controllers;

use App\Models\Classroom;

use App\Models\SubjectRegister;
use App\Models\Subjects;
use App\Models\TimeRegister;
use App\Models\TimeStudy;
use App\Models\UserAdmin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ClassroomController extends Controller
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

            $content->header('Khoa, lớp');
            $content->description('Danh sách phòng học');

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

            $content->header('Phòng học');
            $content->description('Thêm phòng học');

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
        return Admin::grid(Classroom::class, function (Grid $grid) {
            $grid->rows(function (Grid\Row $row) {
                $row->column('number', $row->number);
            });
            $grid->number('STT');
//            $grid->id('ID')->sortable();
            $grid->name('Tên')->display(function ($name){
                return  '<a href="/admin/class_room/' . $this->id . '/details">'.$name.'</a>';
            })->sortable();
            $grid->actions(function ($actions) {
                $actions->append('<a href="/admin/class_room/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
            });
            $grid->created_at('Tạo vào lúc')->sortable();
            $grid->updated_at('Cập nhật vào lúc')->sortable();
            $grid->filter(function ($filter){
                $filter->disableIdFilter();
                $filter->like('name', 'Tên');
                $filter->between('created_at', 'Tạo vào lúc')->datetime();
            });
        });
    }

    protected function gridSubjectRegister($idSubjectRegister)
    {
        return Admin::grid(SubjectRegister::class, function (Grid $grid) use ($idSubjectRegister) {
//            $grid->resource('admin/subject-register');
            $grid->model()->whereIn('id', $idSubjectRegister)->orderBy('id_time_register', 'DESC');
//            $grid->id('ID')->sortable();
            $grid->rows(function (Grid\Row $row) {
                $row->column('number', $row->number);
            });
            $grid->number('STT');
            $grid->id('Mã học phần');
            $grid->id_subjects('Môn học')->display(function ($idSubject){
                if($idSubject){
                    return Subjects::find($idSubject)->name;
                } else {
                    return '';
                }
            });

            $grid->column('Buổi học')->display(function () {
                $day = TimeStudy::where('id_subject_register', $this->id)->pluck('day')->toArray();
                $day = array_map(function ($day) {
                    switch ($day) {
                        case 2:
                            $day = 'Thứ 2';
                            break;
                        case 3:
                            $day = 'Thứ 3';
                            break;
                        case 4:
                            $day = 'Thứ 4';
                            break;
                        case 5:
                            $day = 'Thứ 5';
                            break;
                        case 6:
                            $day = 'Thứ 6';
                            break;
                        case 7:
                            $day = 'Thứ 7';
                            break;
                        case 8:
                            $day = 'Chủ nhật';
                            break;
                    }
                    return "<span class='label label-success'>{$day}</span>";
                }, $day);
                return join('&nbsp;', $day);
            })->sortable();
            $grid->column('Thời gian học')->display(function () {
                $timeStart = TimeStudy::where('id_subject_register', $this->id)->pluck('time_study_start')->toArray();
                $timeEnd = TimeStudy::where('id_subject_register', $this->id)->pluck('time_study_end')->toArray();
                $time = array_map(function ($timeStart, $timeEnd) {
                    return "<span class='label label-success'>{$timeStart} - {$timeEnd}</span>";
                }, $timeStart, $timeEnd);
                return join('&nbsp;', $time);
            })->sortable();
            $grid->id_user_teacher('Giảng viên')->display(function ($id_user_teacher){
                if($id_user_teacher){
                    $teacher = UserAdmin::find($id_user_teacher);
                    if($teacher){
                        return $teacher->name;
                    } else {
                        return '';
                    }
                } else {
                    return '';
                }
            })->sortable();
            $grid->id_time_register('Đợt đăng ký')->display(function ($idTimeRegister){
                $timeRegister = TimeRegister::find($idTimeRegister);
                if(!empty($timeRegister->name)){
                    if($idTimeRegister % 2 == 0) {
                        return "<span class='label label-info'>{$timeRegister->name}</span>";
                    } else {
                        return "<span class='label label-success'>{$timeRegister->name}</span>";
                    }
                } else {
                    return '';
                }
            })->sortable();
            $grid->qty_current('Số lượng hiện tại')->sortable();
//            $grid->qty_min('Số lượng tối thiểu');
//            $grid->qty_max('Số lượng tối đa');

            $grid->date_start('Ngày bắt đầu')->sortable();
            $grid->date_end('Ngày kết thúc')->sortable();

            $grid->created_at('Tạo vào lúc')->sortable();
            $grid->updated_at('Cập nhật vào lúc')->sortable();

            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->filter(function($filter){
                $filter->disableIdFilter();
                $filter->like('id', 'Mã học phần');
//                $filter->in('id_subjects', 'Tên môn học')->multipleSelect(Subjects::all()->pluck('name', 'id'));
                $filter->where(function ($query) {
                    $input = $this->input;
                    $query->whereIn('id_subjects', $input);
                }, 'Tên môn học')->multipleSelect(Subjects::all()->pluck('name', 'id'));
                $filter->in('id_user_teacher', 'Giảng viên')->multipleSelect(UserAdmin::where('type_user', 0)->pluck('name', 'id'));
                $filter->in('id_time_register', 'TG Đăng ký')->multipleSelect(TimeRegister::all()->pluck('name','id'));
                $filter->like('qty_current', 'SL hiện tại');
                $filter->date('date_start', 'Ngày bắt đầu');
                $filter->date('date_end', 'Ngày kết thúc');
                $filter->between('created_at', 'Tạo vào lúc')->datetime();
            });
            $grid->actions(function ($actions) {
                $actions->disableEdit();
                $actions->disableDelete();
                $actions->append('<a href="/admin/subject_register/' . $actions->getKey() . '/edit"><i class="fa fa-edit" ></i></a>');
                $actions->append('<a href="/admin/subject_register/' . $actions->getKey() . '/details"><i class="fa fa-eye"></i></a>');
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
        return Admin::form(Classroom::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('name', 'Tên phòng')->rules(function ($form){
                return 'required|unique:class_room,name,'.$form->model()->id.',id';
            });

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    public function details($id){
        return Admin::content(function (Content $content) use ($id) {
            $classRoom = Classroom::findOrFail($id);
            $content->header('Phòng học');
            $content->description($classRoom->name);
            $content->body($this->detailsView($id));
        });
    }

    public function detailsView($id){
        $form = $this->form()->view($id);
        $idSubjectRegister = TimeStudy::where('id_classroom', $id)->pluck('id_subject_register');
        $gridSubjectRegister = $this->gridSubjectRegister($idSubjectRegister)->render();
        return view('vendor.details',
            [
                'template_body_name' => 'admin.ClassRoom.info',
                'form' => $form,
                'gridSubjectRegister' => $gridSubjectRegister

            ]
        );
    }
}
