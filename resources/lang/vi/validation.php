<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ' :attribute phải được chấp nhận.',
    'active_url'           => ' :attribute không phải là một URL hợp lệ.',
    'after'                => ' :attribute phải là sau :date.',
    'after_or_equal'       => ' :attribute phải là sau hoặc bằng :date.',
    'alpha'                => ' :attribute chỉ có thể chứa chữ cái.',
    'alpha_dash'           => ' :attribute chỉ có thể chứa chữ cái, số và dấu gạch ngang.',
    'alpha_num'            => ' :attribute chỉ có thể chứa chữ cái và số.',
    'array'                => ' :attribute phải là một mảng.',
    'before'               => ' :attribute phải là trước :date.',
    'before_or_equal'      => ' :attribute phải là trước hoặc bằng :date.',
    'between'              => [
        'numeric' => ' :attribute phải ở giữa :min và :max.',
        'file'    => ' :attribute phải ở giữa :min và :max kilobytes.',
        'string'  => ' :attribute phải ở giữa :min và :max ký tự.',
        'array'   => ' :attribute phải ở giữa mục :min và mục :max.',
    ],
    'boolean'              => ' :attribute trường phải đúng hoặc sai.',
    'confirmed'            => ' :attribute nhận đinh không phù hợp.',
    'date'                 => ' :attribute ngày không hợp lệ.',
    'date_format'          => ' :attribute không khớp với định dạng :format.',
    'different'            => ' :attribute và :other phải khác nhau.',
    'digits'               => ' :attribute :digits phải là số.',
    'digits_between'       => ' :attribute phải ở giữa số :min và :max.',
    'dimensions'           => ' :attribute kích thước hình ảnh không hợp lệ.',
    'distinct'             => ' :attribute trường này có giá trị trùng lặp.',
    'email'                => ' :attribute phải là một địa chỉ email hợp lệ',
    'exists'               => ' Lựa chọn :attribute không hợp lệ.',
    'file'                 => ' :attribute phải là một tệp.',
    'filled'               => ' :attribute trường này phải có giá trị.',
    'image'                => ' :attribute phải là hình ảnh.',
    'in'                   => 'Lựa chọn :attribute không hợp lệ.',
    'in_array'             => ' :attribute trường không tồn tại trong :other.',
    'integer'              => ' :attribute phải là số nguyên',
    'ip'                   => ' :attribute phải là địa chỉ IP hợp lệ.',
    'ipv4'                 => ' :attribute phải là địa chỉ IPv4 hợp lệ.',
    'ipv6'                 => ' :attribute phải là địa chỉ IPv6 hợp lệ.',
    'json'                 => ' :attribute phải là một chuỗi JSON hợp lệ.',
    'max'                  => [
        'numeric' => ' :attribute không thể lớn hơn :max.',
        'file'    => ' :attribute không thể lớn hơn :max kilobytes.',
        'string'  => ' :attribute không thể lớn hơn :max ký tự.',
        'array'   => ' :attribute không thể có nhiều hơn mục :max.',
    ],
    'mimes'                => ' :attribute phải là loại tệp: :values.',
    'mimetypes'            => ' :attribute phải là một loại tệp: :values.',
    'min'                  => [
        'numeric' => ' :attribute ít nhất phải :min.',
        'file'    => ' :attribute ít nhất phải :min kilobytes.',
        'string'  => ' :attribute ít nhất phải :min ký tự.',
        'array'   => ' :attribute phải có ít nhất mục :min.',
    ],
    'not_in'               => 'Lựa chọn :attribute không có hiệu lực.',
    'not_regex'            => ' :attribute định dạng không hợp lệ.',
    'numeric'              => ' :attribute phải là một số.',
    'present'              => ' :attribute field must be present.',
    'regex'                => ' :attribute định dạng không hợp lệ.',
    'required'             => ' :attribute trường này đang để trống.',
    'required_if'          => ' :attribute trường này đang để trống khi :other có :value.',
    'required_unless'      => ' :attribute trường này đang để trống trừ khi :other có trong :values.',
    'required_with'        => ' :attribute trường này bắt buộc khi :values ở hiện tại.',
    'required_with_all'    => ' :attribute trường này bắt buộc khi :values ở hiện tại.',
    'required_without'     => ' :attribute trường này bắt buộc khi :values không ở hiện tại.',
    'required_without_all' => ' :attribute trường này bắt buộc khi không có :values có mặt.',
    'same'                 => ' :attribute và :other phải phù hợp với nhau.',
    'size'                 => [
        'numeric' => ' :attribute cần phải :size.',
        'file'    => ' :attribute cần phải :size kilobytes.',
        'string'  => ' :attribute cần phải :size ký tự.',
        'array'   => ' :attribute phải chứa mục :size.',
    ],
    'string'               => ' :attribute phải là một chuỗi.',
    'timezone'             => ' :attribute phải là một khu vực hợp lệ.',
    'unique'               => ' :attribute đã tồn tại.',
    'uploaded'             => ' :attribute không thể tải lên.',
    'url'                  => ' :attribute định dạng không hợp lệ.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
