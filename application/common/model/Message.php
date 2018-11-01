<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6 0006
 * Time: 11:00
 */

namespace app\common\model;


use think\Model;

class Message extends Model
{
    /**
     * @return array|bool
     * 获取系统消息
     */

    public function getSysMessageList()
    {
        $rows = $this->field('mid,content,title,create_time')->where('user_id=0 and status=1')
            ->order('mid desc')
            ->paginate();
        $items = $rows->items();
        if (empty($items)) return false;
        return ['thisPage' => $rows->currentPage(), 'hasNext' => $rows->hasMore(), 'data' => $items];
    }

}