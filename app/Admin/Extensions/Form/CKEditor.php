<?php

namespace App\Admin\Extensions\Form;

use Encore\Admin\Form\Field;

class CKEditor extends Field
{
    public static $js = [
        '/packages/ckeditor/ckeditor.js',
        '/packages/ckeditor/adapters/jquery.js',
        '/packages/ckfinder/ckfinder.js',
    ];

    protected $view = 'admin.ckeditor';

    public function render()
    {
//        $this->script = "$('textarea.{$this->getElementClass()}').ckeditor();";
        $this->script = "$('textarea.{$this->getElementClass()[0]}').ckeditor();";
        return parent::render();
    }
}