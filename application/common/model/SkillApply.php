<?php

namespace app\common\model;

use think\Model;

class SkillApply extends Model
{
    public function getList($where = []){
        return $this->alias('a')
            ->join('skill s','s.skill_id = a.skill_id','LEFT')
            ->join('users u','u.user_id = a.user_id','LEFT')
            ->where($where)
            ->field('a.*,s.skill_name,s.img as skill_img,u.nick_name,u.header_img')
            ->order('a.apply_id desc')
            ->paginate('',false,['query'=>request()->param()]);
    }

    public function getDetail($where = [])
    {
        return $this->alias('a')
            ->join('skill s','s.skill_id = a.skill_id','LEFT')
            ->join('users u','u.user_id = a.user_id','LEFT')
            ->where($where)
            ->field('a.*,s.skill_name,s.img as skill_img,u.nick_name,u.header_img')
            ->find();
    }


    public function getMy($map = [])
    {
        return $this->alias('a')
            ->join('skill s','s.skill_id = a.skill_id','LEFT')
            ->join('users u','u.user_id  = a.user_id','LEFT')
            ->where($map)
            ->field('s.skill_name,s.img,s.skill_id,a.is_use')
            ->select();
    }




}
