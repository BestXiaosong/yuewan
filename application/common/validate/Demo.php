<?php
namespace app\common\validate;

use think\Validate;

class Demo extends Validate
{
    protected $rule = [
        'cate_name|分类名为必填'  =>  'require',
    ];
//    手机号码 /^1[34578]{1}[0-9]{9}$/




}