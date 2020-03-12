<?php
namespace App\Admin\Extensions\Subject;

use Encore\Admin\Form\Builder;
use Encore\Admin\Form\Field;

class BuilderID extends Builder {
    protected function removeReservedFields()
    {
        if (!$this->isMode(static::MODE_CREATE)) {
            return;
        }

        $reservedColumns = [
            $this->form->model()->getCreatedAtColumn(),
            $this->form->model()->getUpdatedAtColumn(),
        ];

        $this->fields = $this->fields()->reject(function (Field $field) use ($reservedColumns) {
            return in_array($field->column(), $reservedColumns);
        });
    }
}