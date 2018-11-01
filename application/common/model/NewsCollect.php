<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/1 0001
 * Time: 14:14
 */

namespace app\common\model;


use think\Model;

class NewsCollect extends Model
{
    //我的收藏
    /**
     * @param $user_id
     * @return array|bool
     */
    public function myCollect($user_id)
    {
        $rows = $this->alias('c')
            ->field('c.collect_id,c.news_id,n.title,n.img,n.create_time,ca.cate_name,n.detail')
            ->join('news n', 'n.news_id = c.news_id', 'LEFT')
            ->join('news_cate ca', 'ca.cid = n.cid', 'LEFT')
            ->where('c.user_id', $user_id)
            ->where('n.status=1 and c.status=1')
            ->order('c.collect_id desc')
            ->paginate();
        $items = $rows->items();
        if (empty($items)) return false;
        return ['thisPage' => $rows->currentPage(), 'hasNext' => $rows->hasMore(), 'data' => $items];
    }

    //删除我的收藏

    public function updateStatus($collect_ids, $user_id)
    {
//        return $this->where('collect_id', 'in', $collect_ids)->delete();

        return $this->save(['status' => 0], function ($query) use ($user_id, $collect_ids) {
            // 更新user_id值为$user_id并且collect_id再$collect_ids中的数据
            $query->where('user_id', $user_id)->where('collect_id', 'in', $collect_ids);
        });
    }


    public function getCollectByUser($user_id, $news_id)
    {
        return $this->where('user_id',$user_id)->where('news_id',$news_id)->where('status',1)->find();
    }


}