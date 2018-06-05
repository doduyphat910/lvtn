<?php
namespace App\Admin\Extensions\Rate;
use Encore\Admin\Form;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Encore\Admin\Admin;

use Illuminate\Support\Facades\Redirect;


class FormRate extends Form {
    public function store()
    {
        $data = Input::all();
        if ($data['midterm'] + $data['attendance'] + $data['end_term'] != 100) {
            ?>
            <script>
                // swal({
                //     type: 'error',
                //     title: 'Lỗi',
                //     text: 'Tỷ lệ điểm chuyên cần +  giữa kì + cuối kì phải đúng 100!',
                // })
                swal({
                    title: 'Lỗi',
                    text: "Tỷ lệ điểm chuyên cần, giữa kì, cuối kì phải đúng 100!",
                    type: 'error',
                    confirmButtonColor: '#3085d6',
                }, function() {
                    window.location = "/admin/rate/create";
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

            return $this->redirectAfterStore();
        }
    }

}
?>