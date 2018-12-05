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
            ->join('user_extend e','e.user_id = a.user_id','LEFT')
            ->where($map)
            ->field("e.noble_id,e.noble_time,u.user_id,max(play_num) as play_num,u.nick_name,a.pid,a.play_url,u.header_img")
            ->group('a.user_id')
            ->cache(5)
            ->paginate()->each(function ($item){
                $item['noble_id'] = \app\api\controller\Base::checkNoble($item);
                $item['user_id'] = hashid($item['user_id']);

            });

        //返回值进行随机排序
        $items = $rows->items();
        shuffle($items);

        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$items];

    }



}