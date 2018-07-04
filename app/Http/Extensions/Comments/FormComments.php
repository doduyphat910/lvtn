<?php

namespace App\Http\Extensions\Comments;

use App\Models\TimeRegister;
use App\Models\UserSubject;
use Encore\Admin\Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Encore\Admin\Admin;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Redirect;


class FormComments extends Form
{
    public function store()
    {
        $data = Input::all();
        $currentPath = Route::getFacadeRoot()->current()->uri();
        $timeRegister = TimeRegister::where('status', 1)->orderBy('id', 'DESC')->first();
        $user = Auth::user();
        $countUserRequest = UserSubject::where('id_time_register', $timeRegister->id)->where('id_user', $user->id)
                            ->where('id_subject', $data['id_subject'])->count();
        if ($currentPath == "user/user-subject") {
            if (empty($data['id_subject'])) {
                ?>
                <script>
                    swal({
                        title: 'Lỗi',
                        text: "Môn học trống",
                        type: 'error',
                        confirmButtonColor: '#3085d6',
                    }, function () {
                        location.reload();
                    });
                </script>
                <?php
            } elseif ($countUserRequest > 0 ) {
                ?>
                <script>
                    swal({
                        title: 'Lỗi',
                        text: "Bạn đã yêu cầu môn học này",
                        type: 'error',
                        confirmButtonColor: '#3085d6',
                    }, function () {
                        location.reload();
                    });
                </script>
                <?php
            }
            else {
                // Handle validation errors.
                if ($validationMessages = $this->validationMessages($data)) {
                    return back()->withInput()->withErrors($validationMessages);
                }

                if (($response = $this->prepare($data)) instanceof Response) {
                    return $response;
                }

                DB::transaction(function () {
                    $inserts = $this->prepareInsert($this->updates);

                    foreach ($inserts as $column => $value) {
                        $this->model->setAttribute($column, $value);
                    }

                    $this->model->save();

                    $this->updateRelation($this->relations);
                });

                if (($response = $this->complete($this->saved)) instanceof Response) {
                    return $response;
                }

                if ($response = $this->ajaxResponse(trans('admin.save_succeeded'))) {
                    return $response;
                }
                admin_toastr('Lưu thành công');
                // return $this->redirectAfterStore();
            }
        } elseif ($currentPath == "user/comments") {
            if (empty($data['name']) || empty($data['description'])) {
                ?>
                <script>
                    swal({
                        title: 'Lỗi',
                        text: "Có trường để trống",
                        type: 'error',
                        confirmButtonColor: '#3085d6',
                    }, function () {
                        location.reload();
                    });
                </script>
                <?php
            } else {
                // Handle validation errors.
                if ($validationMessages = $this->validationMessages($data)) {
                    return back()->withInput()->withErrors($validationMessages);
                }

                if (($response = $this->prepare($data)) instanceof Response) {
                    return $response;
                }

                DB::transaction(function () {
                    $inserts = $this->prepareInsert($this->updates);

                    foreach ($inserts as $column => $value) {
                        $this->model->setAttribute($column, $value);
                    }

                    $this->model->save();

                    $this->updateRelation($this->relations);
                });

                if (($response = $this->complete($this->saved)) instanceof Response) {
                    return $response;
                }

                if ($response = $this->ajaxResponse(trans('admin.save_succeeded'))) {
                    return $response;
                }
                admin_toastr('Lưu thành công');
                // return $this->redirectAfterStore();
            }
        }



    }

}

?>