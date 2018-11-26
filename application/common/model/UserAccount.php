<?php
namespace app\common\model;




class UserAccount extends Base
{
    public function getList($where = []){
        return $this->alias('a')
            ->join('users u','u.user_id = a.user_id','LEFT')
            ->where($where)
            ->order('account_id desc')
            ->field('a.*,u.nick_name')
            ->paginate('',false,['query'=>request()->param()]);
    }




}