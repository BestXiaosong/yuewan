<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/11 0011
 * Time: 10:00
 */

namespace app\api\controller;


use app\common\model\ChatUsers;
use app\common\model\RoomFollow;
use app\common\model\Users;
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
        $this->ApiLimit(1,$this->user_id);
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
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 获取房间|群组列表
     */
    public function groupList()
    {

        $type = input('post.type');
        $map  = [];

        if ($type != 1 && $type != 2) api_return(0,'参数错误');

        $map['a.status']  = 1;
        $map['a.user_id'] = $this->user_id;
        $map['c.type']    = $type;
        $model = new ChatUsers();

        $rows = $model->getRows($map);

        api_return(1,'获取成功',$rows);
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 邀人入群
     */
    public function invite()
    {
        $this->ApiLimit(1,$this->user_id);
        $userIds  = input('post.userIds');
        $group_id = input('post.group_id');

        $userIds = explode(',',$userIds);

        if (count($userIds) < 1 ) api_return(0,'请选择要邀请的用户!');

        $model = new ChatUsers();

        $map['a.user_id']  = $this->user_id;
        $map['a.group_id'] = $group_id;
        $map['a.status']   = 1;
        $map['c.status']   = 1;

        $master = $model->getDetail($map);

        if (!$master) api_return(0,'您不在群组中,不能进行操作');

//        if ($master['type'] == 2 && $master['group_user'] != $this->user_id){
//            api_return(0,'您不是群主,不能进行操作');
//        }

        Db::startTrans();
        try{

            foreach ($userIds as $k => $v){

                $user_id = dehashid($v);
                if (!is_numeric($user_id)) api_return(0,'参数错误');
                $where['group_id'] = $group_id;
                $where['user_id']  = $user_id;
                $data = $model->where($where)->field('chat_id,status')->find();

                if ($data){
                    if ($data['status'] == 1){
                        //用户已在群组中,结束本次循环
                        continue;
                    }else{
                        $save['status']  = 1;
                        $save['chat_id'] = $data['chat_id'];
                        $model->save($save,['chat_id'=>$save['chat_id']]);
                    }

                }else{
                    $save['group_id'] = $group_id;
                    $save['user_id']  = $user_id;
                    $model->save($save);
                }
            }

            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            api_return(0,$e->getMessage());
        }

        api_return(1,'拉入成功');
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 删除群成员
     */
    public function delUsers()
    {
        $this->ApiLimit(1,$this->user_id);
        $userIds  = input('post.userIds');
        $group_id = input('post.group_id');

        $userIds = explode(',',$userIds);

        if (count($userIds) < 1 ) api_return(0,'请选择要删除的用户!');

        $model = new ChatUsers();

        $map['a.user_id']  = $this->user_id;
        $map['a.group_id'] = $group_id;
        $map['a.status']   = 1;
        $map['c.status']   = 1;

        $master = $model->getDetail($map);

        if (!$master) api_return(0,'您不在群组中,不能进行操作');

        if ($master['group_user'] != $this->user_id){
            api_return(0,'您不是群主,不能进行操作');
        }

        Db::startTrans();
        try{

            foreach ($userIds as $k => $v){

                $user_id = dehashid($v);
                if (!is_numeric($user_id)) api_return(0,'参数错误');

                if ($user_id == $this->user_id){
                    api_return(0,'您不能删除自己');
                }

                $where['group_id'] = $group_id;
                $where['user_id']  = $user_id;
                $data = $model->where($where)->field('chat_id,status')->find();

                if ($data){
                    if ($data['status'] == 1){

                        $model->save(['status'=>0],['chat_id'=>$data['chat_id']]);

                    }else{
                        //用户不在群组中,结束本次循环
                        continue;
                    }

                }else{
                    api_return(0,'用户不在群组中');
                }
            }

            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            api_return(0,$e->getMessage());
        }

        api_return(1,'删除成功');
    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 群管理界面
     */
    public function groupManage()
    {
        $id = input('post.id');

        $map['group_id'] = $id;
        $map['status']   = 1;

        $field = 'group_user,group_id,type as group_type,group_name';
        $group = Db::name('chat_group')->where($map)->field($field)->cache(3)->find();

        if (!$group) api_return(0,'参数错误');

        $model = new ChatUsers();

        $user = $this->inGroup();

        if ($user){
            //如果在群里 判断是否具有管理权限及获取是否开启群消息通知状态

            if ($group['group_user'] == $this->user_id){
                $group['has_power'] = 1;
            }else{
                $group['has_power'] = 0;
            }

            unset($group['group_user']);

            $shield['user_id']  = $this->user_id;
            $shield['group_id'] = $id;
            $shield['status']   = 1;

            $shield_id = Db::name('chat_shield')->where($shield)->value('status');

            $group['is_shield'] = $shield_id??0;

        }else{

            //不在群组中判断群组类型  群组为家族  返回code 400 指示前端跳转家族申请页面
            if ($group['group_type'] == 2){
                api_return(400,'您不在群组中');
            }else{
                api_return(0,'您不在群组中');
            }
        }

        $condition['a.group_id'] = $id;
        $condition['a.status']   = 1;

        $rows = $model->getList($condition,$this->user_id);

        api_return(1,'获取成功',['group'=>$group,'rows'=>$rows]);

    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 家族信息\家族介绍
     */
    public function groupInfo()
    {

        $field = 'group_user,group_id,type as group_type,group_name,img,tag';

        $group = $this->groupCheck($field);

        $group['tag'] = explode(',',$group['tag']);
        $group['group_max'] = $this->extend('group_max');

        $model = new ChatUsers();

        $user = $this->inGroup();

        if ($user){
            $group['in'] = 1;
        }else{
            $group['in'] = 0;
        }

        $master = $this->userInfo('nick_name,header_img,user_id',$group['group_user']);
        $master['user_id'] = hashid($master['user_id']);
        $master['noble_id'] = $this->userExtra('noble_id',$group['group_user']);

        $condition['a.group_id'] =  $group['group_id'];
        $condition['a.status']   = 1;

        $rows = $model->getList($condition,$group['group_user']);

        //TODO 家族房间未查询
        $group['user_count'] = count($rows) + 1;
        unset($group['group_user']);

        api_return(1,'获取成功',['group'=>$group,'master'=>$master,'rows'=>$rows]);

    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 申请加入家族
     */
    public function applyGroup()
    {

        $this->ApiLimit(1,$this->user_id);

        $group = $this->groupCheck();

        $user = $this->inGroup();

        if ($user){
            api_return(0,'您已在家族中,请勿重复操作');
        }else{
            $cache = cache('applyGroup'. $group['group_id']);

            if (in_array($this->user_id,$cache)){
                api_return(0,'您已提交申请,请耐心等待审核');
            }else{
                $cache[] = $this->user_id;
            }

            cache('applyGroup'. $group['group_id'],$cache);

            api_return(1,'申请提交成功,请耐心等待审核');

        }

    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 家族审核列表
     */
    public function applyList()
    {

        $group = $this->groupCheck();

        $users = cache('applyGroup'. $group['group_id']);

        $map['a.user_id'] = ['in',$users];

        $model = new Users();

        $rows = $model->apply($map);

        api_return(1,'获取成功',$rows);

    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 检查家族是否存在
     */
    protected function groupCheck($field = 'group_id,group_user')
    {
        $id = input('post.id');

        $map['group_id'] = $id;
        $map['status']   = 1;
        $map['type']     = 2;

        $group = Db::name('chat_group')->where($map)->field($field)->cache(3)->find();

        if (!$group) api_return(0,'参数错误');

        return $group;
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 判断用户是否在群组中
     */
    protected function inGroup($group_id = null,$user_id = null)
    {
        if ($user_id){
            $where['user_id']  = $user_id;
        }else{
            $where['user_id']  = $this->user_id;
        }
        if ($group_id){
            $where['group_id'] = $group_id;
        }else{
            $where['group_id'] = input('post.id');
        }

        $where['status']   = 1;

        return Db::name('chat_users')->where($where)->value('chat_id');
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

