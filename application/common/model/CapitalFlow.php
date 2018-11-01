<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/28
 * Time: 11:35
 */

namespace app\common\model;


use think\Model;

class CapitalFlow extends Model
{
    public function getList($where = []){
        return $this->alias('a')
//            ->join('users u','u.user_id = a.user_id','LEFT')
            ->where($where)
            ->order('a.c_id desc')
            ->field('a.*')
            ->paginate('',false,['query'=>request()->param()]);
    }




}