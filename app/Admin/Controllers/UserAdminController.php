<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ModelFormCustom;
use App\Models\ClassSTU;
use App\Models\UserAdmin;
use App\Models\SubjectGroup;

use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Role;
use Encore\Admin\Controllers\UserController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Route;

class UserAdminController extends UserController
{
    use ModelFormCustom;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('Tài khoản');
            $content->description(trans('admin.list'));
            $content->body($this->grid()->render());
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

            $content->header('Tài khoản');
            $admin = UserAdmin::find($id);
            $content->description($admin->name);

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

            $content->header('Tài khoản');
            $content->description('Tạo tài khoản');

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
        return Admin::grid(UserAdmin::class,function (Grid $grid) {
//            $grid->id('ID')->sortable();
            $currentPath = Route::getFacadeRoot()->current()->uri();
            if ($currentPath == 'admin/teacher_user') {
                $grid->model()->where('type_user', 0);
            } else if($currentPath == 'admin/user_admin') {
                $grid->model()->where('type_user', 1);
            }
            $grid->rows(function (Grid\Row $row) {
                $row->column('number', $row->number);
            });
            $grid->number('STT');
//            $grid->code_number('Mã số');
            $grid->username(trans('admin.username'))->sortable();
            $grid->name(trans('admin.name'))->sortable();
            $grid->roles(trans('admin.roles'))->pluck('name')->label()->sortable();
            $grid->email('Email')->sortable();
            if ($currentPath == 'admin/teacher_user') {
                $grid->column('Lớp')->display(function ($id) {
                    $idTeacher = $this->id;
                    $arrClassName = ClassSTU::where('id_user_teacher', $idTeacher)->pluck('name')->toArray();
                    $arrClassName = array_map(function ($arrClassName){
                        if($arrClassName) {
                            return "<span class='label label-primary'>{$arrClassName}</span>"  ;
                        } else {
                            return '';
                        }
                    },$arrClassName);
                    return join('&nbsp;', $arrClassName);
                })->sortable();
            }
            $grid->type_user('Loại tài khoản')->display(function ($typeUser){
                if($typeUser == 0) {
                    return 'Giảng viên';
                } else if($typeUser == 1) {
                    return 'Quản trị';
                }
            })->sortable();
            $grid->created_at(trans('admin.created_at'))->sortable();
            $grid->updated_at(trans('admin.updated_at'))->sortable();

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $user = UserAdmin::find($actions->getKey());
                $roleUser = $user->roles()->first();
//                $role = ;
                if(!empty($roleUser->slug)) {
                    $role = $roleUser->slug;
                    if ($role == "administrator") {
                        $actions->disableDelete();
                    }
                }

            });

            $grid->tools(function (Grid\Tools $tools) {
                $tools->batch(function (Grid\Tools\BatchActions $actions) {
                    $actions->disableDelete();
                });
            });
            $grid->filter(function ($filter){
                $filter->disableIdFilter();
                $filter->like('user_name', 'Tên đăng nhập');
                $filter->like('name', 'Tên');
                $filter->like('email', 'Email');
                $currentPath = Route::getFacadeRoot()->current()->uri();
                if ($currentPath == 'admin/teacher_user') {
                    $filter->where(function ($query) {
                        $input = $this->input;
                        $arrTeacher = ClassSTU::where('id', $input)->pluck('id_user_teacher')->toArray();
                        $query->whereIn('id', $arrTeacher);
                    }, 'Lớp')->select(ClassSTU::all()->pluck('name', 'id'));
                }
                if ($currentPath == 'admin/all_user') {
                   $filter->equal('type_user', 'Loại tài khoản')->radio([0 => 'Giảng viên', 1=> 'Quản trị']);
                }
                $filter->between('created_at', 'Tạo vào lúc')->datetime();
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        return Admin::form(UserAdmin::class, function (Form $form) {
            $script = <<<EOT
        $(function () {
            $("label.radio-inline ,.iCheck-helper").on("click",function(){
                       var value_target =  $("input[name='type_user']:checked").val();
                      if(value_target =='0')
                      {
                         $('.id_class').parent().parent().css("display", "block");
                     }else{
                         if(value_target =='1'){
                             $('.id_class').parent().parent().css("display", "none");
                           }
                     }
                        
                });
            });
EOT;
            Admin::script($script);

            $form->display('id', 'ID');
            $currentPath = Route::getFacadeRoot()->current()->uri();
//            if($currentPath == "admin/teacher_user/create") {
//                $form->text('code_number', 'Mã GV')->rules('required');
//            }
            $form->text('username', trans('admin.username'))->rules('required');
            $form->text('name', trans('admin.name'))->rules('required');
            $form->email('email', 'Email');
            $form->image('image', trans('admin.avatar'));
            $form->password('password', trans('admin.password'))->rules('required|confirmed');
            $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
                ->default(function ($form) {
                    return $form->model()->password;
                });

            $form->ignore(['password_confirmation']);
            $form->radio('type_user', 'Loại tài khoản')->options([0 => 'Giảng viên', 1=> 'Quản trị']);

//            $form->select('id_class', 'Lớp')->options(ClassSTU::all()->pluck('name', 'id'))
//                ->setElementClass('id_class');
            $form->multipleSelect('roles', trans('admin.roles'))->options(Role::all()->pluck('name', 'id'));
            $form->multipleSelect('permissions', trans('admin.permissions'))->options(Permission::all()->pluck('name', 'id'));
//            $form->hidden('code_number');
            $form->display('created_at', trans('admin.created_at'));
            $form->display('updated_at', trans('admin.updated_at'));
//            $form->saving(function (Form $form) {
//                if ($form->password && $form->model()->password != $form->password) {
//                    $form->password = bcrypt($form->password);
//                }
//            });

            //random code_number teacher & admin
//            $form->saving(function (Form $form) {
//                if($form->type_user == 0) {
//                    $code = 'GV';
//                    $count = UserAdmin::where('type_user', 0)->get()->count();
//                    $form->code_number = $code . '500'. ($count + 1);
//                } else if($form->type_user == 1) {
//                    $code = 'QT';
//                    $count = UserAdmin::where('type_user', 1)->get()->count();
//                    $form->code_number = $code . '00'. ($count + 1);
//                }
//            });
        });
    }
}
