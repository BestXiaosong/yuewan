<?php

namespace app\common\model;

class VodCategory extends Base{

    protected $dateFormat = false;


    public function getList($where = []){
        return $this->where($where)->order('cid DESC')->paginate('',false,['query'=>request()->param()]);
    }

}
