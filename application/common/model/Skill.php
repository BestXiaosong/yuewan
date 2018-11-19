<?php

namespace app\common\model;

use think\Model;

class Skill extends Model
{
    public function getList($where = []){
        return $this
            ->where($where)
            ->order('sort')
            ->paginate('',false,['query'=>request()->param()]);
    }

    public function getDetail($map = [])
    {
        return $this->where($map)->order('sort')->field('skill_id,skill_name,img,status')->find();
    }

    public function getRows($map = [])
    {
        return $this->where($map)->order('sort')->field('skill_id,skill_name,img')->select();
    }



}
