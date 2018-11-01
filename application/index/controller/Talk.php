<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/31
 * Time: 15:11
 */

namespace app\index\controller;


use rongyun\api\RongCloud;

class Talk extends Base
{
    public function index()
    {
        $content = $this->request->post('content');
        $room = $this->request->post('room_id');

        $RongCloud = new RongCloud(config('rongyun')['appKey'],config('rongyun')['appSecret']);
        $result = $RongCloud->message()->publishChatroom('1',['play_dgyl1KZJ'],'RC:TxtMsg',json_encode(array('content'=>'测试数据','extra'=>array('sendname'=>'xxx','sendimg'=>'yyyy'))));
        var_dump($result);exit;
    }

}