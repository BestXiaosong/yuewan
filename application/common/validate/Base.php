<?php
namespace app\common\validate;

use think\Db;
use think\Validate;

class Base extends Validate
{
    protected $rule = [
        'nick_name'  =>  'require|length:6,12|chsAlphaNum|checkName',
        'password'  =>  'require|length:6,12',
        'trade_password'  =>  'require|length:6,12',
        'trade_password|交易密码'  =>  'require|length:6,12',
        'header_img' =>  'require|checkImg',
        'day' =>  'require|day',
        'content|评论' =>  'require|length:1,59',
        'real_name|真实姓名' =>  'require|length:2,25',
        'bid' =>  'debar',
        'mobile|手机号码' =>  'require|/^1[345789]{1}[0-9]{9}$/',
        'phone|手机号码' =>  'require|/^1[345789]{1}[0-9]{9}$/',
        'ID|身份证号码' =>  "require|IDCard",
        'bank_num|银行卡号' =>  "require|bankCard",
        'msg|备注信息' =>  'length:4,30',
        'cid'=>'debar',
        'gid'=>'require|goods',
        'grade'=>'require|in:1,2,3',
        'guess_ratio|竞猜抽成比例'=>'integer|between:1,99',
        'compare_num|人脸比对百分比'=>'integer|between:0,100',
    ];

    protected $message = [
        'nick_name.length'  =>  '昵称为6到12位中文或字母的组合',
        'nick_name.chsDash'  =>  '昵称只能是汉字、字母和数字',
        'nick_name.require'  =>  '昵称不能为空',
        'content.length'  =>  '评论为1到59个字符',
        'header_img.require'  =>  '图片不能为空',
        'day.require' => '考试提醒时间不能为空',
        'grade.in' => '评分类型错误',
        'grade.require' => '评分内容不能为空',
        'gid.require' => '商品id不能为空',
        'content.require' => '评价内容不能为空',
    ];

    protected $scene = [
        'extend' => ['guess_ratio','compare_num'],
        'nick_name'   =>  ['nick_name'],
        'front_user_add'   =>  ['phone'],
        'header_img'=> ['header_img'],
        'edit'=> ['header_img','nick_name','day'],
        'day'=> ['day'],
        'msg'=> ['msg'],
        'content'=> ['content'],
        'bankAdd' => ['real_name','ID','bid','mobile','bank_num','bank_name'],
        'addComment' => ['cid','gid','grade','content'],
        //根据密钥修改密码验证
        'key' => ['password'],
        'setKey' => ['phone','password','trade_password'],
    ];

    /**
     * 验证昵称是否允许使用
     */
    protected function checkName($value)
    {
        $name = Db::name('user')->where('nick_name',$value)->value('user_id');
        if ($name){
            return '该昵称已存在,不能使用';
        }else{
            if (filterWord($value,'nick_name')) return '该昵称不可用';
        }
        return true;
    }



    /**
     * 验证商品是否正常存在
     */
    protected function goods($value,$rule,$data){
        if (!is_numeric($value)) return '商品id不能为空';
        $where['status'] = 1;
        $where['gid'] = $value;
        if ($data['type'] == 'goods'){
            if (!empty(Db::name('goods')->where($where)->value('gid'))){
                return true;
            }else{
                return '非法参数(2101)';
            }
        }elseif ($data['type'] == 'card'){
            if (!empty(Db::name('card')->where($where)->value('gid'))){
                return true;
            }else{
                return '非法参数(2101)';
            }
        }else{
            return '评价类型错误';
        }
    }




    protected function password($value){
        $length = strlen($value);
        echo $length;exit;
    }


    /**
     * 检查文件是否存在于框架中
     */
    protected function checkImg($value)
    {
        return file_exists('.'.DS.$value) ? true : '图片地址错误';
    }

    /**
     * 银行卡验证
     */
    protected function bankCard($card_number){
        $arr_no = str_split($card_number);
        $last_n = $arr_no[count($arr_no)-1];
        krsort($arr_no);
        $i = 1;
        $total = 0;
        foreach ($arr_no as $n){
            if($i%2==0){
                $ix = $n*2;
                if($ix>=10){
                    $nx = 1 + ($ix % 10);
                    $total += $nx;
                }else{
                    $total += $ix;
                }
            }else{
                $total += $n;
            }
            $i++;
        }
        $total -= $last_n;
        $x = 10 - ($total % 10);
        if($x == $last_n){
            return true;
        }else{
            return '银行卡格式不正确';
        }
    }

    /**
     * 字段排除   添加时不允许存在主键时使用
     */
    protected function debar($value){
        return !isEmpty($value)?'非法参数':true;
    }

    /**
     * 检查时间
     */
    protected function day($value){
        return $value > time() && count($value) < 11 ? true : '考试提醒时间需要大于当前时间';
    }

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