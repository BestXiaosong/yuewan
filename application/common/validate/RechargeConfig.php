<?php
namespace app\common\validate;

use think\Validate;

class RechargeConfig extends Validate
{
    protected $rule = [
        'price|价格'  =>  'require|number',
        'money|到账金额'  =>  'require|number',
        'sort|排序'  =>  'require|lt:100',

    ];
}