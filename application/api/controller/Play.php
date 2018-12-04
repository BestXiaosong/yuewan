<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/2
 * Time: 10:36
 */

namespace app\api\controller;


use app\common\model\Room;
use app\common\model\RoomFollow;
use think\Db;
use wheat\Wheat;

class Play extends User
{

    /**
     * 房间列表
     */
    public function roomList()
    {
        $map['a.status'] = 1;
        $type = input('post.type');
        if (is_numeric($type)) $map['a.type'] = $type;
        $model = new Room();
        $rows = $model->getRows($map);
        api_return(1,'获取成功',$rows);
    }




    /**
     * 进入房间
     */
    public function joinRoom()
    {

        $this->ApiLimit(1,$this->user_id);

        $data = input('post.');
        $model = new Room();

        //TODO 完成不同类型房间的不同数据返回

        $data = $model->joinRoom($data['id'],$this->user_id,trim($data['password']));

        if ($data !== false) {
            //查询当前用户是否被禁言

            $data['baned'] = $this->chatbanList($data['room_id'],$this->user_id);

            api_return(1,'获取成功',$data);
        }
        api_return(0,$model->getError());
    }



    /**
     * 上麦、换麦、抱麦
     * */
    public function upWheat(){
        $post = request()->only(['room_id','wheat_id','type','user_id'],'post');
        //验证数据
        $result = $this->validate($post,'Wheat.up');
        if(true !== $result){
            api_return(0,$result);
        }
        //不传默认登录用户ID
        if(empty($post['user_id'])){
            $post['user_id'] = $this->user_id;
        }else{
            $post['user_id'] = dehashid($post['user_id']);
            if (!is_numeric($post['user_id'])) api_return(0,'参数错误');
        }

        //TODO  不同房间不同处理

        $wheat = new Wheat();
        if(isset($post['type']) && $post['type']  == 1){
            //TODO 验证用户是否有抱麦权限

            $ret = $wheat->embrace($post['user_id'],$post['room_id'],$post['wheat_id']);  //抱麦
        }else{
            $ret = $wheat->on($post['user_id'],$post['room_id'],$post['wheat_id']);  //上麦
        }
        if($ret['code']){
            api_return(1,$ret['msg'],$ret['data']['wheat']);
        }else{
            api_return(0,$ret['msg']);
        }
    }

    /**
     *下麦、提麦
     * */
    public function downWheat(){
        $post = request()->only(['room_id','wheat_id','type'],'post');
        //验证数据
        $result = $this->validate($post,'Wheat.down');
        if(true !== $result){
            api_return(0,$result);
        }
        $wheat = new Wheat();
        if ($post['type'] == 1){  //下麦

            $wheatInfo = $wheat->getWheatId($post['room_id'],$post['wheat_id']);

            if ($wheatInfo['user_id'] != hashid($this->user_id)){
                api_return(0,'您不在麦位上,不能下麦');
            }


        }else{ //踢人
            //TODO 验证是否具有踢人权限


        }

        $ret = $wheat->down($post['room_id'],$post['wheat_id']);
        if($ret['code']){
            api_return(1,$ret['msg'],$ret['data']);
        }else{
            api_return(0,$ret['msg']);
        }
    }

    /**
     *锁麦
     * */
    public function lockWheat(){
        $post =$this->request->post();
        //验证数据
        $result = $this->validate($post,'Wheat.down');
        if(true !== $result){
            api_return(0,$result);
        }
        api_return(1,'操作成功');

    }


