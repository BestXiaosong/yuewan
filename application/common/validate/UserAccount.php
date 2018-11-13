<?php
namespace app\common\validate;

use think\Validate;

class UserAccount extends Validate
{
    protected $rule = [
        'real_name|真实姓名'  =>  'require',
        'account|账号'  =>  'require',
    ];
}