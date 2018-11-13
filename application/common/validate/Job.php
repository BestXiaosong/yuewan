<?php
namespace app\common\validate;

use think\Validate;

class Job extends Validate
{
    protected $rule = [

        'job|工作名'  =>  'require',
        'sort|排序'  =>  '<:100',

    ];
}