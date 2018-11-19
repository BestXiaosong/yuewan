<?php
namespace app\common\validate;

use think\Validate;

class Opinion extends Validate
{
    protected $rule = [
        'type|反馈类型' => 'require',
        'content|反馈内容' => 'require|max:60',
    ];



}