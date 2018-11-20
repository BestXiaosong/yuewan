<?php

namespace app\common\model;

use think\Db;
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


    public function getRows($map = [])
    {

        $rows = $this->alias('a')
            ->join('skill s','s.skill_id = a.skill_id','LEFT')
            ->where($map)
            ->field('s.skill_name,a.apply_id,a.my_form,a.my_gift_id,s.form_id,s.gift_id,a.mini_price,s.spec')
            ->select();
        foreach ($rows as $k => $v){
            $form['form_id']  = ['in',$v['form_id']];
            $form['status']   = 1;
            $rows[$k]['form'] = Db::name('skill_form')->where($form)->field('form_id,form_name')->cache(10)->select();

            $gift['gift_id']  = ['in',$v['gift_id']];
            $gift['status']   = 1;
            $rows[$k]['gift'] = Db::name('gift')->where($gift)->field('gift_id,gift_name,thumbnail,img,price')->order('price')->cache(10)->select();

        }

        return $rows;
    }





}
