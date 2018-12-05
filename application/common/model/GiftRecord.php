<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/7
 * Time: 14:26
 */

namespace app\common\model;


use app\api\controller\User;
use think\Db;
use think\Model;

class GiftRecord extends Model
{

    public function users()
    {
        return $this->hasOne('users','user_id','to_user');
    }


    public function giftCount($where = [])
    {
        return $this->alias('a')
            ->join([
                ['gift g','g.gift_id = a.gift_id','left']
            ])
            ->where($where)
            ->field('g.gift_id,g.gift_name,g.img,sum(a.num) as num,g.thumbnail,g.price')
            ->group('gift_id')
            ->cache(15)
            ->order('g.price')
            ->select();
    }

    public function giftList($map = [])
    {
        $rows =  $this->alias('a')
            ->join([
                ['users u','u.user_id = a.user_id','left']
            ])
            ->where($map)
            ->field('a.total,u.nick_name,a.to_user,a.create_time')
            ->cache(15)
            ->order('a.record_id desc')
            ->paginate()->each(function ($item){
                $item['to_user'] = \app\api\controller\Base::staticInfo('nick_name',$item['to_user']);
            });
        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$rows->items()];
    }



    //后台获取赠送礼物日志
    public function  recordList($where=[])
    {
        return $this->alias('gr')->where($where)
            ->join('gift g','gr.gift_id = g.gift_id','left')
            ->join('users u','gr.user_id = u.user_id','left')
            ->field('g.gift_name,g.thumbnail,g.price,gr.num,gr.create_time,gr.update_time,u.nick_name,gr.to_user')
            ->order('gr.record_id desc')
            ->paginate('',false,['query'=>request()->param()]);
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



}