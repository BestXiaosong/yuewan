<?php
/**
 * Created by PhpStorm.
 * User: echo
 * Date: 2018/11/26
 * Time: 11:46
 */

namespace app\common\validate;


use think\Validate;

class Wheat extends Validate
{
    protected $rule = [
        'room_id'  => 'require',
        'wheat_id'   => 'require',
        'type' => 'in:0,1',
        'user_id' => 'requireIf:type,1',
    ];

    protected $message = [
        'room_id.require'  =>  '缺少房间ID',
        'wheat_id.require'  =>  '缺少麦位ID',
        'type.in'  =>  '类型有误',
        'user_id.requireIf'  =>  '缺少用户ID',
    ];

    protected $scene = [
        'up'   =>  ['room_id','wheat_id','type','user_id'],
        'down'  =>['room_id','wheat_id'],
        'lock'  =>['room_id','wheat_id'],
    ];

}