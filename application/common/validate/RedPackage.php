<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/2 0002
 * Time: 16:50
 */

namespace app\common\validate;


use think\Validate;

class RedPackage extends Validate
{
    protected $rule = [
        'role_id'  =>  'require|number',
        'user_id'  =>  'require|number',
        'to_id'  =>  'require|number',
        'money_type' =>  'require|checkType',
        'msg' =>  'max:35',
        'money' =>  'require|number',
        'red_type'  =>  'require|checkStatus',
        'num' =>  'require|number',
        'luck_king_money' =>  'require|number',
        'expire_time' =>  'require|number',
    ];

    protected $message = [
        'role_id.require'  =>  '角色id必须',
        'role_id.number'  =>  '角色id必须是数字',
        'user_id.require'  =>  '用户id必填',
        'user_id.number'  =>  '用户id必须是数字',
        'to_id.require'  =>  '接收者id必须',
        'to_id.number'  =>  '接收者id必须是数字',
        'money_type.require'  =>  '金币类型必选',
        'msg.max' =>  '消息最多35个字符',
        'money.require' =>  '金币必填',
        'money.number' =>  '金币必须是数字',
        'red_type'=>'红包类型必须',
        'num.require' =>'红包数量必须',
        'num.number'=>'红包数量必须是数字',
        'expire_time'=>'过期时间必须'



    ];

    protected $scene = [
        'add'   =>  ['money_type','money','red_type','num','to_id'],
        'edit'  =>['role_id','user_id','money_type','money','red_type','num','expire_time']
    ];

    public function checkStatus($value){
        if($value==1||$value==2){
            return true;
        }
        return '不合法参数值';
    }

    public function checkType($value){
        if($value==2||$value==1||$value==3){
            return true;
        }
        return '不合法参数值';
    }


}