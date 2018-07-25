<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */


use App\Admin\Extensions\Form\CKEditor;
use Encore\Admin\Form;
use Encore\Admin\Facades\Admin;


Encore\Admin\Form::forget(['map', 'editor']);
//Admin::js('public/vendor/laravel-admin/chartjs/chart.js');

Form::extend('ckeditor', CKEditor::class);