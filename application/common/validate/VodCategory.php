<?php
namespace app\common\validate;

use think\Validate;

class VodCategory extends Validate
{
    protected $rule = [
        'cate_name|分类名'  =>  'require',
        'img|logo图'  =>  'require',
    ];
}