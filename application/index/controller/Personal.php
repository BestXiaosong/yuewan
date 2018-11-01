<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/3 0003
 * Time: 15:17
 */

namespace app\index\controller;


use app\common\model\Users;
use Qiniu\Auth;
use think\Db;

class Personal extends User
{

    public function top()
    {
        return view();
    }

    public function left()
    {
        return view();
    }


    public function personal()
    {
        return view();
    }

    /**
     * @return \think\response\View
     * 账号信息
     */
    public function accountinfo()
    {
        $phone = $this->phone;
        $token = $this->token;

        $data = curl_post(config('api_domain') . '/user/getUser', ['phone' => $phone, 'token' => $token]);
        $data = json_decode($data, true);
        if ($data['code'] == 1) {
            $this->assign('data', $data['data']);
        } else {
            $this->assign('data', array());
        }

        $ex = Db::table('cl_extend')->where('id', 1)->field('app_qrcode')->find();
        $this->assign('app_qrcode', $ex['app_qrcode']);
        return view();
    }

    /**
     * 换绑手机号
     */

    public function resetPhone()
    {
        if (request()->isAjax()) {
            $user = new \app\common\logic\Users();
            $phone = $this->phone;
            $newphone = request()->post('phone');
            $code = request()->post('code');
            $cache_code = cache('code' . $newphone);
            if ($cache_code != null && $code == $cache_code) {
                $res = $user->resetPhone($phone, $newphone);
                if ($res !== false) {
                    api_return(1, '修改成功');
                } else {
                    api_return(0, '修改失败');
                }
            }
            api_return(0, '验证码错误');

        } else {
            api_return(0, '非法操作');

        }
    }

    /**
     * @return \think\response\View
     * 角色信息
     */
    public function roleinfo()
    {
        if (request()->isAjax()) {

        }
        //粉丝
        $myfans = $this->httpPost(array('list_num' => 7), '/User/my_fans');
        $this->assign('fans', $myfans);
        //当前角色基本资料
        $data = $this->httpPost(array(), '/user/getUser');
        $this->assign('data', $data);
        return view();
    }

    /**
     * @return \think\response\View
     * 我的关注
     */

    public function focusroom()
    {
        $room = $this->httpPost(array(), '/focus/focusRoom');
        $role = $this->httpPost(array(), '/Focus/focusRole');
        $this->assign('room', $room['data']);
        $this->assign('role', $role['data']);
        return view();
    }

    /**
     * @return \think\response\View
     * 观看历史
     */
    public function watchhistory()
    {

//        $history = $this->httpPost(array(), '/self_room/vod');
        $room = $this->httpPost(array('type' => 2), '/Vod/history');
        $history = $this->httpPost(array('type' => 1), '/Vod/history');
        $this->assign('room', $room);
        $this->assign('data', $history);
        return view();
    }

    /**
     * @return \think\response\View
     * 我的房间
     */

    public function myroom()
    {
        //我创建的房间
        $roomlist = $this->httpPost(array(), '/self_room/addRoomList');
        //我管理的房间
        $glroom = $this->httpPost(array(), '/self_room/glRoom');
        //我的回放
        $history = $this->httpPost(array(), '/self_room/vod');
//        dump($history);exit;
        $space = $this->httpPost(array(), '/Self_room/space');
        $user = $this->httpPost(array(), '/user/getUser');
//        dump($user);exit;
        $this->assign('space', $space[0]);
        $this->assign('user', $user);
        $this->assign('history', $history['data']);
        $this->assign('creroom', $roomlist['data']);
        $this->assign('glroom', $glroom['data']);


        return view();
    }

    /**
     * @return \think\response\View
     * 我的钱包
     */

    public function mywallet()
    {
        //用户信息
        $user = $this->httpPost(array(), '/user/getUser');
        //积分兑换汇率
        $rate = $this->httpPost(array(), '/Api/rate');
        $rate['charge'] = floatval($rate['charge'] / 100);
        //bcdn兑积分记录
        $record = $this->httpPost(array('type' => 1), '/user/record');
        //积分兑换BCDN
        $monto = $this->httpPost(array('type' => 2), '/user/record');
        //明细
        $details = $this->httpPost(array(), '/Money/detail');
        $this->assign('details', $details['data']);

        $this->assign('data', $user);
        $this->assign('rate', $rate);
        $this->assign('bcdnto', $record['data']);
        $this->assign('monto', $monto['data']);

        return view();

    }

    /**
     * @return \think\response\View
     * 系统消息
     */

    public function message()
    {
        $sys = $this->httpPost(array(), '/message/systemmessage');
        $this->assign('sys', $sys);
        $actmsg = $this->httpPost(array(), '/play/actMsg');
        $this->assign('actmsg', $actmsg);

        return view();
    }

