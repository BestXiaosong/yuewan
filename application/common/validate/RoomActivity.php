<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 10:40
 */

namespace app\common\validate;


use think\Validate;

class RoomActivity extends Validate
{
    protected $rule = [
        'title|简介'  =>  'require|max:20',
        'start_time|活动开始时间'  =>  'number',
        'img|封面图片'  =>  'require',
        'detail|简介'  =>  'require|max:100',
        'status|活动状态'  =>  'require|in:1,2',
    ];
}