<?php
namespace App\Http\Extensions\Information;
use Encore\Admin\Form;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Encore\Admin\Admin;

use Illuminate\Support\Facades\Redirect;


class FormInformation extends Form {
    public function update($id, $data = null)
    {
        $data = ($data) ?: Input::all();
        $isEditable = $this->isEditable($data);

        $data = $this->handleEditable($data);

        $data = $this->handleFileDelete($data);
        if(strlen($data['password']) < 6) {
            ?>
            <script>
                swal({
                    title: 'Lỗi',
                    text: "Mật khẩu phải ít nhất 6 kí tự",
                    type: 'error',
                    confirmButtonColor: '#3085d6',
                }, function() {
                    location.reload();
                });
            </script>
            <?php

        } else {
            if($data['password'] != $data['password_confirmation']) {
                ?>
                <script>
                    swal({
                        title: 'Lỗi',
                        text: "Mật khẩu và xác nhận mật khẩu phải giống nhau",
                        type: 'error',
                        confirmButtonColor: '#3085d6',
                    }, function() {
                        location.reload();
                    });
                </script>
                <?php

            } else {
                if ($this->handleOrderable($id, $data)) {
                    return response([
                        'status'  => true,
                        'message' => trans('admin.update_succeeded'),
                    ]);
                }
                /* @var Model $this->model */
                $this->model = $this->model->with($this->getRelations())->findOrFail($id);

                $this->setFieldOriginalValue();

                // Handle validation errors.
                if ($validationMessages = $this->validationMessages($data)) {
                    if (!$isEditable) {
                        return back()->withInput()->withErrors($validationMessages);
                    } else {
                        return response()->json(['errors' => array_dot($validationMessages->getMessages())], 422);
                    }
                }

                if (($response = $this->prepare($data)) instanceof Response) {
                    return $response;
                }

                DB::transaction(function () {
                    $updates = $this->prepareUpdate($this->updates);

                    foreach ($updates as $column => $value) {
                        /* @var Model $this->model */
                        $this->model->setAttribute($column, $value);
                    }

                    $this->model->save();

                    $this->updateRelation($this->relations);
                });

                if (($result = $this->complete($this->saved)) instanceof Response) {
                    return $result;
                }

                if ($response = $this->ajaxResponse(trans('admin.update_succeeded'))) {
                    return $response;
                }
                admin_toastr('Cập nhật thành công');
//            return $this->redirectAfterUpdate();
            }
        }
    }
}
?>