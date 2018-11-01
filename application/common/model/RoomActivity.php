<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 10:34
 */

namespace app\common\model;


use think\Db;
use think\Model;

class RoomActivity extends Model
{
    public function saveChange($data = [])
    {

    }


    /**
     * 获取房间活动历史
     */
    public function history($where = [],$role_id)
    {
        $rows =  $this->where($where)->field('activity_id,img,role_id,title,detail,charge,money,start_time,reserve,status')->cache(60)->select();
        if (empty($rows)) return false;
        $map['role_id'] = $role_id;
        foreach ($rows as $k => $v){
            $rows[$k]['role_id'] = hashid($v['role_id']);
            $map['status'] = 1;
            $map['activity_id'] = $v['activity_id'];
            $rows[$k]['activity'] = Db::name('room_record')->where($map)->value('record_id')?1:0;
        }
        return $rows;
    }

    /**
     * 获取活动消息
     */
    public function actMsg($where = [],$role_id = 0)
    {
        $rows = $this->alias('a')
                ->where($where)
            ->order('a.create_time desc')
            ->field('a.room_id,a.activity_id,a.img,a.title,a.start_time,a.reserve,a.status as aStatus,a.detail,a.charge,a.money')
            ->paginate();
        $items = $rows->items();
        if (empty($items)) return false;
        $map['role_id'] = $role_id;
        foreach ($items as $k => $v){
            $map['activity_id'] = $v['activity_id'];
            $map['role_id']     = $role_id;
            $items[$k]['status'] = Db::name('room_record')->where($map)->value('status')??0;
            if ($v['aStatus'] == 2){
                if ($v['status'] == 1){
                    $items[$k]['is_reserve'] = 1;
                }else{
                    $items[$k]['is_reserve'] = 0;
                }
            }
            $items[$k]['room_id'] = hashid($v['room_id']);
        }
        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$items];
    }

    public function getOne($where,$role_id = 0)
    {
        $row = $this->alias('a')
            ->where($where)
            ->field('a.detail_img,a.room_id,a.activity_id,a.img,a.title,a.start_time,a.reserve,a.status as aStatus,a.detail,a.charge,a.money')
            ->find();
        if (empty($row))    return false;
        $row['is_reserve'] = 0;
        if ($row['aStatus'] == 2){
            $map['activity_id'] = $row['activity_id'];
            $map['role_id']     = $role_id;
            $row['status'] = Db::name('room_record')->where($map)->value('status')??0;
            if ($row['status'] == 1){
                $row['is_reserve'] = 1;
            }
        }
        $row['room_id'] = hashid($row['room_id']);
        return $row;

    }

    /**
     *  获取当前房间最新活动
     */

    public function new_activity($id){

        $result = db('room_activity')->where(['room_id'=>$id,'status'=>2])->field('img,detail_img,room_id,title,detail,charge,money,start_time,reserve')->find();
        if($result){
            $result['room_id'] = hashid($result['room_id']);
            return $result;
        }else{
            return 0;
        }
    }

}