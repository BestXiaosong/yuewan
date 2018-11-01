<?php
namespace app\common\validate;

use think\Db;
use think\Validate;

class Coin extends Validate
{
    protected $rule = [
        'coin_name|货币名'  =>  'require|checkName',
        'red_mini|发红包最低金额'  =>  'require',
        'img|货币图标'  =>  'require',
        'length|小数位数'  =>  'require|between:0,5',
        'status|发行'  =>  'number|between:0,1',
    ];

    /**
     * 验证昵称是否允许使用
     */
    protected function checkName($value,$rule,$data)
    {
        if ($data['id']){
            $map['coin_id'] = ['neq',$data['id']];
        }
        $map['coin_name'] = $value;

        $name = Db::name('coin')->where($map)->value('coin_id');
        if ($name){
            return '该昵称已存在,不能使用';
        }
        return true;
    }



}