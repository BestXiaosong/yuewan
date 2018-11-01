<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/30 0030
 * Time: 16:41
 */
namespace app\common\model;




class UserId extends Base
{
    public function getList($where = [])
    {
        $file = '*';
        return $this->alias('s')
            ->where($where)
            ->order('s.create_time desc')
            ->field($file)
            ->paginate(15, false, ['query' => request()->param()]);

//        return $this->getLastSql();
    }

    public function detail($id){
        return $this->where(['ID'=>$id])->find();
    }
}