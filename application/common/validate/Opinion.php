<?php
namespace app\common\validate;

use think\Validate;

class Opinion extends Validate
{
    protected $rule = [
        'e_mail' => 'require|email|max:30',
        'content' => 'require|max:60',
    ];

    protected $message = [
        'e_mail.require' => '邮箱地址必须',
        'e_mail.email' => '邮箱格式不正确',
        'e_mail.max' => '邮箱最多30个字符',

        'content.require' => '反馈内容不能为空',
        'content.max' => '内容最多60个字符',
    ];

}