<?php
namespace app\common\model;




class Banner extends Base
{
    public function getList($map = []){
        return $this->alias('b')
            ->join('banner_cate c','c.cid = b.cid','LEFT')
            ->where($map)
            ->order('order')
            ->field('b.*,c.cate_name')
            ->paginate('',false,['query'=>request()->param()]);
    }

    public function getBanner($map = [],$limit = 6)
    {
        return $this->where($map)->field('bid,url,title,img')->order('order')->limit($limit)->select();
    }


}