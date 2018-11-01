<?php
return [
    'view_replace_str' => [
        '__PUBLIC__' => '/public/admin',
        '__IMG__' => '/public/admin/images',
        '__JS__' => '/public/admin/js',
        '__CSS__' => '/public/admin/css',
        '__TREEGRID__' => '/public/admin/treegrid',
        '__TIMEPICKER__' => '/public/admin/timePicker',
        '__UEDITOR__' => '/public/admin/ueditor',
        '__ROOT__' => '/',
        '__UPLOAD__'=>config('upload'),
    ],
    'auth' => [
        'auth_group' => 'cl_auth_group',
        'auth_group_access' => 'cl_auth_group_access',
        'auth_rule' => 'cl_auth_rule'
    ]
];