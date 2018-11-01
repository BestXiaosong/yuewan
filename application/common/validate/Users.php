<?php
namespace app\common\validate;

use think\Validate;

class Users extends Validate
{
    protected $rule = [
        'nick_name|昵称'  =>  'length:2,12',
        'real_name|真实姓名'  =>  'length:2,12',
//        'ID|身份证号码'  =>  'IDCard',
        'header_img|头像'  =>  'require',
        'phone|手机号码'  =>  '/^1[345789]{1}[0-9]{9}$/',
        'password|登录密码'  =>  'length:6,12',
        'trade_password|交易密码'  =>  'length:6,12',
        'sex|性别为必选项'  =>  'require|in:0,1',
//        'region|地区'  =>  'require',
    ];

    protected $scene = [
        'edit'=> ['header_img','nick_name'],
        'back_edit'=> ['header_img','nick_name','phone'],
        'back_add'=> ['header_img','phone','nick_name'],
    ];


    /**
     * 检查文件是否存在于框架中
     */
    protected function checkImg($value)
    {
        return file_exists('.'.DS.$value) ? true : '图片地址错误';
    }




    protected function IDCard($value)
    {
        $preg_card = '/^(^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$)|(^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}[Xx])$)$/';
        if (preg_match($preg_card,$value)) {
            if (strlen($value) == 18) {
                $idCardWi = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
                $idCardY = [1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2];
                $idCardWiSum = 0;
                for ($i = 0; $i < 17; $i++) {
                    $idCardWiSum += substr($value, $i,1) * $idCardWi[$i];
                }
                $idCardMod = $idCardWiSum % 11;//计算出校验码所在数组的位置
                $idCardLast = substr($value, 17);//得到最后一位身份证号码
                //如果等于2，则说明校验码是10，身份证号码最后一位应该是X
                if ($idCardMod == 2) {
                    if ($idCardLast == "X" || $idCardLast == "x") {
                        return true;
                    } else {
                        return "身份证号码错误！";
                    }
                } else {
                    //用计算出的验证码与最后一位身份证号码匹配，如果一致，说明通过，否则是无效的身份证号码
                    if ($idCardLast == $idCardY[$idCardMod]) {
                        return true;
                    } else {
                        return "身份证号码错误！";
                    }
                }
            } else {
                return '身份证格式不正确!';
            }
        }else{
            return '身份证格式不正确';
        }
    }
}