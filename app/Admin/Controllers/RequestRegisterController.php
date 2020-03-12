<?php
namespace App\Admin\Controllers;
use App\Models\ClassSTU;
use App\Models\Rate;

use App\Models\Semester;
use App\Models\StudentUser;
use App\Models\SubjectGroup;
use App\Models\Subjects;
use App\Models\TimeRegister;
use App\Models\UserSubject;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\MessageBag;

class RequestRegisterController extends Controller
{
    use ModelForm;

    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('Học phần');
            $content->description('Danh sách yêu cầu ĐK');

            $content->body($this->grid());
        });
    }

    protected function grid()
    {
        return Admin::grid(UserSubject::class, function (Grid $grid) {
            $subject = UserSubject::select('id_subject')->distinct()->pluck('id_subject')->toArray();
            $grid->model()->select('id_subject','id_time_register')->distinct();
            $grid->rows(function (Grid\Row $row) {
                $row->column('number', $row->number);
            });
            $grid->number('STT');
            $grid->id_subject('Môn học')->display(function ($idSubject) {
                $subject = Subjects::find($idSubject);
                if(!empty($subject->name)){
                    return '<a href="/admin/request-register/' . $idSubject . '/details">'.$subject->name.'</a>';
                } else {
                    return '';
                }
            })->sortable();
            $grid->column('SL yêu cầu')->display(function (){
                $countStudent = UserSubject::distinct('id_user')->where('id_time_register', $this->id_time_register)->where('id_subject', $this->id_subject)
                   ->count();
                return $countStudent;
            });
            $grid->id_time_register('Đợt ĐK')->display(function ($idTimeRegister){
                $timeRegister = TimeRegister::find($idTimeRegister);
                if(!empty($timeRegister->name)){
                    if($idTimeRegister % 2 == 0) {
                        return  "<span class='label label-info'>{$timeRegister->name}</span>";
                    } else {
                        return  "<span class='label label-success'>{$timeRegister->name}</span>";
                    }
                } else {
                    return '';
                }
            })->sortable();
            $grid->actions(function ($actions) {
                $actions->disableEdit();
                $actions->disableDelete();
                $actions->append('<a href="/admin/request-register/' . $actions->row->id_subject . '/details"><i class="fa fa-eye"></i></a>');
            });
//            $grid->created_at('Tạo vào lúc')->sortable();
//            $grid->updated_at('Cập nhật vào lúc')->sortable();
            $grid->filter(function($filter) {
                $filter->disableIdFilter();
                $filter->in('id_subject', 'Môn học')->multipleSelect(Subjects::all()->pluck('name','id'));
                $filter->in('id_time_register', 'Đợt ĐK')->multipleSelect(TimeRegister::all()->pluck('name','id'));
                $filter->between('created_at', 'Tạo vào lúc')->datetime();
            });
            $grid->disableCreateButton();
//            $grid->disableActions();
            $grid->disableExport();
        });
    }

    public function details($id){
        return Admin::content(function (Content $content) use ($id) {
            $subject = Subjects::findOrFail($id);
            $content->header('DS yêu cầu ĐK');
            $content->description($subject->name);
            $content->body($this->detailsView($id));
        });
    }

    public function detailsView($id){
//        $idSubjectRegister = TimeStudy::where('id_classroom', $id)->pluck('id_subject_register');
        $gridStudent = $this->gridStudent($id)->render();
        return view('vendor.details',
            [
                'template_body_name' => 'admin.RequestRegister.info',
                'gridStudent' => $gridStudent

            ]
        );
    }

    protected function gridStudent($idSubject)
    {
        return Admin::grid(UserSubject::class, function (Grid $grid) use ($idSubject) {
            $grid->model()->where('id_subject', $idSubject);
//            $grid->rows(function (Grid\Row $row) {
//                $row->column('number', $row->number);
//            });
//            $grid->number('STT');
            $grid->id_user('MSSV')->display(function ($idUser){
                $user = StudentUser::find($idUser);
                if($user->code_number){
                    return $user->code_number;
                } else {
                    return '';
                }
            })->sortable();
            $grid->column('Họ SV')->display(function (){
                $user = StudentUser::find($this->id_user);
                if($user->first_name){
                    return $user->first_name;
                } else {
                    return '';
                }
            });
            $grid->column('Tên SV')->display(function (){
                $user = StudentUser::find($this->id_user);
                if($user->last_name){
                    return $user->last_name;
                } else {
                    return '';
                }
            });
            $grid->column('Lớp ')->display(function (){
                $user = StudentUser::find($this->id_user);
                if($user->id_class){
                    $class = ClassSTU::find($user->id_class);
                    if($class->name) {
                        return "<span class='label label-info'>{$class->name}</span>";
                    }
                } else {
                    return '';
                }
            });

            $grid->id_subject('Môn học')->display(function ($idSubject) {
                $subject = Subjects::find($idSubject);
                if(!empty($subject->name)){
                    return $subject->name;
                } else {
                    return '';
                }
            })->sortable();
            $grid->id_time_register('Đợt ĐK')->display(function ($idTimeRegister){
                $timeRegister = TimeRegister::find($idTimeRegister);
                if(!empty($timeRegister->name)){
                    if($idTimeRegister % 2 == 0) {
                        return  "<span class='label label-info'>{$timeRegister->name}</span>";
                    } else {
                        return  "<span class='label label-success'>{$timeRegister->name}</span>";
                    }
                } else {
                    return '';
                }
            })->sortable();
            $grid->created_at('Tạo vào lúc')->sortable();
            $grid->updated_at('Cập nhật vào lúc')->sortable();
            $grid->filter(function($filter) {
                $filter->disableIdFilter();
                $filter->like('first_name', 'Họ SV');
                $filter->like('last_name', 'Tên SV');
                $filter->in('id_subject', 'Môn học')->multipleSelect(Subjects::all()->pluck('name','id'));
                $filter->in('id_time_register', 'Đợt ĐK')->multipleSelect(TimeRegister::all()->pluck('name','id'));
                $filter->between('created_at', 'Tạo vào lúc')->datetime();
            });
            $grid->disableRowSelector();
            $grid->disableCreateButton();
            $grid->disableActions();
            $grid->disableExport();
        });
    }
}