<?php
namespace app\common\validate;

use think\Validate;

class SaleSuccess extends Validate
{
    protected $rule = [
//        'title|简介'  =>  'require',
        'type|拍卖品类型'  =>  'require',
        'g_id|可拍卖品'  =>  'require',
        'start_time|开始时间'  =>  'require',
        'end_time|结束时间'  =>  'require',
        'money|竞拍价格'  =>  'require',
    ];
}