<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/11 0011
 * Time: 10:00
 */

namespace app\api\controller;


use app\common\model\RoomFollow;
use think\Db;

class ChatRoom extends User
{
    /**
     * 禁言列表
     */
    public function bannedList()
    {

        $role_id = $this->role_id;
        $hash_room_id = request()->post('room_id');
        if (empty($hash_room_id)) {
            api_return(0, '房间id不能为空');
        }
        $room_id = dehashid(explode('_',$hash_room_id)[1]);
        $roomInfo = Db::name('room')->where('room_id',$room_id)->field('user_id')->cache(60)->find();
        if ($roomInfo['user_id'] != $this->user_id){
            $roomfollow = new RoomFollow();
            $res = $roomfollow->getStatus($room_id, $role_id);
            if ($res['status'] != 2) {
                api_return(0, '不是管理员');
            }
        }
        $rows = $this->chatbanList($hash_room_id);
        if ($rows !== false) {
            foreach($rows as &$va){
               $roleinfo = Db::table('cl_role')->where('role_id',dehashid($va['userId']))->find();
                $va['role_id'] = $va['userId'];
                $va['room_id'] = $hash_room_id;
                $va['role_name'] = $roleinfo['role_name'];
                $va['header_img'] = $roleinfo['header_img'];
            }
            api_return(1, '获取成功', $rows);
        } else {
            api_return(0, '获取失败');
        }

    }

    /**
     * 取消禁言
     */

    public function cancelbanned()
    {
        $role_id = $this->role_id;
        $hash_room_id = request()->post('room_id');
        if (empty($hash_room_id)) {
            api_return(0, '房间id不能为空');
        }
        $room_id = explode('_',$hash_room_id)[1];
        $room_id = dehashid($room_id);
        $roomInfo = Db::name('room')->where('room_id',$room_id)->field('user_id')->cache(60)->find();
        $follow_id = request()->post('role_id');
        if (empty($follow_id)) api_return(0,'角色参数错误');
        if ($roomInfo['user_id'] != $this->user_id){
            $roomfollow = new RoomFollow();
            $res = $roomfollow->getStatus($room_id, $role_id);
            if ($res['status'] != 2) {
                api_return(0, '不是管理员');
            }else{
                $row = $roomfollow->getStatus($room_id, dehashid($follow_id));
                if ($row['status'] == 2){
                    api_return(0,'您不能对管理员进行操作');
                }
            }
        }

        $res = $this->chatpick($follow_id, $hash_room_id);
        if ($res) {
            api_return(1, '解除禁言成功');
        }
        api_return(0, '解除禁言失败');
    }

    /**
     * 禁言
     */

    public function banned()
    {
        $role_id = $this->role_id;
        $hash_room_id = request()->post('room_id');
        $room_id = explode('_',$hash_room_id)[1];
        $room_id = dehashid($room_id);
        $hash_role_id = request()->post('role_id');
        if (empty($hash_role_id)) api_return(0,'角色id为空');
        $roomInfo = Db::name('room')->where('room_id',$room_id)->field('user_id')->cache(60)->find();
        if ($roomInfo['user_id'] != $this->user_id){
            $roomfollow = new RoomFollow();
            $res = $roomfollow->getStatus($room_id, $role_id);
            if ($res['status'] != 2) {
                api_return(0, '不是管理员');
            }else{
                $row = $roomfollow->getStatus($room_id, dehashid($hash_role_id));
                if ($row['status'] == 2){
                    api_return(0,'您不能对管理员实行禁言');
                }
            }
        }

        $res = $this->chatban($hash_room_id, $hash_role_id);

        if ($res) {
            api_return(1, '禁言成功');
        } else {
            api_return(0, '禁言失败');
        }
    }

}

