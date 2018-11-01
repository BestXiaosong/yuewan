<?php
namespace app\common\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'username'  =>  'require|min:5|unique:user',
        'password' =>  'require|min:6',
    ];

    protected $message = [
        'username.require'  =>  '请输入用户名',
        'username.min'  =>  '用户名长度至少5位',
        'username.unique'  =>  '用户名已存在',
        'password.require' =>  '请输入密码',
        'password.min' =>  '密码长度至少6位',
    ];

    protected $scene = [
        'add'   =>  ['username','password'],
    ];
}