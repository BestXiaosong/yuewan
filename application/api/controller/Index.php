<?php

namespace app\api\controller;

use app\common\model\Banner;
use app\common\model\News as NewsModel;
use app\common\model\NewsCollect as NewsCollectModel;
use app\common\model\PlayCategory;
use app\common\model\Room;
use app\common\model\RoomActivity;
use think\Request;
use think\Db;
use app\common\model\Gift as Gifts;
use app\common\model\Users;
use app\common\model\RoomFollow;
use think\Config;

class Index extends Base
{


    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $ip = request()->ip();
        echo 'api:';
        echo  $ip;exit('end');
    }








    /**
     * 发送短信
     */
    public function sms()
    {
        if (request()->isPost()){
            $phone = input('post.phone');
            $has   = cache('code_num'.$phone);
            $endToday = strtotime(date('Y-m-d 23:59:59'));
            $time = $endToday - time();
            if ($has){
                if ($has['num'] >= 15)api_return(0,'短信发送过多');
                if ($has['time']+60 > time())api_return(0,'短信发送过于频繁,请一分钟后重试');
            }else{
                cache('code_num'.$phone,['num'=>1,'time'=>time()],$time);
            }
            $result = sendSms($phone);
            if ($result){
                cache('code_num'.$phone,['num'=>$has['num']+1,'time'=>time()],$time);
                api_return(1,'发送成功');
            }else{
                api_return(0,'发送失败');
            }
        }
        api_return(666,999);
    }

//    public function pushUrl()
//    {
//        $data = $this->getPlayInfo(1);
//        api_return(1,'获取成功',$data);
//    }

    //生成微信分享二维码

    public function shareQrcode(){
        $data = request()->param('url');
        $res = code($data);
        api_return(1,'成功',$res);
    }




    /**
     * 版本控制
     */
    public function version()
    {
        $new = Db::name('version')->field('versionCode,versionName,force,url,detail')->order('id desc')->cache(300)->find();
        if (!empty($new)) api_return(1,'获取成功',$new);
        api_return(0,'系统错误');
    }



    /**
     * 获取banner及广告图
     */
    public function getBanner()
    {
        $model = new Banner();
        $where['status'] = 1;
        $where['cid']    = input('post.cid');
        $limit = is_numeric(input('post.num'))?input('post.num'):6;
        $rows  = $model->getBanner($where,$limit);
        if (!empty($rows)) api_return(1,'获取成功',$rows);
        api_return(0,'暂无数据');
    }


    /**
     * 获取说明文档
     */
    public function explain()
    {
        $id = input('post.id');
        if (!is_numeric($id)) api_return(0,'参数错误');
        $data = Db::name('explain')->where('id',$id)->value('content');
        if (!empty($data)) api_return(1,'获取成功',$data);
        api_return(0,'暂无数据');
    }

    /**
     * 获取当前角色粉丝列表
     */
    public function my_fans(){
        $id = input('post.id');
        if (!empty($id)){
            $role_id = dehashid($id);
            if (!is_numeric($role_id)) api_return(0,'参数错误');
        }else{
            $this->user_id;
            $role_id = $this->role_id;
        }
        $model = new Users();
        $list_num = $this->request->post('list_num')?$this->request->post('list_num'):'';
        $result = $model->fans($role_id,$list_num);
        if($result){
            api_return(1,'获取成功',$result);
        }else{
            api_return(0,'暂无数据');
        }
    }


    /**
     * 根据role_id获取角色信息及是否关注
     */
    public function roleInfo()
    {
        $role_id = input('id');
        $where['a.role_id'] = dehashid($role_id);
//        $where['a.role_id'] = input('post.id');
        if (!is_numeric($where['a.role_id'])) api_return(0,'参数错误');
        $model = new \app\common\model\Role();
        $data  = $model->getOne($where,$this->role_id);
        if ($data !== false) {
            if (!empty(input('post.chat'))){
                $chat = input('post.chat');
                $data['baned'] = $this->chatbanList($chat,$role_id);
                $info = explode('_',$chat);
                $room_id = $info[1];
                $roomInfo = Db::name('room')->where('room_id',$room_id)->field('user_id')->cache(60)->find();
                if ($roomInfo['user_id'] == $this->user_id){
                    $data['is_admin'] = 1;
                }else{
                    $roomfollow = new RoomFollow();
                    $res = $roomfollow->getStatus(dehashid($room_id), $where['a.role_id']);
                    if ($res['status'] == 2) {
                        $data['is_admin'] = 1;
                    }else{
                        $data['is_admin'] = 0;
                    }
                }
            }
            api_return(1,'获取成功',$data);
        }
        api_return(0,'服务器繁忙,请稍后重试');
    }

    /**
     * 获取融云token
     */
    public function token()
    {
        $token = $this->R_token($this->role_id);
        if ($token !== false) api_return(1,'获取成功',$token);
        api_return(0,'服务器繁忙,请稍后重试');
    }




    /**
     * 回放视频搜索
     */
    public function vediosearch()
    {
        $where['v.title'] = ['like','%'.trim($this->request->post('title')).'%'];
//        $where['title'] = ['like','%数%'];
        $where['v.status'] = 1;
        $id = $this->user_id;
        $model = new \app\common\model\Vod();
        $result = $model->getVedio($where,$id);
        if($result){
            api_return(1,'成功',$result);
        }else{
            api_return(0,'暂无数据');
        }
    }

    public function img()
    {
        getVideoCover('http://file.51soha.com/vod0v21072460.mp4');
    }


}
