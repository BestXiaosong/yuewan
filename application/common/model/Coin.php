<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/28
 * Time: 11:35
 */

namespace app\common\model;


use think\Db;
use think\Model;

class Coin extends Model
{
    public function getList($where = []){
        return $this->alias('a')
            ->where($where)
            ->order('a.sort')
            ->field('a.*')
            ->select();
    }


    public function getRows($where = [],$user_id = 0)
    {
        $rows  = $this->where($where)->order('sort')->field('field,table,coin_id,coin_name,img,status,length')->paginate();
        $items = $rows->items();
//        if ($items) return false;
        foreach ($items as $k => $v){
            $money = Db::name($v['table'])->where('user_id',$user_id)->value($v['field'])??0;
            $items[$k]['money'] = number_format($money,$v['length'],'.','');
            unset($v['table']);
            unset($v['field']);
            unset($v['length']);
        }
        return ['hasNext'=>$rows->hasMore(),'thisPage'=>$rows->currentPage(),'data'=>$items];
    }


}