<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/26
 * Time: 15:46
 */

namespace app\common\model;

use think\Model;

class RoomLog extends Model
{



    public function getRows($map = [])
    {
        $rows = $this->where($map)
            ->order('log_id desc')
            ->cache(10)
            ->paginate(15);
        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$rows->items()];
    }





}