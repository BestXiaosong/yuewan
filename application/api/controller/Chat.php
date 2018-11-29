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
use think\Exception;

class Chat extends User
{

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 创建群聊
     */
    public function createGroup()
    {

        $this->ApiLimit(1,$this->user_id);

        $data = request()->only(['userIds','group_name','img','tag','type'],'post');

        if ($data['type'] != 1 && $data['type'] != 2) api_return(0,'群组类型错误');

        if ($data['type'] == 1){ //普通群

            $group_name = $this->userInfo('nick_name');
            $group_img  = $this->userInfo('header_img');

        }else{  //家族群
            $extra = $this->userExtra('noble_id,noble_time');

            if (!$extra['noble_id'] || $extra['noble_time'] < time()) api_return(0,'非贵族不能创建家族');

            $group_name = $data['group_name'];
            $group_img  = $data['img'];

        }

        $group['group_name']  = $group_name;
        $group['img']         = $group_img;
        $group['tag']         = $data['tag'];
        $group['type']        = $data['type'];
        $group['group_user']  = $this->user_id;
        $group['create_time'] = time();
        $group['update_time'] = time();

        if (strstr($data['userIds'],',')){
            $userHashIds = explode(',',$data['userIds']);
            $userHashIds = array_unique($userHashIds);
            if ($data['type'] == 2){ //家族群

                $map['status']     = 1;
                $map['group_user'] = $this->user_id;
                $map['type']       = 2;
                $has_group = Db::name('chat_group')->where($map)->value('group_id');
                if ($has_group) api_return(0,'您已经创建了家族,不能重复创建');

                $group_max = $this->extend('group_max');
                if (count($userHashIds) > $group_max-1) api_return(0,'家族群最多只能'.$group_max.'人');
            }
        }else{
            api_return(0,'最少需要两人加入!');
        }
        Db::startTrans();
        try{

            $group_id = Db::name('chat_group')->insertGetId($group);

            $master['group_id']    = $group_id;
            $master['type']        = 2;
            $master['user_id']     = $this->user_id;
            $master['create_time'] = time();
            $master['update_time'] = time();

            $users[] = $master;

            $userIds = [];
            foreach ($userHashIds as $k => $v){

                $userIds[$k] = dehashid($v);
                //成员为自己结束本次循环
                if ($userIds[$k] == $this->user_id) continue;

                if ($k <= 9){
                    $exist = $this->userInfo('user_id',$userIds[$k]);
                    //找不到用户结束本次循环
                    if (!$exist) continue;

                    if ($data['type'] == 1){ //创建普通群组更新群名及头像图片
                        $group_name .= '、'.$this->userInfo('nick_name',$userIds[$k]);
                        $group_img  .= ','.$this->userInfo('header_img',$userIds[$k]);
                    }

                    $user['group_id']    = $group_id;
                    $user['type']        = 1;
                    $user['user_id']     = $userIds[$k];
                    $user['create_time'] = time();
                    $user['update_time'] = time();
                    $users[] = $user;
                }
            }

            Db::name('chat_users')->insertAll($users);

            if ($data['type'] == 1){
                $update['group_name'] = $group_name;
                $update['img']        = $group_img;
                Db::name('chat_group')->where('group_id',$group_id)->update($update);
            }

            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            api_return(0,$e->getMessage());
        }
        api_return(1,'创建成功',['group_id'=>$group_id]);
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 退出群组
     */
    public function signOut()
    {
        $id = input('post.id');

        $map['group_id'] = $id;
        $map['user_id']  = $this->user_id;
        $map['status']   = 1;

        $groupUser = Db::name('chat_users')->where($map)->find();

        if ($groupUser) {

            if ($groupUser['type'] == 2){

                api_return(0,'管理员不能退出');

            }else{

                $result = Db::name('chat_users')->where('chat_id',$groupUser['chat_id'])->update(['status'=>0]);

                if ($result) {
                    api_return(1,'退出成功');
                }else{
                    api_return(0,'退出失败');
                }

            }

        }else{
            api_return(0,'参数错误');
        }
    }















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

