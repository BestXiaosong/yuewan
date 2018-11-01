<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/6 0006
 * Time: 14:58
 */

namespace app\index\controller;


use think\Config;

class Play extends User
{
    //直播间列表
    public function index(){

        $url = '/index/cate';
        $cate = $this->httpPost(array(),$url);
        $cid = $this->request->param('cid');
        $title = $this->request->param('title');
        $page  = $this->request->param('p');
        $info = $this->httpPost('','/index/getUser');
        $url = '/index/roomList';
        $param = array();
        if(!empty($cid)){
            $urls = '/index/getBanner';
            $result = $this->httpPost(array('cid'=>10,'num'=>3),$urls);
            $param['cid'] = $cid;
            $this->assign('cid',$cid);
            $this->assign('ad',$result);
        }
        if(!empty($title)){
            $param['title'] = $title;
        }
        if(!empty($page)){
            $param['page'] = $page;
        }
        $data = $this->httpPost($param,$url);
//        dump($data);exit;
        return view('',[
            'cate'=>$cate,
            'data'=>$data,
            'info'=>$info,
        ]);
    }


    //接口请求方法


    public function detail()
    {
        $pid = $this->request->param('pid');
        $result = $this->httpPost(array('pid'=>$pid),'/index/click');
//        dump($result);exit;
        return view('',[
            'result'=>$result,
        ]);
    }

    public function category(){
        $url = '/index/cate';
        $result = $this->httpPost(array(),$url);
        return view('',[
            'category'=>$result
        ]);
    }

    public function play_detail(){
//        $RongCloud = new \rongyun\api\RongCloud('m7ua80gbmjrnm','cWPdDytpyx4');
//        $result = $RongCloud->chatroom()->addGagUser('8xK1dyWj', 'play_vnpAvBA7', '1');
//        $result = $RongCloud->chatroom()->ListGagUser('play_vnpAvBA7');
//        dump($result);exit;

        $url = '/index/cate';
        $cate = $this->httpPost(array(),$url);
        $room = $this->request->param('room_id');
        if (is_numeric($room)) $room = hashid($room);
        $cid = $this->request->param('cid');
        $info = $this->httpPost('','/index/getUser');
//        dump($info);exit;
        if($this->token == ''){
            $this->assign('no_login',1);
        }else{
            $this->assign('no_login',0);
        }
        $param = array('id'=>$room);
        if(!empty($cid)){
            $this->assign('cid',$cid);
        }
        $url = '/index/joinRoom';
        $room_info = $this->httpPost($param,$url);
//        dump($room_info);exit;
        if($this->code != 1){
            $this->error($this->errorMsg??'系统错误');
        }
//        dump($this->json);exit;
        if($this->code != 400 && $this->code != 300){
            $url = '/index/new_activity';
            $activity = $this->httpPost($param,$url);
            $url = '/index/roleInfo';
            $room_owner_info = $this->httpPost(array('id'=>$room_info['Homeowner']),$url);
//        dump($this->json);exit;
            $url = '/index/giftList';
            $gift = $this->httpPost(array(),$url);
            $url = '/play/PlayInfo';
            $play_info = $this->httpPost($param,$url);
            $url = '/user/getMoney';
            $money = $this->httpPost(array(),$url);
            $url = '/play/roomAdmins';
            $manager_list = $this->httpPost($param,$url);
            $url = '/index/reportList';
            $report_reason = $this->httpPost(array(),$url);
            $url = '/play/roomUsers';
            $user_list = $this->httpPost($param,$url);
//        dump($this->json);exit;
            $notice_model = new \app\common\logic\Room();
            $notice = $notice_model->new_notice(dehashid($room));
            $url = '/play/pushUrl';
            $push_url = $this->httpPost($param,$url);
            $urls = '/index/getBanner';
            $result = $this->httpPost(array('cid'=>11,'num'=>2),$urls);
            return view('',[
                'ad'=>$result,
                'room_info'=>$room_info,
                'play_info'=>$play_info,
                'push_url'=>$push_url,
                'cate'=>$cate,
                'room_owner_info'=>$room_owner_info,
                'info'=>$info,
                'notice'=>$notice,
                'money'=>$money,
                'gift'=>$gift,
                'manager_list'=>$manager_list,
                'user_list'=>$user_list,
                'report_reason'=>$report_reason,
                'activity'=>$activity,
            ]);
        }elseif($this->code == 400){
           $url = '/index/getCharge';
           $money = $this->httpPost($param,$url);
           $url = '/index/explain';
           $talk = $this->httpPost(array('id'=>10),$url);
            return view('charge',[
                'cate'=>$cate,
                'info'=>$info,
                'money'=>$money,
                'talk'=>$talk,
                'room_id'=>$room,
                'code'=>400,
            ]);
        }else{
            $url = '/index/activity_money';
            $money = $this->httpPost($param,$url);
            $url = '/index/explain';
            $talk = $this->httpPost(array('id'=>10),$url);
            return view('charge',[
                'cate'=>$cate,
                'info'=>$info,
                'money'=>$money,
                'talk'=>$talk,
                'room_id'=>$room,
                'code'=>300,
            ]);
        }

    }

}