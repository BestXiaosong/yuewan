<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/26
 * Time: 15:21
 */

namespace app\common\validate;


use think\Db;
use think\Validate;

class Room extends Validate
{
    protected $rule = [
//        'room_name|房间名'  =>  'require|chs|checkName',
        'cid|直播分类' => 'number',
        'detail|房间详情' => 'max:80',
        'is_close|全员禁言' => 'in:0,1',
        'brief|房间简介' => 'max:80',
    ];

    protected $scene = [
        'add_room'=> ['cid','detail','is_close','brief'],
        'edit'=> ['cid'],
    ];


    protected function checkName($value)
    {
        if (strlen($value) > 20*3 || strlen($value) < 10*3) return '房间名为10到20位汉字';
        $name = Db::name('room')->where('room_name',$value)->value('room_id');
        if ($name){
            return '该房间名已存在,不能使用';
        }else{
            $str = filterWord($value,'room_name');
            if ($str) return '房间名含有非法字符:'.$str;
        }
        return true;
    }

}