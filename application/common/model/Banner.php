<?php
namespace app\common\model;




class Banner extends Base
{
    public function getList($where = []){
        return $this->alias('b')
            ->join('banner_cate c','c.cid = b.cid','LEFT')
            ->where($where)
            ->order('order')
            ->field('b.*,c.cate_name')
            ->paginate('',false,['query'=>request()->param()]);
    }

    public function getBanner($where = [],$limit = 6)
    {
        return $this->where($where)->field('bid,url,title,img')->order('order')->limit($limit)->select();
    }


}