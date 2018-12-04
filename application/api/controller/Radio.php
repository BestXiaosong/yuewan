<?php
/**
 * Created by xiaosong
 * E-mail:4155433@gmail.com
 * Date: 2018/12/4
 * Time: 16:33
 * 电台管理模块
 */

namespace app\api\controller;


use wheat\Wheat;

class Radio extends Api
{
    private static $roomInfo = null;

    private static $room_id = null;

    public function _initialize()
    {
        parent::_initialize();

        self::$room_id = input('post.room_id');

        self::$roomInfo = $this->roomInfo(self::$room_id);
        if (self::$roomInfo['type'] != 1){
            api_return(0,'房间类型访问错误');
        }

    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 封麦|开麦
     */
    public function wheatChange()
    {
        if ($this->user_id != self::$roomInfo['user_id']){
            api_return(0,'您不是房主,不能封麦');
        }

        $data = request()->only(['wheat_id','type'],'post');

        $wheat = new Wheat();

        if ($data['type'] == 1){ //封麦
            $ret = $wheat->lock(self::$room_id,$data['wheat_id']);
        }else{ //开麦
            $ret = $wheat->open(self::$room_id,$data['wheat_id']);
        }

        if($ret['code']){
            api_return(1,$ret['msg'],$ret['data']['wheat']);
        }else{
            api_return(0,$ret['msg']);
        }

    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 踢下麦
     */
    public function downWheat()
    {
        if ($this->user_id != self::$roomInfo['user_id']){
            $power = self::roomPower(self::$room_id,$this->user_id);
            if ($power == 0){
                api_return(0,'权限不足,不能封麦');
            }
        }

        $data = request()->only(['wheat_id'],'post');

        $wheat = new Wheat();

        $ret = $wheat->down(self::$room_id,$data['wheat_id']);

        if($ret['code']){
            api_return(1,$ret['msg'],$ret['data']['wheat']);
        }else{
            api_return(0,$ret['msg']);
        }
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

        if (self::$roomInfo['user_id'] == $this->user_id){ //房主
            $power = 1;
        }else{
            $power = self::roomPower(self::$room_id,$this->user_id);
        }

        $wheat = new Wheat();
        if(isset($post['type']) && $post['type']  == 1){//抱麦

            if ($power == 0 ){
                api_return(0,'权限不足,不能操作');
            }

            if ($post['wheat_id'] == 1){
                api_return(0,'主播位不能抱麦');
            }

            $ret = $wheat->embrace($post['user_id'],$post['room_id'],$post['wheat_id']);
        }else{//上麦

            if ($post['wheat_id'] == 1){
                if ($power == 0 ){
                    api_return(0,'权限不足,不能上主播位');
                }
            }
            $ret = $wheat->on($post['user_id'],$post['room_id'],$post['wheat_id']);
        }
        if($ret['code']){
            api_return(1,$ret['msg'],$ret['data']['wheat']);
        }else{
            api_return(0,$ret['msg']);
        }
    }


}