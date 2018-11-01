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

class Role extends Validate
{
    protected $rule = [
        'role_name|角色名'  =>  'require|length:6,12|checkName',
        'birthday|出生日期'  =>  'dateFormat:Y-m-d',
        'place|所在地区'  =>  'chs',
        'sign|签名'  =>  'max:60',
        'first_time|第一次接触区块链的时间'  =>  'dateFormat:Y-m-d',
        'sex|性别为必选项'  =>  'in:0,1',
    ];

    protected $scene = [
        'add_role'=> ['role_name','birthday','sex'],
        'edit' => ['place','sign','birthday','first_time','sex'],
    ];





    protected function checkName($value)
    {
        $name = Db::name('role')->where('role_name',$value)->value('role_id');
        if ($name){
            return '该角色名已存在,不能使用';
        }else{
            $str = filterWord($value,'role_name');
            if ($str) return '角色名含有非法字符:'.$str;
        }
        return true;
    }

}