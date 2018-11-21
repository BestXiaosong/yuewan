<?php

namespace app\common\model;

use think\Db;
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
        $data =  $this->where($map)->field('skill_id,skill_name,img,is_fast,form_id,grade')->cache(15)->find();

        if ($data){

            $form['form_id'] = ['in',$data['form_id']];
            $form['status']  = 1;
            $data['form'] = Db::name('skill_form')->field('form_id,form_name')->where($form)->cache(15)->select();

            $data['grade'] = explode(',',$data['grade']);
        }

        return $data;

    }

    public function getRows($map = [])
    {
        return $this->where($map)->order('sort')->field('skill_id,skill_name,img')->select();
    }



}
