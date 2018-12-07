<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/2
 * Time: 10:36
 */

namespace app\api\controller;


use app\common\model\GiftRecord;
use app\common\model\Room;
use app\common\model\RoomBlacklist;
use app\common\model\RoomLog;
use app\common\model\UserGuard;
use app\common\model\Users;
use think\Db;

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

        $data = $model->joinRoom($data['room_id'],$this->user_id,trim($data['password']));

        if ($data !== false) {
            //查询当前用户是否被禁言

            $data['baned'] = $this->chatbanList($data['room_id'],$this->user_id);
            //修改当前用户所在房间
            Db::name('user_extend')->where('user_id',$this->user_id)->update(['room_id'=>$data['room_id']]);

            api_return(1,'获取成功',$data);
        }
        api_return(0,$model->getError());
    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 房间在线用户列表
     */
    public function onlineUser()
    {
        $map = [];
        $id  = input('post.room_id');

        $map['e.room_id']       = $id;
        $map['e.online_status'] = 1;
        $model = new Users();
        $rows = $model->getItems($map);

        api_return(1,'获取成功',$rows);
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 房间日志
     */
    public function roomLog()
    {
        $map['room_id'] = input('post.room_id');

        if (!is_numeric($map['room_id'])) api_return(0,'房间id错误');

        $model = new RoomLog();

        $rows  = $model->getRows($map);

        api_return(1,'获取成功',$rows);
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 房间黑名单列表
     */
    public function blackList()
    {
        $map['a.room_id'] = input('post.room_id');
        if (!is_numeric($map['a.room_id'])) api_return(0,'房间id错误');

        $map['a.status'] = 1;
        $map['a.end_time'] = ['>',time()];

        $model = new RoomBlacklist();

        $rows  = $model->getRows($map);

        api_return(1,'获取成功',$rows);

    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 房间打赏列表
     */
    public function roomGift()
    {

        $map['a.room_id'] = input('post.room_id');
        if (!is_numeric($map['a.room_id'])){
            api_return(0,'参数错误');
        }
        $map['a.status']  = 1;
        $model = new GiftRecord();

        $rows = $model->giftList($map);

        api_return(1,'获取成功',$rows);
    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 真爱排行榜
     */
    public function trueLove()
    {

        $map['a.status'] = 1;
        $map['a.room_id'] = ['>',0];
        $map['g.price'] = ['>',519];
        $model = new GiftRecord();

        $rows = $model->getTrueLove($map);

        api_return(1,'获取成功',$rows);
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 守护排行榜
     */
    public function protect()
    {
        $map['a.status'] = 1;
        $map['a.room_id'] = ['>',0];
        $model = new GiftRecord();
        $rows = $model->protect($map);
        api_return(1,'获取成功',$rows);
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 获取CP排行榜
     */
    public function guard()
    {
        $time = cache('guardTime');
        $map = [];

        if (time()+86430 > $time){
            $time = time() + 86430;
            cache('guardTime',$time);
        }

        $map['a.status'] = 1;
        $map['a.end_time'] = ['>',$time];

        $model = new UserGuard();
        $rows = $model->getRows($map);

        api_return(1,'获取成功',$rows);

    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 获取贵族排行榜
     */
    public function noble()
    {

        $time = cache('nobleTime');
        $map = [];

        if (time()+30 > $time){
            $time = time() + 30;
            cache('nobleTime',$time);
        }

        $map['e.noble_id']   = ['>',0];
        $map['e.noble_time'] = ['>',$time];

        $model = new Users();

        $rows = $model->getNoble($map);

        api_return(1,'获取成功',$rows);

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




}