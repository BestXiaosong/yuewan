<?php
namespace app\common\model;



use think\Model;

class RechargeConfig extends Model
{
    public function getList($where = []){
        return $this
            ->where($where)
            ->order('sort')
            ->paginate('',false,['query'=>request()->param()]);
    }


    public function getRows($map = [])
    {
        return $this
            ->where($map)
            ->field('r_id,price,money')
            ->order('sort')
            ->select();
    }

}