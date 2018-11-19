<?php
namespace app\common\model;




class LoginLog extends Base
{
    public function getList($where = []){
        return $this->alias('b')
            ->join('users c','c.user_id = b.user_id','LEFT')
            ->order('b.create_time DESC')
            ->where($where)
            ->field('b.*,c.nick_name')
            ->paginate(15,false,['query'=>request()->param()]);
    }

}