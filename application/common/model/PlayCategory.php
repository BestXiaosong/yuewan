<?php

namespace app\common\model;

class PlayCategory extends Base{
    protected $dateFormat = false;
    public function getList($where = []){
        return $this->where($where)->order('cid DESC')->paginate('',false,['query'=>request()->param()]);
    }

    public function getRows($where = [])
    {
        return $this->where($where)->field('img,cate_name,cid')->select();
    }

}
