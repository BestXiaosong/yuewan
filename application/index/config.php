<?php
return [
    'view_replace_str' => [
        '__PUBLIC__' => '/public/index',
        '__IMG__' => '/public/index/img',
        '__IMG2__' => '/public/index/img2',
        '__JS__' => '/public/index/js',
        '__CSS__' => '/public/index/css',
        '__ROOT__' => '/',
        '__UPLOAD__' => config('upload'),
        '__api_domin__' => config('upload'),
    ],
    'token_expire' => 2592000,
    //coding文章
    'coding_access_key' => '991c090b3badb180c6971d0679c00018',
    'coding_secret_key' => '67ad99bddd3b0d61',
    //红包领取redis配置
    'redis_ip'=> \think\Env::get('host','39.108.192.61'),
    'redis_pwd'=> \think\Env::get('password','39.108.192.60'),
    'redis_port'=> \think\Env::get('port','39.108.192.60'),
    'default_filter'         => 'strip_tags',
    // 默认控制器
    'default_controller'     => 'Web',

];