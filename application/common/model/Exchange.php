<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/28
 * Time: 11:35
 */

namespace app\common\model;


use think\Model;

class Exchange extends Model
{

    /**
     * 提现已审核未成交数据
     */
    public function getList($where = [])
    {
        $rows  =  $this->where($where)->field('id,type,num,create_time')->paginate();
        $items = $rows->items();
        if (empty($items)) return false;
        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$items];
    }



}