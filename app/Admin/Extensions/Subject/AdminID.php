<?php
namespace App\Admin\Extensions\Subject;
use Encore\Admin\Admin;
use Closure;

class AdminID extends Admin {

    public function form($model, Closure $callable)
    {
        return new FormID($this->getModel($model), $callable);
    }
}