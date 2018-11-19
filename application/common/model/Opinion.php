<?php
namespace app\common\model;




class Opinion extends Base
{

    public function getList($filter = []){

        $field = 'a.type,a.status,a.oid,a.content,a.is_read,a.create_time,u.phone';

        return $this->alias('a')
            ->where($filter)
            ->order('a.oid DESC')
            ->join(['cl_users'=>'u'],'u.user_id = a.user_id')
            ->field($field)
            ->paginate(15,false,['query'=>request()->param()]);
    }

    public function getOne($id){

       return $this->alias('a')
            ->field('a.oid,a.content,u.nick_name')
            ->order('a.oid DESC')
            ->join(['cl_users'=>'u'],'u.user_id = a.user_id')
            ->where('oid',$id)->find();
    }



}