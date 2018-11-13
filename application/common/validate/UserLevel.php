<?php
namespace app\common\validate;

use think\Validate;

class UserLevel extends Validate
{
    protected $rule = [

        'level|等级'  =>  'require',
        'experience|所需经验'  =>  'require|number',
        'color|颜色代码'  =>  'require',

    ];
}