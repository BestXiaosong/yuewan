<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/26
 * Time: 15:21
 */

namespace app\common\validate;


use think\Validate;

class Order extends Validate
{
    protected $rule = [
        'order_time|预约时间'  =>  'require|checkDate',

    ];

    protected $scene = [
        'add'=> ['room_name','cid','detail','is_close','brief'],
        'edit'=> ['cid'],
    ];
    protected function checkDate($value)
    {
        $time = strtotime($value);

        if (false === $time) return '时间格式错误';

        if ($time < time()+60*30) return '预约时间需大于当前时间30分钟以上';

        return true;

    }


}