    /**
     * 获取房主及管理员
     */
    public function roomAdmins()
    {

        $id = dehashid(input('post.id'));
//        $id = input('post.id');
        if (!is_numeric($id)) api_return(0,'参数错误');
        $model = new Room();
        $where['a.room_id'] = $id;
        $rows = $model->getAdmins($where);
        if (!empty($rows)) api_return(1,'获取成功',$rows);
        api_return(0,'暂无数据');

    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 设置房间管理员
     */
    public function admin()
    {
        $map['role_id'] = dehashid(input('post.role_id'));
        if (!is_numeric($map['role_id'])) api_return(0,'非法参数');
        $map['room_id'] = dehashid(input('post.room_id'));
        if (!is_numeric($map['room_id'])) api_return(0,'房间id错误');
        $room = Db::name('room')->where('room_id',$map['room_id'])->field('room_id,user_id')->find();
        if ($room['user_id'] != $this->user_id) api_return(0,'您不是房主,没有权限操作');
        $type = input('post.type');
        $follow = Db::name('room_follow')->where($map)->find();
        if ($type == 1){ //设置管理员
            if (empty($follow)){
                $map['status'] = 2;
                $map['create_time'] = time();
                $map['update_time'] = time();
                $result = Db::name('room_follow')->insert($map);
            }elseif ($follow['status'] == 2){
                api_return(0,'该用户已经是管理员了');
            }else{
                $result = Db::name('room_follow')->where('follow_id',$follow['follow_id'])->update(['status'=>2,'update_time'=>time()]);
            }
        }else{ //取消管理员
            if ($follow['status'] != 2){
                api_return(0,'该用户不是管理员!');
            }else{
                $result = Db::name('room_follow')->where('follow_id',$follow['follow_id'])->update(['status'=>1,'update_time'=>time()]);
            }
        }
        if ($result) api_return(1,'操作成功');
        api_return(0,'操作失败');
    }




    /**
     * 获取房间普通成员
     */
    public function roomUsers()
    {

        $id = dehashid(input('post.id'));
//        $id = input('post.id');
        if (!is_numeric($id)) api_return(0,'参数错误');
        $model = new RoomFollow();
        $where['a.room_id'] = $id;
        $where['a.status'] = ['between','0,1'];
        $where['r.status'] = 1;
        $rows = $model->getUsers($where);
        if (!empty($rows)) api_return(1,'获取成功',$rows);
        api_return(0,'暂无数据');
    }






    /**
     * 房间公告修改
     */
    public function changeDetail(){
        $id = $this->role_id;
        $where = array();
        $room_id = dehashid($this->request->post('room_id'));
        if (!is_numeric($room_id)) api_return(0,'参数错误');
        $notice_id = $this->request->post('notice_id');
        $content   = $this->request->post('content');
        $title     = $this->request->post('title');
        if(!empty($content)){
            $where['content'] = $content;
        }
        if(!empty($title)){
            $where['title'] = $title;
        }
        $model = new \app\common\logic\Room();
        $data = $model->notice($id,$room_id,$where,$this->user_id,$notice_id);
        if($data == 1){
            api_return(1,'操作成功');
        }elseif($data == 0){
            api_return(0,'操作失败');
        }elseif($data == -2){
            api_return(0,'您并没有修改公告');
        }else{
            api_return(0,'您没有修改公告的权限');
        }
    }

    /**
     * 房间公告删除
     */
    public function notice_del(){
        $id = $this->user_id;
        $room_id = dehashid($this->request->post('room_id'));
        if (!is_numeric($room_id)) api_return(0,'参数错误');
        $notice_id = $this->request->post('notice_id');
        $model = new \app\common\logic\Room();
        $data = $model->changeStatus($id,$room_id,$notice_id,1,0,$this->user_id);
        if($data == 1){
            api_return(1,'操作成功');
        }elseif($data == 0){
            api_return(0,'操作失败');
        }else{
            api_return(0,'您没有删除公告的权限');
        }
    }

    /**
     * 房间公告置顶
     */
    public function up_notice(){
        $id = $this->user_id;
        $room_id = dehashid($this->request->post('room_id'));
        if (!is_numeric($room_id)) api_return(0,'参数错误');
        $notice_id = $this->request->post('notice_id');
        $model = new \app\common\logic\Room();
        $data = $model->changeStatus($id,$room_id,$notice_id,0,1);
        if($data == 1){
            api_return(1,'操作成功');
        }elseif($data == 0){
            api_return(0,'操作失败');
        }else{
            api_return(0,'您没有置顶公告的权限');
        }
    }
    
















    /**
     * 播放信息获取
     */
    public function PlayInfo()
    {
        $id = dehashid(input('post.id'));
        if (!is_numeric($id)) api_return(0,'参数错误');
        $data = $this->getPlayInfo($id);
        api_return(1,'获取成功',$data);
    }








    /**
     *  直播间开启与关闭(by yy)
     */
    public function ioRoom(){
        $room_id = dehashid($this->request->post('room_id'));
        if((!is_numeric($room_id))) api_return(0,'参数错误');
        $data = Db::name('room')->where(['room_id'=>$room_id])->value('play_status');
        $status = 0;
        if($data == 1){
            $status = 0;
        }elseif($data == 0){
            $status = 1;
        }
        Db::name('room')->where(['room_id'=>$room_id])->update(['play_status'=>$status]);
        api_return(1,'修改成功');
    }


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 房间开启全员禁言
     */
    public function roomClose()
    {
        $id = dehashid(input('post.id'));
        if (!is_numeric($id)) api_return(0,'参数错误');

        $roomInfo = Db::name('room')->where('room_id',$id)->field('user_id,is_close')->find();
//        if ($roomInfo['user_id'] != $this->user_id){
//            $map['role_id'] = $this->role_id;
//            $map['status']  = 2;
//            $follow_id = $map['room_id'] = Db::name('room_follow')->where($map)->value('follow_id');
//            if (!$follow_id) api_return(0,'您无权限进行操作');
//        }
        if ($roomInfo['is_close'] == 1) api_return(0,'房间已处于禁言状态,不能进行操作!');
        $result = Db::name('room')->where('room_id',$id)->update(['is_close'=>1]);
        if ($result !== false) {
            $this->sendMsg('room_'.hashid($id),5);
            $this->sendMsg('play_'.hashid($id),5);
            api_return(1,'禁言成功');
        }
        api_return(0,'操作失败');
    }


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 房间禁言关闭
     */
    public function roomOpen()
    {
        $id = dehashid(input('post.id'));
        if (!is_numeric($id)) api_return(0,'参数错误');
//
        $roomInfo = Db::name('room')->where('room_id',$id)->field('user_id,is_close')->find();
//        if ($roomInfo['user_id'] != $this->user_id){
//            $map['role_id'] = $this->role_id;
//            $map['status']  = 2;
//            $follow_id = $map['room_id'] = Db::name('room_follow')->where($map)->value('follow_id');
//            if (!$follow_id) api_return(0,'您无权限进行操作');
//        }
        if ($roomInfo['is_close'] == 0) api_return(0,'房间未处于禁言状态,不能进行操作!');
        $result = Db::name('room')->where('room_id',$id)->update(['is_close'=>0]);
        if ($result !== false){
            $this->sendMsg('room_'.hashid($id),8);
            $this->sendMsg('play_'.hashid($id),8);
            api_return(1,'操作成功');
        }
        api_return(0,'操作失败');
    }


    public function new_activity(){
        $id = dehashid(input('post.id'));
        if (!is_numeric($id)) api_return(0,'参数错误');
        $model = new \app\common\model\RoomActivity();
        $data = $model->new_activity($id);
        if($data !=0){
            api_return(1,'获取成功',$data);
        }else{
            api_return(0,'暂无数据');
        }
    }
    public function activity_money(){
        $id = dehashid(input('post.id'));
//        $id = 9;
        if (!is_numeric($id)) api_return(0,'参数错误');
        $join = [
           ['cl_room_activity ra','r.room_id = ra.room_id','left']
        ];
        $file = 'ra.money,ra.charge';
        $data = Db::name('room')->alias('r')->field($file)->join($join)->where(['r.room_id'=>$id])->order('ra.start_time desc')->find();
        if($data['charge'] == 1){
            $datas = $data['money']??1;
        }else{
            $datas = 0;
        }
        api_return(1,'获取成功',$datas);
    }
}