    /**
     * @return \think\response\View
     * 红包
     */
    public function myred()
    {
        $send = $this->httpPost(array(), '/red_package/mysendred');
        $this->assign('send', $send);
        $recevie = $this->httpPost(array(), '/red_package/myreceive');
        $this->assign('receive', $recevie);
        return view();
    }

    /**
     * @return \think\response\View
     * 聊天室管理
     */
    public function chatroom()
    {
        //我管理的房间
        $glroom = $this->httpPost(array(), '/self_room/addRoomList');
        $this->assign('glroom', $glroom['data']);

        //默认查询第一个房间的禁言名单
        if (!empty($glroom['data'])) {
            $room_id = 'play_' . $glroom['data'][0]['room_id'];
            $ban = $this->httpPost(array('room_id' => $room_id), '/chat_room/bannedlist');
            $this->assign('banlist', $ban);
        } else {
            $this->assign('banlist', array());
        }


        return view();
    }

    /**
     * 房间管理
     */

    public function mgroom()
    {
        $glroom = $this->httpPost(array(), '/self_room/addRoomList');
        $this->assign('glroom', $glroom['data']);
        //房间分类
        $cata = $this->httpPost(array(), '/play/cate');
        $this->assign('cate', $cata);
        if (!empty($glroom['data'])) {
            $room_id = $glroom['data'][0]['room_id'];
            //管理员
            $manerge = $this->httpPost(array('id' => $room_id), '/play/roomAdmins');
            $this->assign('manage', $manerge);
        } else {
            $this->assign('manage', '');
        }
        return view();
    }


    /**
     * 发起活动
     */

    public function activity()
    {
        //我管理的房间
        $glroom = $this->httpPost(array(), '/self_room/addRoomList');
        $this->assign('glroom', $glroom['data']);
        return view();
    }

    public function collect()
    {

        $list = $this->httpPost([], '/news/mycollect');
        $this->assign('list', $list['data']);
        return view();

    }

    /*
     * 个人中心竞猜
     */
    public function  guess()
    {

        $sendGuess = $this->httpPost([], '/guess/selfSendGuess');//我发起的竞猜
        $recoive = $this->httpPost([], '/guess/selfJoinGuess');//我参与的竞猜

        $this->assign([
            'list' => $sendGuess['data'],
            'row' => $recoive['data']
        ]);
        return view();
    }

    /*
     * 个人中心资产
     */
    public function money()
    {
        /*
         * 我的资产角色
         *  我的房间
         * 礼物
         */
        $myrole = $this->httpPost([], '/user/roleList');
        $myroom = $this->httpPost([], '/self_room/addRoomList');
        $gift = $this->httpPost([], '/gift/myList');
        $cid = $this->httpPost([], '/Vod/catelist');

        $this->assign([
            'role' => $myrole,
            'room' => $myroom['data'],
            'gift' => $gift,
            'cid' => $cid
        ]);
        return view();
    }

    /*
     * 个人中心拍卖
     */

    public function  uction()
    {
        $joinUction = $this->httpPost([], '/Sale/join');
        $selfUction = $this->httpPost([], '/Sale/initiate');

        foreach ($joinUction['data'] as $k => $v) {
            if ($v['type'] == 0) {
                $joinUction['data'][$k]['type'] = '角色';
            } elseif ($v['type'] == 1) {
                $joinUction['data'][$k]['type'] = '房间';
            } elseif ($v['type'] == 2) {
                $joinUction['data'][$k]['type'] = '房间名称';
            }
        }

        foreach ($selfUction['data'] as $k => $v) {
            if ($v['type'] == 0) {
                $selfUction['data'][$k]['type'] = '角色';
            } elseif ($v['type'] == 1) {
                $selfUction['data'][$k]['type'] = '房间';
            } elseif ($v['type'] == 2) {
                $selfUction['data'][$k]['type'] = '房间名称';
            }
        }

        $this->assign([
            'join' => $joinUction['data'],
            'self' => $selfUction['data']
        ]);
        return view();
    }


    /*
     * 我的资产礼物查看详情
     */
    public function giftdetails()
    {
        $type = input('type');
        $id = input('gift_id');
        $list = $this->httpPost(['type' => $type, 'id' => $id], '/gift/giftDetail');
        $money = $this->httpPost(['gift_id' => $id], '/gift/changeMoney');
        $this->assign([
            'id' => $id,
            'list' => $list['data'],
            'row' => $money
        ]);
        return view();
    }


    public function vodupload()
    {
        $cid = $this->httpPost(array(), '/vod/catelist');
        $this->assign('cid', $cid);
        return view();
    }

    public function delete1()
    {
        $file = $this->request->param('file');
        $result = delVod($file);
        if (!$result) {
            return array('code' => 1);
        } else {
            return array('code' => 0);
        }
    }


    public function about()
    {
        return view();
    }

    public function invite()
    {
        return view();
    }

}