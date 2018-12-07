<?php
namespace app\common\model;




use think\Db;

class Order extends Base
{
    public function getList($where = []){
        return $this->alias('b')
            ->join('banner_cate c','c.cid = b.cid','LEFT')
            ->where($where)
            ->order('order')
            ->field('b.*,c.cate_name')
            ->paginate('',false,['query'=>request()->param()]);
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 获取订单评论
     */
    public function getComment($map = [])
    {
        $rows =  $this->alias('a')
            ->join('users u','u.user_id = a.user_id','LEFT')
            ->join('gift g','g.gift_id = a.gift_id','LEFT')
            ->where($map)
            ->field('a.score,g.gift_name,g.thumbnail,g.img,a.num,a.tags,a.content,a.update_time,u.nick_name,u.header_img,u.user_id')
            ->cache(30)
            ->paginate()->each(function ($item){

                $tags = [];
                $tags['tag']    = ['in',$item['tags']];
                $tags['status'] = 1;
                $item['tags'] = Db::name('skill_tag')->where($tags)->field('tag_name')->cache(30)->select();
                $item['user_id'] = hashid($item['user_id']);

            });
        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$rows->items()];
    }


    public static function InterestTag($map = [])
    {

        return self::alias('a')
            ->where($map)
            ->join('skill s','s.skill_id = a.skill_id','LEFT')
            ->distinct(true)
            ->field('s.skill_name')
            ->cache(15)
            ->select();
    }
    
    
    


}