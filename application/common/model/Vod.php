<?php
namespace app\common\model;




class Vod extends Base
{

    public function getList($map = [])
    {
        return $this->alias('a')
            ->join('users u','u.user_id = a.user_id','LEFT')
            ->where($map)
            ->order('a.pid')
            ->field('a.*,u.nick_name,u.header_img')
            ->paginate('',false,['query'=>request()->param()]);
    }


}