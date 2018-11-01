<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/30
 * Time: 10:36
 */

namespace app\common\model;


use think\Model;

class RoleName extends Model
{
    public function getName($where = [])
    {
        $rows = $this->where($where)->field('name_id,role_name,type')->select();
        if (empty($rows)) return false;
        foreach ($rows as $v){
            $v['name_id'] = hashid($v['name_id']);
        }
        return $rows;
    }
}