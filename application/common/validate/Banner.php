<?php
namespace app\common\validate;

use think\Validate;

class Banner extends Validate
{
    protected $rule = [
//        'title|简介'  =>  'require',
        'cid|图片分类'  =>  'require',
        'img|图片地址'  =>  'require',
        'url|链接地址'  =>  'url',
    ];
}