<?php
namespace app\common\validate;

use think\Validate;

class UserId extends Validate
{
    protected $rule = [
        'ID_num|身份证号码'  =>  'require|IDCard',
        'real_name|姓名'  =>  'require|chs|max:12',
        'face|身份证正面照'  =>  'require',
        'back|身份证反面照'  =>  'require',
        'img|识别照'  =>  'require',
    ];


    /**
     * 身份证号码验证
     */
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