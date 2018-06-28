<?php

namespace App\Admin\Controllers;

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
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header(trans('admin.administrator'));
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
            $grid->id('ID')->sortable();
            $currentPath = Route::getFacadeRoot()->current()->uri();
            if ($currentPath == 'admin/teacher_user') {
                $grid->model()->where('type_user', 0);
            } else if($currentPath == 'admin/user_admin') {
                $grid->model()->where('type_user', 1);
            }
            $grid->code_number('Mã số');
            $grid->username(trans('admin.username'));
            $grid->name(trans('admin.name'));
            $grid->roles(trans('admin.roles'))->pluck('name')->label();
            $grid->email('Email');
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
                });
            }
            $grid->type_user('Loại tài khoản')->display(function ($typeUser){
                if($typeUser == 0) {
                    return 'Giảng viên';
                } else if($typeUser == 1) {
                    return 'Quản trị';
                }
            });
            $grid->created_at(trans('admin.created_at'));
            $grid->updated_at(trans('admin.updated_at'));

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                if ($actions->getKey() == 3) {
                    $actions->disableDelete();
                }
            });

            $grid->tools(function (Grid\Tools $tools) {
                $tools->batch(function (Grid\Tools\BatchActions $actions) {
                    $actions->disableDelete();
                });
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
            $form->text('username', trans('admin.username'))->rules('required');
            $form->text('name', trans('admin.name'))->rules('required');
            $form->email('email', 'Email');
//            $form->image('avatar', trans('admin.avatar'));
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
            $form->hidden('code_number');
            $form->display('created_at', trans('admin.created_at'));
            $form->display('updated_at', trans('admin.updated_at'));
//            $form->saving(function (Form $form) {
//                if ($form->password && $form->model()->password != $form->password) {
//                    $form->password = bcrypt($form->password);
//                }
//            });
            $form->saving(function (Form $form) {
                if($form->type_user == 0) {
                    $code = 'GV';
                    $count = UserAdmin::where('type_user', 0)->get()->count();
                    $form->code_number = $code . '500'. ($count + 1);
                } else if($form->type_user == 1) {
                    $code = 'QT';
                    $count = UserAdmin::where('type_user', 1)->get()->count();
                    $form->code_number = $code . '00'. ($count + 1);

                }
            });


//            $form->saving(function (Form $form) {
//                $count = StudentUser::where('school_year', $form->school_year )->count();
//                $year = $form->school_year;
//                $year = substr( $year, 2, 2);
//                $form->code_number = $form->level . '5' . $year . '00'. ($count + 1);
//            });
        });
    }
}
