<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/2 0002
 * Time: 11:10
 */

namespace app\common\model;


use think\Model;

class RedHistory extends Model
{
    //当前用户发送红包详情
    public function getHistoryByRedId($red_id)
    {
        $rows = $this->alias('h')
            ->field('h.money,h.luck_king,h.create_time,r.header_img,r.role_name')
            ->where('h.red_id', $red_id)
            ->join('role r', 'r.role_id = h.role_id', 'LEFT')
            ->paginate();
        $items = $rows->items();
        $count = $this->where('red_id', $red_id)->count();
        return ['thisPage' => $rows->currentPage(), 'hasNext' => $rows->hasMore(), 'data' => $items,'count'=>$count];
    }

    //当前角色接收红包记录
    public function getReceiveByRoleId($role_id)
    {
        $rows = $this->alias('h')
            ->field('h.red_id,h.money,h.luck_king,h.create_time,r.role_name,red.red_type,red.money_type')
            ->where('h.role_id', $role_id)
            ->join('role r', 'r.role_id = h.role_id', 'LEFT')
            ->join('red_package red', 'red.red_id = h.red_id', 'LEFT')->order('h.create_time desc')
            ->paginate();
        $items = $rows->items();
        if (empty($items)) return false;
        foreach ($items as $k => $va) {
            $items[$k]['red_id'] = hashid($va['red_id']);
        }
        return ['thisPage' => $rows->currentPage(), 'hasNext' => $rows->hasMore(), 'data' => $items];
    }

    //接收红包统计
    public function receiveAcount($user_id, $role_id)
    {
        $res = $this->field('count(*) as num,sum(money) as sum_money')->where('user_id', $user_id)->where('role_id', $role_id)->cache(true, 60)->group('role_id')->find();
        $luck = $this->getLuckKingCountByRoleId($user_id, $role_id);
        if (empty($luck)) {
            $res['luck_num'] = 0;
        } else {
            $res['luck_num'] = $luck['luck_num'];

        }
        return $res;
    }

    //手气王次数统计
    public function getLuckKingCountByRoleId($user_id, $role_id)
    {
        return $this->field('count(*) as luck_num')->where('user_id', $user_id)->where('role_id', $role_id)->where('luck_king=1')->cache(true, 60)->group('role_id')->find();
    }

    //当前角色是否领取红包

    public function receiveStatus($role_id,$red_id)
    {
        return $this->where('role_id',$role_id)->where('red_id',$red_id)->value('h_id');

    }

}