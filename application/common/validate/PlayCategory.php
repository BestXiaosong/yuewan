<?php
namespace app\common\validate;

use think\Validate;

class PlayCategory extends Validate
{
    protected $rule = [
        'cate_name|分类名'  =>  'require',
    ];
}