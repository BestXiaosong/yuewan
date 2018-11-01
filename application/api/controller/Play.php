<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/2
 * Time: 10:36
 */

namespace app\api\controller;


use app\common\logic\RoomActivity;
use app\common\model\Banner;
use app\common\model\PlayCategory;
use app\common\model\Room;
use app\common\model\RoomFollow;
use think\Db;
use think\Exception;

class Play extends User
{
    /**
     * 获取直播banner图
     */
    public function banner()
    {
        $where['cid'] = 2;
        $where['status'] = 1;
        $rows = Banner::where($where)->field('img')->select();
        if (!empty($rows)) api_return(1,'获取成功',$rows);
        api_return(0,'暂无数据');
    }


    /**
     * 获取推荐
     */
    public function top()
    {
        $model = new Room();
        $where['a.status'] = 1;
        $where['a.play_status'] = ['neq',0];
        $where['b.status'] = 1;
        if (input('post.type') == 'hot'){
            $where['r.official'] = ['neq',1];
        }elseif (input('post.type') == 'official'){
            $where['r.official'] = 1;
        }else{
            api_return(0,'类型错误');
        }
        $rows = $model->top($where);
        if ($rows !== false) api_return(1,'获取成功',$rows);
        api_return(0,'暂无数据');
    }

    /**
     * 获取房间分类
     */
    public function cate()
    {
        $model = new PlayCategory();
        $where['status'] = 1;
        if (input('post.top') == 1) $where['top'] = 1;
        $rows  = $model->getRows($where);
        if (!empty($rows)) api_return(1,'获取成功',$rows);
        api_return(0,'暂无数据');
    }

