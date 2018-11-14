<?php
namespace app\common\validate;

use think\Db;
use think\Validate;

class Skill extends Validate
{
    protected $rule = [
        'skill_name|技能名'  =>  'require|Name',
        'sort|排序'  =>  'require|<:100',
        'img|图片地址'  =>  'require',
    ];

    /**
     * 验证技能名是否可用
     */
    protected function Name($value,$rule,$data){

        $table = 'skill';

        $pk = Db::name($table)->getPk();

        $map['skill_name'] = $value;
        if (is_numeric($data['id'])){
            $map[$pk] = ['neq',$data['id']];
        }

        $have = Db::name($table)->where($map)->value($pk);

        return !$have ? true:'该技能名已存在!';

    }




}