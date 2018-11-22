<?php
namespace app\common\model;


use think\Db;
use think\Model;

class Vod extends Model
{

    public function getList($map = [])
    {
        return $this->alias('a')
            ->join('users u','u.user_id = a.user_id','LEFT')
            ->where($map)
            ->order('a.pid desc')
            ->field('a.*,u.nick_name,u.header_img')
            ->paginate('',false,['query'=>request()->param()]);
    }


    public function getRows($map = [])
    {

        $rows =  $this->alias('a')
            ->join('users u','u.user_id = a.user_id','LEFT')
            ->where($map)
            ->field("u.user_id,max(play_num) as play_num,u.nick_name,a.pid,a.play_url,u.header_img")
            ->group('a.user_id')
            ->paginate()->each(function ($item){

                //TODO 遍历查询用户贵族身份


                $item['user_id'] = hashid($item['user_id']);

            });

        //返回值进行随机排序
        $items = $rows->items();
        shuffle($items);

        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$items];

    }



}