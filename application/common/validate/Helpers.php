<?php
namespace app\common\validate;

use think\Validate;

class Helpers extends Validate
{
    protected $rule = [
        'img|图标'  =>  'require',
        'title|标题'  =>  'require|max:25',
        'brief|简介'  =>  'require|max:25',
        'content|详情'  =>  'require',
    ];
}