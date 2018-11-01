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
        $this->get_user_id();
    }

    public function index()
    {
        $ip = request()->ip();
        echo 'api:';
        echo  $ip;exit('end');
    }





    /**
     * 莓果websocket推送处理
     */
    public function ws()
    {
        if (request()->isPost()){
            $ip = request()->ip();
            echo  $ip;
            if ($ip == '127.0.0.1' ){
                $data = input('post.');
                $cache = cache('websocket');
                $cache[] = $data;
                cache('websocket',$cache);
                echo json_encode('success');
            }
        }
        echo json_encode('No access rights');
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





    public function test77()
    {
        $data['type'] = 'text';
        $data['data']['content'] = '测试弹幕消息';
        $data['data']['role_name'] = '测试发送者姓名';
        $data['data']['role_id'] = '测试发送者role_id';
        $data['data']['img'] = 'xxx.img';
        echo json_encode($data);
     }

    public function test99()
    {
        $id = input('id');
        dump($this->sendMsg1($id));
    }
    public function test98()
    {
        $id = input('id');
        dump($this->sendMsg1($id));
    }


    public function test()
    {
        $id   = input('id');
        $type = input('type');
        if ($type == 1){ //礼物消息
            $rows['gift_name'] = '无字天书';
            $rows['img'] = 'http://file.51soha.com/cb911201808151000427141.png';
            $rows['num'] = '10';
            $rows['role_name'] = '9999';
            $rows['role_id'] = hashid(2);
        }elseif ($type == 2){ //red_package
            $rows['msg'] = '红包信息';
            $rows['red_id']  = hashid(2);
            $rows['role_name']  = '小松';
            $rows['role_id']  =  hashid(5);
        }elseif ($type == 3){// guess 竞猜消息
            $rows['guess_id'] = hashid(2);
            $rows['title']  =  '竞猜标题';
            $rows['answer_A']  =  '竞猜选项A';
            $rows['answer_B']  =  '选项B';
        }else{
            api_return(0,'错误请求');
        }

        $this->sendMsg($id,$type,$rows);
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
     * 进入房间
     */
    public function joinRoom()
    {

        $isLogin = 0;
        $user_id = $this->user_id;
        if ($user_id){
            $isLogin = 1;
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
        }

        $id = dehashid(input('post.id'));
//        $id = input('post.id');
        if (!is_numeric($id)) api_return(0,'参数错误');
        $model = new Room();
        if ($isLogin != 0){
            $role_id = $this->role_id;
            $data = $model->msg($id,$this->user_id,$role_id);
            if (!$role_id){
                api_return(0,'请先创建角色再进入直播间');
            }
        }else{
            $data = $model->no_login_msg($id);
        }

        if ($data !== false) {
            $url = db('explain')->where(['id'=>12])->cache(86400)->value('content');
//            $data['shareUrl'] = $url.'index/share_detail/room_id/'.hashid($id).'/user_id/'.hashid($this->user_id);
            $data['shareUrl'] = $url.'index/scheme?roomid='.hashid($id).'&user_id='.hashid($this->user_id);

            if ($isLogin){
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
                //查询当前用户是否被禁言
                if ($data['play_status'] == 1){
                    $chat = $data['chat_id_play'];
                }else{
                    $chat = $data['chat_id'];
                }
                $data['baned'] = $this->chatbanList($chat,$this->role_id);
            }else{
                //查询当前用户是否被禁言
                $data['baned'] = false;
            }
            //播放地址信息
            $data['playUrl'] = $this->getPlayInfo($id);
            //融云token
            $data['r_token'] = $this->R_token($this->role_id);
            api_return(1,'获取成功',$data);
        }
        api_return(0,$model->getError());
    }



    /**
     * 获取直播推荐
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


    public function test12()
    {
        $i = 'play_'.hashid(10);
        echo hashid(3);
        echo $i;die;
//        dump($this->chatpick(hashid(6),'play_'.hashid(8)));die;
        $res = $this->chatban('play_'.hashid(8),hashid(6));
        dump($res);
        $data = $this->chatbanList('play_'.hashid(8));
        dump($data);
    }

      /*
       * 举报理由接口
       */
     public function reportList(){
        $row=report();
        if($row!==false)api_return(1,'请求成功',$row);
        api_return(0,'请求失败');
     }

    /**
     * 咨讯详情不需要登陆接口
     */
    public function newsDetails()
    {
        $news_id = Request::instance()->param('news_id');
        if (!is_numeric($news_id)) {
            api_return(0, 'news_id必须是数字');
        }

        $model = new NewsModel();
        $detail = $model->getNewsDetailsById($news_id);
        //利好利空状态
        $user_id = $this->user_id;
        $replymodel = new \app\common\model\NewsReply();
        $type = $replymodel->getReplyStatus($news_id, $user_id);
        $detail['type'] = $type;
        //收藏状态
        $collectmodel = new NewsCollectModel();
        $colect_status = $collectmodel->getCollectByUser($user_id, $news_id);
        if (!empty($colect_status)) {
            $detail['collect_id'] = $colect_status['collect_id'];
            $detail['collect_status'] = 1;
        } else {
            $detail['collect_id'] = 0;
            $detail['collect_status'] = 0;
        }

        //咨讯详情页推荐
        $recommend = $model->getNewsListByCid($detail->cid, 3);
        $arr = array();
        $arr['detail'] = $detail;
        $arr['list'] = $recommend;
        $detail ? api_return(1, '获取成功', $arr) : api_return(0, '获取失败');
    }


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 测试推送
     */
    public function Push($type,$j_push_id = '')
    {
        if (empty($j_push_id)){
            $j_push_id = '170976fa8aaa348b423';
        }
        $extend = [];
        if ($type == 1){
            $extend['extras'] = [
                'type' => 'room',//跳转至房间
                'room_id' => 'oLpYxK8G',
            ] ;
        }elseif ($type == 2){
            $extend['extras'] = [
                'type' => 'assets',//跳转至资产
            ] ;
        }elseif ($type == 3){
            $extend['extras'] = [
                'type' => 'wallet',//跳转至钱包
            ] ;
        }
        $data =  j_push('小松的测试推送',$j_push_id,$extend);
        dump($data);exit;
    }

    public function test1111()
    {
        echo hashid(8);exit;


        echo date('Y-m-d H:i:s',1538243024);

    }


    public function pushUrl()
    {
        $data = $this->getPushUrl(10);
        api_return(1,'test',$data);
    }


    /**
     * 性能测试
     */
    public function chunk()
    {
        for ($i = 0;$i < 100000;$i++){
            $data[$i]['type'] = rand(1,9999999999);
            $data[$i]['user_id'] = rand(1,9999999999);
            $data[$i]['smoney'] = rand(1,9999999999);
            $data[$i]['create_time'] = time();
            $data[$i]['update_time'] = time();
        }
        Db::name('test')->insertAll($data);
    }


    public function chunk1()
    {
        Db::name('test')->chunk(100, function($users) {
            foreach ($users as $user) {
                echo count($users);
            }
        });
    }

    public function chunk2()
    {
        Db::name('test')->select();
    }

    public function test7($num = 100000)
    {
        for ($i = 0;$i <= $num;$i++){
            $data[$i]['guess_id'] = 16;
            if ($i / 2 == 0){
                $data[$i]['answer'] = 'A';
            }else{
                $data[$i]['answer'] = 'B';
            }
            $data[$i]['role_id'] = 17;
            $data[$i]['user_id'] = 3;
            $data[$i]['money']   = rand(1,99999);
        }
        echo Db::name('guess_record')->insertAll($data);



    }

    /**
     * 获取回放列表
     */
    public function vodlist()
    {
//        $where['v.top'] = 0;
        $where['v.status'] = 1;
        $cid =$this->request->post('cid');
        if(!empty($cid)){
            $where['v.cid'] = $cid;
        }
        $model = new \app\common\model\Vod();
        if($this->user_id == 0){
            $result = $model->no_login_getRows($where);
        }else{
            $result = $model->getRowss($where,$this->user_id);
        }
        if($result){
            api_return(1,'成功',$result);
        }else{
            api_return(0,'暂无数据');
        }
    }



    /**
     *回放视频点击次数
     */
    public function click()
    {
        $pid = $this->request->post('pid');
//        $pid = 1;
//        $id = 1;
        $model = new \app\common\logic\Vod();
        if($this->user_id == 0){
            $result = $model->no_login_click($pid);
        }else{
            $result = $model->click($pid,$this->user_id);
        }
//        print_r($result);exit;
        if($result){
            api_return(1,'操作成功',$result);
        }else{
            api_return(0,'操作失败');
        }
    }


    /**
     * 获取回放推荐列表
     */
    public function lists()
    {
        $where['v.top'] = 1;
        $where['v.status'] = 1;
//        $id = 1;
        $model = new \app\common\model\Vod();
        if($this->user_id == 0){
            $result = $model->no_login_getLists();
        }else {
            $result = $model->getLists($where, $this->user_id);
        }
//        echo $result;exit;
        if($result){
            api_return(1,'成功',$result);
        }else{
            api_return(0,'暂无数据');
        }
    }
    /**
     * 获取回放分类列表
     */
    public function catelist()
    {
        $where['status'] = 1;
        $model = new \app\common\model\Vod();
        $result = $model->getCate($where);
        if($result){
            api_return(1,'成功',$result);
        }else{
            api_return(0,'暂无数据');
        }
    }

    /**
     * 获取最新的活动
     */
    public function act()
    {
        $where['status'] = 2;
        $data = Db::name('room_activity')
            ->where($where)
            ->order('activity_id desc')
            ->field('activity_id,title,img,detail,charge,start_time,reserve,money,status')
            ->find();
        if (!empty($data)){
            $map['activity_id'] = $data['activity_id'];
            $map['status'] = 1;
            $map['role_id'] = $this->role_id;
            $record_id = Db::name('room_record')->where($map)->value('record_id');
            $data['activity'] = empty($record_id) ?0:1;
            api_return(1,'获取成功',$data);
        }

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
        $model = new RoomActivity();
        $rows  = $model->actMsg($where,$this->role_id);
        if ($rows !== false) api_return(1,'获取成功',$rows);
        api_return(0,'暂无数据');
    }

    /**
     * 获取推荐主播
     */
    public function role()
    {
        $where = [];
        if (!empty(input('post.role_name'))) $where['role_name'] = ['like','%'.trim(input('post.role_name')).'%'];
        $rows = Db::name('role')->where($where)->field('role_name,header_img,sign,role_id,fans_num')->order(['top'=>'desc','update_time'=>'desc'])->limit(10)->cache(300)->select();
        if (!empty($rows)) {
            foreach ($rows as $k => $v){
                $map = [];
                $map['role_id'] = $v['role_id'];
                $map['follow_role_id'] = $this->role_id;
                $map['status'] = 1;
                $rows[$k]['is_follow'] = Db::name('role_follow')->where($map)->value('status')??0;
                $rows[$k]['role_id'] = hashid($v['role_id']);
            }
            api_return(1,'获取成功',$rows);
        }

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
     * 首页咨询
     */
    public function newsList()
    {
        $newModel = new NewsModel();
        $top = $newModel->getNewsTop();
        $newslist = $newModel->apiNewsList();
        $type = request()->param('type');
        $num = count($top);
        if ($type == 'pc') {
            if($num == 0){
                $num += 1;
            }else{
                unset($top[0]);
            }
            if ($num < 3) {
                foreach ($newslist['data'] as $k => $va) {
                    if ($num == 3) {
                        break;
                    }
                    $top[] = $va;
                    $num += 1;
                }
            }else{
                $arr[] = $top[1];
                $arr[] = $top[2];
//                unset($top);
                $top = $arr;
            }
        } else {
            if ($num < 5) {
                foreach ($newslist['data'] as $k => $va) {
                    if ($num == 5) {
                        break;
                    }
                    $top[] = $va;
                    $num += 1;
                }

            }

        }
        $top ? api_return(1, '获取成功', $top) : api_return(0, '获取失败');
    }


    //top文章
    public function topic()
    {
        $accessKey = Config::get('coding_access_key');
        $secretKey = Config::get('coding_secret_key');

        $httpParams = array(
            'access_key' => $accessKey,
            'date' => time(),

        );
        $last_id = Request::instance()->post('last_id');
        if (!empty($last_id)) {
            $httpParams['last_id'] = $last_id;
        }
        $signParams = array_merge($httpParams, array('secret_key' => $secretKey));

        ksort($signParams);
        $signString = http_build_query($signParams);

        $httpParams['sign'] = strtolower(md5($signString));

        $url = 'http://api.coindog.com/topic/list?' . http_build_query($httpParams);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $curlRes = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($curlRes, true);


        if (array_key_exists('status_code', $json)) {
            api_return(0, $json['message']);
        } else {
            if (empty($json)) {
                api_return(0, '没有数据');
            }
            foreach ($json as $k=>$v){
                if($k>8){
                    unset($json[$k]);
                }
            }
            api_return(1, '获取成功', $json);
        }

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

    public function  giftList()
    {

        $model=new Gifts();
        $showList=$model->giftList(['status'=>1]);
        $showList?api_return(1,'获取成功',$showList):api_return(0,'获取失败');
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
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 获取活动收费金额
     */
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


    /**
     * 获取用户信息
     */
    public function getUser()
    {
        if($this->user_id == 0){
            api_return(1,'获取成功',array());
        }else{
            $where['a.user_id'] =$this->user_id;
            $model  = new Users();
            $result = $model->getDetail($where);
            if ($result !== false) {
                api_return(1, '获取成功', $result);
            } else {
                api_return(0, '获取失败');
            }
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
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 活动详情
     */
    public function actDetail()
    {
        $where['a.activity_id'] = input('post.id');
        $model = new RoomActivity();
        $rows  = $model->getOne($where,$this->role_id);
        if ($rows !== false) {
            $url = db('explain')->where(['id'=>12])->cache(60)->value('content');
//            $rows['shareUrl'] = $url.'index/share_detail?room_id='.$rows['room_id'].'&user_id='.hashid($this->user_id);
            $rows['shareUrl'] = $url.'index/scheme/?roomid='.hashid($rows['room_id']).'&user_id='.hashid($this->user_id);
            api_return(1,'获取成功',$rows);
        }
        api_return(0,'暂无数据');
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
     * 房间活动列表
     */
    public function actList()
    {
        $where['room_id'] = dehashid(input('post.id'));
//        $where['room_id'] = input('post.id');
        if (!is_numeric($where['room_id'])) api_return(0,'参数错误');
        $model = new RoomActivity();
        $rows  = $model->history($where,$this->role_id);
        if ($rows !== false) api_return(1,'获取成功',$rows);
        api_return(0,'暂无数据');
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
}
