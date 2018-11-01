<?php
namespace app\common\model;




class MoneyDetail extends Base
{
    protected $dateFormat = 'Y/m/d H:i';
    public function getList($where = []){
        $file = 'type,money,money_type,create_time,remark';
        $rows = $this->order('create_time desc')->where($where)->field($file)->paginate();
        $items = $rows->items();
        if (empty($items)) return false;
        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$items];
    }
    public function money_detail_list($where = [])
    {
        return db('money_detail')->alias('m')
            ->where($where)
            ->order('create_time desc')
            ->paginate('',false,['query'=>request()->param()]);
//        return db('money_detail')->getLastSql();
    }

}