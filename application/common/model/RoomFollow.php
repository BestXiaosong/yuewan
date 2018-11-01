<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/26
 * Time: 15:46
 */

namespace app\common\model;


use think\Model;

class RoomFollow extends Model
{


    /**
     * 获取房间成员
     */
    public function getUsers($where = [])
    {

        $rows = $this
            ->alias('a')
            ->where($where)
            ->join([
                ['role r', 'r.role_id = a.role_id', 'left'],
            ])
            ->order('a.status desc')
            ->field('a.status,r.role_name,r.header_img,r.role_id')
            ->cache(1)
            ->paginate();
        $items = $rows->items();
        if (!empty($items)) {
            foreach ($items as $k => $v) {
                $items[$k]['role_id'] = hashid($v['role_id']);
                if ($v['status'] == 1) {
                    $items[$k]['remark'] = '成员';
                } else {
                    $items[$k]['remark'] = '游客';
                }
            }
        } else {
            return false;
        }
        $num = $this->alias('a')
            ->where($where)
            ->join([
                ['role r', 'r.role_id = a.role_id', 'left'],
            ])
            ->cache(60)
            ->count('a.follow_id');
        return ['thisPage' => $rows->currentPage(), 'hasNext' => $rows->hasMore(), 'data' => $items,'num'=>$num];
    }

    /**
     * @param $room_id
     * @param $role_id
     * @return array|false|\PDOStatement|string|Model
     * 管理员状态
     */
    public function getStatus($room_id, $role_id)
    {
        return $this->where('room_id', $room_id)->where('role_id', $role_id)->find();
    }

    /**
     * 获取禁言列表
     */

    public function getBannedList($room_id)
    {
        $rows = $this
            ->alias('a')
            ->where('a.status', -1)
            ->where('a.room_id', $room_id)
            ->join([
                ['role r', 'r.role_id = a.role_id', 'left'],
            ])
            ->order('a.status desc')
            ->field('r.role_name,r.header_img,r.role_id,a.update_time,a.follow_id')
            ->cache(60)
            ->paginate();
        $items = $rows->items();
        if (empty($items)) return false;
        return ['thisPage' => $rows->currentPage(), 'hasNext' => $rows->hasMore(), 'data' => $items];

    }

    public function cancelBanned($followid)
    {
        return $this->save(['status'=>1],['follow_id'=>$followid]);

    }


}