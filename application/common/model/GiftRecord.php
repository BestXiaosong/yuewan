<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/7
 * Time: 14:26
 */

namespace app\common\model;


use think\Db;
use think\Model;

class GiftRecord extends Model
{
    public function myList($where = [])
    {
        return $this->alias('a')
            ->join([
                ['gift g','g.gift_id = a.gift_id']
            ])
            ->where($where)
            ->field('g.gift_id,g.gift_name,g.img,sum(a.num) as num')
            ->group('gift_id')
            ->cache(60)
            ->select();
    }

    public function myDetail($where = [],$type)
    {
        if ($type == 1){
            $rows = $this->alias('a')
                ->where($where)
                ->field('a.role_id,a.num,a.gift_id,a.create_time')
                ->cache(60)
                ->paginate();
        }else{
            $rows = $this->alias('a')
                ->where($where)
                ->field('a.to_role as role_id,a.num,a.gift_id,a.create_time')
                ->cache(60)
                ->paginate();
        }
        $items = $rows->items();
        if (empty($items)) return false;
        foreach ($items as $k => $v){
            $items[$k]['role_name'] = Db::name('role')->where('role_id',$v['role_id'])->cache(60)->value('role_name');
            $items[$k]['header_img'] = Db::name('role')->where('role_id',$v['role_id'])->cache(60)->value('header_img');
            $items[$k]['gift_name'] = Db::name('gift')->where('gift_id',$v['gift_id'])->cache(60)->value('gift_name');
            $items[$k]['img'] = Db::name('gift')->where('gift_id',$v['gift_id'])->cache(60)->value('img');
            $items[$k]['role_id'] = hashid($v['role_id']);
        }
        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$items];
    }


    public function  giftChange($where=[]){
//        $a=[];
//        $num=$this->where($where)->sum('num');
//        $price=db('gift')->where(['gift_id'=>$where['gift_id']])->value('price');
//        $totalmoney=$price*$num;
//        $a['num']=$num;
//        $rate=db('extend')->value('gift_ratio');
//        $a['rate']=$rate.'%';
//        $as=$totalmoney*$num['rate'];
//        $a['money']=$totalmoney-$as.'积分';
//        return $a;

        //可兑换礼物总数
        $num = $this->where($where)->sum('num');
        //礼物单价
        $price = db('gift')->where(['gift_id'=>$where['gift_id']])->value('price');
        //手续费%
        $rate = db('extend')->value('gift_ratio');
        $data['rate'] = $rate.'%';
        $data['num']  = $num;
        //礼物总值
        $total = bcmul($price,$num,2);
        //扣除金额
        $data['rate_money'] = bcmul($total,($rate/100),2);
        //可获得金额
        $data['money'] = ($total - $data['rate_money']).'积分';
        return $data;

    }

}