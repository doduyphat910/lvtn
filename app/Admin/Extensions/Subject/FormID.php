<?php
namespace App\Admin\Extensions\Subject;
use Encore\Admin\Form;
use Closure;
use Encore\Admin\Form\Builder;

class FormID extends Form {

    /**
     * Create a new form instance.
     *
     * @param $model
     * @param \Closure $callback
     */
    public function __construct($model, Closure $callback)
    {
        $this->model = $model;

        $this->builder = new BuilderID($this);

        $callback($this);
    }
}