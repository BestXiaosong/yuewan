<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/31 0031
 * Time: 14:10
 */

namespace app\common\model;


use think\Model;

class News extends Model
{

    public function newsList($where = array(),$iscache=true)
    {
        $res = $this
            ->alias('n')->field('n.*,c.cate_name')
            ->join('news_cate c', 'n.cid=c.cid', 'LEFT')
            ->where($where)
            ->order('n.news_id desc');
            if($iscache){
                $res->cache(true, 60);
            }

           return $res->paginate('', false, ['query' => request()->param()]);

    }

    public function apiNewsList()
    {
        $rows = $this
            ->alias('n')
            ->field('n.news_id,n.title,n.img,n.create_time,c.cate_name')
            ->join('news_cate c', 'n.cid=c.cid', 'LEFT')
            ->where('n.status=1')
            ->order('n.create_time desc')
            ->cache(true, 60)
            ->paginate();
        $items= $rows->items();
        if (empty($items)) return false;
        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$items];


    }


    //查询推荐
    public function getNewsTop($limit=5)
    {
        return $this
            ->field('n.news_id,n.title,n.img,n.create_time,c.cate_name')
            ->alias('n')
            ->join('news_cate c', 'n.cid=c.cid', 'LEFT')
            ->where('n.is_top=1 and n.status=1')->order('n.create_time desc')
            ->cache(true, 60)
            ->limit($limit)->select();
    }

    //咨讯详情
    public function getNewsDetailsById($id)
    {
        return $this
            ->field('n.news_id,n.title,n.img,n.create_time,n.cid,c.cate_name
            ,n.detail,n.lihao,n.likong')
            ->alias('n')
            ->join('news_cate c', 'n.cid=c.cid', 'LEFT')
            ->where('news_id', $id)
            ->cache(true,60)
            ->find();

    }

    //咨讯详情关联列表

    public function getNewsListByCid($cid, $limit)
    {
        return $this
            ->field('n.news_id,n.title,n.img,n.create_time,c.cate_name')
            ->alias('n')
            ->join('news_cate c', 'n.cid=c.cid', 'LEFT')
            ->where('n.cid',$cid)
            ->where('n.status=1')->order('n.news_id desc')
            ->cache(true, 60)
            ->limit($limit)->select();

    }


}