    /**
     * 房间列表
     */
    public function roomList()
    {
        $where['a.status'] = 1;
        $where['b.status'] = 1;
        $cid = input('post.cid');
        $title = input('post.title');
        if (is_numeric($cid)) $where['a.cid'] = $cid;
        if (!empty($title)) $where['a.room_name'] = array('like','%'.$title.'%');
        $model = new Room();
        $rows = $model->getRows($where);
        if ($rows !== false) api_return(1,'获取成功',$rows);
        api_return(0,'暂无数据');
    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 活动详情
     */
    public function actDetail()
    {
        $where['a.activity_id'] = input('post.id');
        $model = new \app\common\model\RoomActivity();
        $rows  = $model->getOne($where,$this->role_id);
        if ($rows !== false) {
            $url = db('explain')->where(['id'=>12])->cache(60)->value('content');
//            $rows['shareUrl'] = $url.'index/share_detail?room_id='.$rows['room_id'].'&user_id='.hashid($this->user_id);
            $rows['shareUrl'] = $url.'index/scheme?roomid='.hashid($rows['room_id']).'&user_id='.hashid($this->user_id);
            api_return(1,'获取成功',$rows);
        }
        api_return(0,'暂无数据');
    }



    /**
     * 进入房间
     */
    public function joinRoom()
    {
        //接口限流
        $cache = cache('joinRoom_'.$this->user_id);
        if ($cache && $cache>2){
            api_return(0,'服务器繁忙,请稍后重试');
        }else{
            if ($cache){
                cache('joinRoom_'.$this->user_id,2,1);
            }else{
                cache('joinRoom_'.$this->user_id,1,1);
            }
        }

        $id = dehashid(input('post.id'));
//        $id = input('post.id');
        if (!is_numeric($id)) api_return(0,'参数错误');
        $model = new Room();
        $role_id = $this->role_id;
        $data = $model->msg($id,$this->user_id,$role_id);
        if (!$role_id){
            api_return(0,'请先创建角色再进入直播间');
        }
        if ($data !== false) {
            //查询当前用户是否被禁言
            if ($data['play_status'] == 1){
                $chat = $data['chat_id_play'];
            }else{
                $chat = $data['chat_id'];
            }
            $data['baned'] = $this->chatbanList($chat,$this->role_id);


            $url = db('explain')->where(['id'=>12])->cache(86400)->value('content');
//            $data['shareUrl'] = $url.'index/share_detail/room_id/'.hashid($id).'/user_id/'.hashid($this->user_id);
            $rows['shareUrl'] = $url.'index/scheme?roomid='.hashid($id).'&user_id='.hashid($this->user_id);

            $map['role_id'] = $role_id;
            $map['room_id'] = $id;
            //房间游客加入
            $follow = Db::name('room_follow')->where($map)->cache(1)->value('follow_id');
            if (!$follow){
                $map['status'] = 0;
                $map['create_time'] = time();
                $map['update_time'] = time();
                Db::name('room_follow')->insert($map);
            }
            //播放地址信息
            $data['playUrl'] = $this->getPlayInfo($id);
            //融云token
            if (!$this->role_id) api_return(0,'请先选择您的角色');
            $data['r_token'] = $this->R_token($this->role_id);
            api_return(1,'获取成功',$data);
        }
        api_return(0,$model->getError());
    }

    /**
     * 房间信息
     */
    public function roomInfo()
    {
        $id = dehashid(input('post.id'));
        if (!is_numeric($id)) api_return(0,'参数错误');
        $model = new Room();
        $data  = $model->msg($id,$this->user_id,$this->role_id,2);
        if ($data !== false) api_return(1,'获取成功',$data);
        api_return(0,$model->getError());
    }

    /**
     * 进入直播间
     */
//    public function joinPlayRoom()
//    {
//        $id = dehashid(input('post.id'));
//        if (!is_numeric($id)) api_return(0,'参数错误');
//        $model = new Room();
//        $data = $model->msg($id,$this->user_id,$this->role_id);
//        if ($data !== false) {
//            $data['role_id'] = $this->role_id;
//            $data['room_id'] = $id;
//            //房间游客加入
//            $follow = Db::name('room_follow')->where($data)->value('follow_id');
//            if (!$follow){
//                $data['status'] = 0;
//                $data['create_time'] = time();
//                $data['update_time'] = time();
//                Db::name('room_follow')->insert($data);
//            }
//            api_return(1,'获取成功',$data);
//        }
//        $data['playUrl'] = $this->getPlayInfo($id);
//        api_return(0,$model->getError());
//    }




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
     * 房间公告请求
     */
    public function notice_detail(){
//        $id = $this->role_id;
//        api_return(1,'xxx',$id);
        $where = array();
        $notice_id = $this->request->post('notice_id');
        if($notice_id){
            $where['notice_id'] = $notice_id;
        }
        $room_id = dehashid($this->request->post('room_id'));
        if (!is_numeric($room_id)) api_return(0,'参数错误');
        $model = new \app\common\logic\Room();
        $data = $model->detail($room_id,$where);
//        echo $data;exit;
        if($data){
            api_return(1,'操作成功',$data);
        }else {
            api_return(0,'本房间暂无公告');
        }
    }
    /**
     * 房间公告添加
     */
    public function notice(){
        $id = $this->role_id;
//        api_return(1,'xxx',$id);
        $room_id = dehashid($this->request->post('room_id'));
        if (!is_numeric($room_id)) api_return(0,'参数错误');
        $where['content'] = $this->request->post('content');
        $where['title'] = $this->request->post('title');
        $model = new \app\common\logic\Room();
        $data = $model->notice($id,$room_id,$where,$this->user_id);
//        echo $data;exit;
        if($data == 1){
            api_return(1,'操作成功');
        }elseif($data == 0){
            api_return(0,'操作失败');
        }else{
            api_return(0,'您没有添加公告的权限');
        }
    }

    /**
     * 房间公告列表
     */
    public function notice_list(){
//        api_return(1,'xxxx',hashid('1'));
        $room_id = dehashid($this->request->post('room_id'));
        if (!is_numeric($room_id)) api_return(0,'参数错误');
        $model = new \app\common\logic\Room();
        $data = $model->notice_list($room_id);
//        echo $data;exit;
        if($data){
            api_return(1,'操作成功',$data);
        }else {
            api_return(0,'暂无数据');
        }
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
     * 发起活动
     */
    public function act()
    {
        $role_id = $this->role_id;
        $user_id = $this->user_id;
        $map['room_id'] = dehashid(input('post.id'));
        if (!is_numeric($map['room_id'])) api_return(0,'房间参数错误');
        $roomInfo = Db::name('room')->where('room_id',$map['room_id'])->field('user_id')->find();
        $userInfo = Db::name('users')->where('user_id',$user_id)->field('check')->find();
        if ($userInfo['check'] != 1) api_return(0,'请先进行实名认证再发起活动');
        if ($roomInfo['user_id'] != $this->user_id) api_return(0,'您不是房主,无权发起活动');
        if (!is_numeric($map['room_id'])) api_return(0,'参数错误');
        $room = Db::name('room')->where($map)->field('status,play_status,room_id,user_id,role_id,VIP')->find();
        if ($room['status'] != 1) api_return(0,'房间处于拍卖中或已被禁用,不能开启直播');
        if ($room['user_id'] != $user_id) api_return(0,'您无权限开启活动');

        $data   = request()->only(['img','title','detail','charge','start_time','status','money','detail_img','activity_id'],'post');
        $data['id'] = $data['activity_id'];
        if ($room['play_status'] != 0 && empty($data['id'])) api_return(0,'当前房间有活动处于预告状态或已开始,请勿重复申请');
        if ($data['charge'] == 1){
            if ($room['VIP'] != 1) {
                api_return(0, '您的房间不是VIP房间,没有收费的权限');
            }else{
                if ($data['money'] < 1) api_return(0,'收费金额最低1积分起');
            }
        }else{
            unset($data['money']);
        }
        $data['room_id'] =  $map['room_id'];
        $data['role_id'] = $role_id;
        $data['user_id'] = $user_id;
        $model  = new RoomActivity();
        Db::startTrans();
        try{
            $result = $model->saveChange($data);
            if (!$result) api_return(0,$model->getError());
            Db::name('room')->where('room_id',$map['room_id'])->update(['play_status'=>$data['status'],'role_id'=>$this->role_id]);
            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            cache('errorAct'.$this->user_id,$e,7200);
            api_return(0,'服务器繁忙,请稍后重试');
        }
        api_return(1,'活动开启成功');
    }


    /**
     * 预告活动开始
     */
    public function activity()
    {
        $id = input('post.id');
        $map['room_id'] = dehashid($id);

        $roomInfo = Db::name('room')->where('room_id',$map['room_id'])->field('room_name,user_id')->find();
        $userInfo = Db::name('users')->where('user_id',$this->user_id)->field('check')->find();
        if ($userInfo['check'] != 1) api_return(0,'请先进行实名认证再开启直播');
        if ($roomInfo['user_id'] != $this->user_id) api_return(0,'您不是房主,无权开启直播');
        $is_mobile = input('post.is_mobile') == 1??0;
        if (!is_numeric($map['room_id'])) api_return(0,'参数错误');
//        $map['role_id'] = $this->role_id;
        $map['status']  =  2;
        $model = new RoomActivity();
        $act = $model->where($map)->order('activity_id desc')->field('activity_id,role_id,status,title')->find();

        Db::startTrans();
        try{
            if ($act){
                $model->where('activity_id',$act['activity_id'])->update(['status'=>1]);
            }
            Db::name('room')->where('room_id',$map['room_id'])->update(['play_status'=>1,'is_mobile'=>$is_mobile]);
            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            api_return(0,'服务器繁忙,请稍后重试');
        }

        //推送
        if ($act){
            $where['activity_id'] = $act['activity_id'];
            $where['status'] = 1;
            Db::name('room_record')
                ->field('record_id,role_id')
                ->where($where)
                ->chunk(1000, function($items)use($id,$act) {

                    foreach ($items as $k => $v){
                        $push_id = Db::name('role')
                            ->alias('a')
                            ->join('users u','u.user_id = a.user_id','LEFT')
                            ->where('a.role_id',$v['role_id'])
                            ->value('u.j_push_id');
                        Push(1,$push_id,"您预定的活动（".$act['title']."）已开始,点击进入直播间",$id);
                    }

                });
        }else{
            $where['room_id'] = $id;
            $where['status']  = ['between','1,2'];
            Db::name('room_follow')->where($where)
                ->field('role_id,follow_id')
                ->chunk(1000,function ($items) use ($id,$roomInfo){

                    foreach ($items as $k => $v){
                        $push_id = Db::name('role')
                            ->alias('a')
                            ->join('users u','u.user_id = a.user_id','LEFT')
                            ->where('a.role_id',$v['role_id'])
                            ->value('u.j_push_id');
                        Push(1,$push_id,"关注的房间（".$roomInfo['room_name']."）开启了直播,点击进入直播间",$id);
                    }

                });
        }

        $this->sendMsg('room_'.hashid($map['room_id']),6);
        $this->sendMsg('play_'.hashid($map['room_id']),6);
        api_return(1,'操作成功');
    }

    /**
     * 活动预定及取消
     */
    public function reserve()
    {
        $id = input('post.id');
        if (!is_numeric($id)) api_return(0,'参数错误');
        $data['activity_id'] = $id;
        $act = Db::name('room_activity')->where($data)->find();
        if (empty($act)) api_return(0,'非法参数');
        if ($act['status'] == 1) api_return(0,'活动已开始不能预定');
        if ($act['status'] == 0) api_return(0,'活动已结束不能预定');
        $data['role_id'] = $this->role_id;
        $has = Db::name('room_record')->where($data)->find();
        $type = 1;
        if (empty($has)){
            $data['status'] = 1;
            $result = Db::name('room_record')->insert($data);
        }elseif ($has['status'] == 0){
            $result = Db::name('room_record')->where('record_id',$has['record_id'])->update(['status'=>1]);
        }else{
            $type = 0;
            $result = Db::name('room_record')->where('record_id',$has['record_id'])->update(['status'=>0]);
        }
        if ($result){
            if ($type == 1){ //预定
                $msg = '活动预定成功';
                Db::name('room_activity')->where('activity_id',$id)->setInc('reserve');
            }else{ //取消预定
                $msg = '活动取消成功';
                Db::name('room_activity')->where('activity_id',$id)->setDec('reserve');
            }
            api_return(1,$msg);
        }
    }

    /**
     * 房间活动列表
     */
    public function actList()
    {
        $where['room_id'] = dehashid(input('post.id'));
//        $where['room_id'] = input('post.id');
        if (!is_numeric($where['room_id'])) api_return(0,'参数错误');
        $model = new \app\common\model\RoomActivity();
        $rows  = $model->history($where,$this->role_id);
        if ($rows !== false) api_return(1,'获取成功',$rows);
        api_return(0,'暂无数据');
    }

    /**
     * 活动消息
     */
    public function actMsg()
    {
        $where['role_id'] = $this->role_id;
        $where['status']  = ['between','1,2'];
        $room_id = Db::name('room_follow')->where($where)->column('room_id');
        $map['a.room_id'] = ['in',$room_id];
//        $map['r.role_id']  = $this->role_id;
        $model = new \app\common\model\RoomActivity();
        $rows  = $model->actMsg($map,$this->role_id);
        if ($rows !== false) api_return(1,'获取成功',$rows);
        api_return(0,'暂无数据');
    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 获取所有活动消息
     */
    public function allMessage()
    {
        $where['a.status']  = 2;
        $model = new \app\common\model\RoomActivity();
        $rows  = $model->actMsg($where,$this->role_id);
        if ($rows !== false) api_return(1,'获取成功',$rows);
        api_return(0,'暂无数据');
    }


    /**
     * 活动结束
     */
    public function actEnd()
    {
        $map['room_id'] = dehashid(input('post.id'));
        if (!is_numeric($map['room_id'])) api_return(0,'参数错误');
        $roomInfo = Db::name('room')->where('room_id',$map['room_id'])->field('user_id')->find();
        $userInfo = Db::name('users')->where('user_id',$this->user_id)->field('check')->find();
        if ($userInfo['check'] != 1) api_return(0,'请先进行实名认证再开启直播');
        if ($roomInfo['user_id'] != $this->user_id) api_return(0,'您不是房主,无权关闭直播');
        $map['user_id'] = $this->user_id;
        $map['status']  = 1;
        $model = new RoomActivity();
        $act = $model->where($map)->order('activity_id desc')->field('activity_id,role_id,status')->find();
        Db::startTrans();
        try{
            if ($act){
                $model->where('activity_id',$act['activity_id'])->update(['status'=>0]);
            }
            Db::name('room')->where('room_id',$map['room_id'])->update(['play_status'=>0]);
            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            api_return(0,'服务器繁忙,请稍后重试');
        }
        $this->sendMsg('room_'.hashid($map['room_id']),7);
        $this->sendMsg('play_'.hashid($map['room_id']),7);
        api_return(1,'操作成功');
    }


    /**
     * 获取推流地址
     */
    public function pushUrl()
    {
        $id = dehashid(input('post.id'));
        if (!is_numeric($id))  $id = input('post.id');
        if (!is_numeric($id)) api_return(0,'参数错误');
        $room = Db::name('room')->where('room_id',$id)->field('room_id,user_id')->find();
        if ($room['user_id'] != $this->user_id) api_return(0,'您不是房主,不能查看推流地址');
        $user = Db::name('users')->where('user_id',$this->user_id)->field('check,user_id')->find();
        if ($user['check'] != 1) api_return(0,'实名认证后才能进行直播');
        $data = $this->getPushUrl($id);
        api_return(1,'获取成功',$data);
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
     * 房间升级为VIP房间
     */
    public function VIP()
    {
        $cache = cache('ROOM_VIP_'.$this->user_id);
        if ($cache){
            api_return(0,'访问过于频繁,请稍后重试');
        }else{
            cache('ROOM_VIP_'.$this->user_id,1,3);
        }
        $id = dehashid(input('post.id'));
        if (!is_numeric($id)) api_return(0,'参数错误');
        $room = Db::name('room')->where('room_id',$id)->field('user_id,VIP')->find();
        if ($room['user_id'] != $this->user_id) api_return(0,'您不是房主,不能进行操作');
        if ($room['VIP'] == 1) api_return(0,'该房间已经是VIP房间了,请勿重复操作');
        $user = Db::name('users')->where('user_id',$this->user_id)->field('money')->find();
        $money = input('post.money');
        if ($user['money'] < $money) api_return(0,'余额不足');
        $mini  = Db::name('explain')->where('id',7)->cache(60)->value('content');
        if ($money < $mini) api_return(0,"升级VIP房间抵押积分最低为$mini");
        Db::startTrans();
        try{
            Db::name('room')->where('room_id',$id)->update(['VIP'=>1]);
            Db::name('users')->where('user_id',$this->user_id)->setDec('money',$money);
            money($this->user_id,7,-$money,1,'直播间升级付费');
            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            api_return(0,'系统错误');
        }
        api_return(1,'升级成功');
    }


    /**
     * 获取房间升级展示参数
     */
    public function getVIP()
    {
        $data['money']  = Db::name('explain')->where('id',7)->cache(60)->value('content');
        $data['remark'] = Db::name('explain')->where('id',8)->cache(60)->value('content');
        api_return(1,'获取成功',$data);
    }

    /**
     * 开启房间收费
     */
    public function charge()
    {
        $id    = dehashid(input('post.id'));
        if (!is_numeric($id)) api_return(0,'参数错误');
        $room  = Db::name('room')->where('room_id',$id)->field('room_id,user_id,VIP,is_charge')->find();
        if ($room['user_id'] != $this->user_id) api_return(0,'您不是房主,无权进行操作');
        if ($room['VIP'] != 1) api_return(0,'房间不是VIP房间,不能设置付费');
        $data['is_charge'] = 1;
        $data['money']     = input('post.money');
        if ($data['money'] < 1 ) api_return(0,'收费金额为1积分起');
        $result = Db::name('room')->where('room_id',$id)->update($data);
        if ($result) api_return(1,'开启成功');
        api_return(0,'系统错误');
    }

    /**
     * 获取房间收费金额
     */
    public function getCharge()
    {
        $id = dehashid(input('post.id'));
        if (!is_numeric($id)) api_return(0,'参数错误');
        $data = Db::name('room')->where('room_id',$id)->value('money')??1;
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