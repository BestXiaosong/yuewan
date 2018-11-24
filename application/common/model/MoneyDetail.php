<?php
namespace app\common\model;




class MoneyDetail extends Base
{
    protected $dateFormat = 'Y-m-d H:i';

    public function profile()
    {
        return $this->hasOne('users', 'user_id', 'user_id')->field('nick_name');
    }


    public function getList($where = []){
        $field = 'type,money,title,create_time,remark,order_id';
        $rows = $this->order('create_time desc')->order('d_id desc')->where($where)->field($field)->paginate();
        $items = $rows->items();

        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$items];
    }


    public function money_detail_list($where = [])
    {
        return $this->alias('m')
            ->where($where)
            ->order('d_id desc')
            ->paginate('',false,['query'=>request()->param()]);
    }

}