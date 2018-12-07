<?php
/**
 * Created by xiaosong
 * E-mail:4155433@gmail.com
 * Date: 2018/12/6
 * Time: 9:51
 * 娱乐房间模块
 */

namespace app\api\controller;


use wheat\Wheat;

class Fun extends Radio
{

    private static $free = 1;

    public function _initialize()
    {
        self::$roomType = 2;
        self::$generalNotUp = [1,2];

        parent::_initialize();

        $free = cache('room_free_'.self::$room_id);

        if ($free === 'NotFree'){
            self::$free = 0;
        }
    }

    /**
     * 上麦、换麦、抱麦
     * */
    public function upWheat(){

        $post = request()->only(['wheat_id','type','user_id'],'post');
        //验证数据
        $result = $this->validate($post,'Wheat.up');
        if(true !== $result){
            api_return(0,$result);
        }

        if(!empty($post['user_id'])){
            $post['user_id'] = dehashid($post['user_id']);
            if (!is_numeric($post['user_id'])) api_return(0,'参数错误');
        }

        $wheat = new Wheat();
        if(isset($post['type']) && $post['type']  == 1){//抱麦

            $this->checkPower($this->user_id);

            if ($post['wheat_id'] == 1 || $post['wheat_id'] == 2){
                api_return(0,'房主和接待位不能抱麦');
            }

            $ret = $wheat->embrace($post['user_id'],self::$room_id,$post['wheat_id']);
        }else{//上麦

            $powerCode = $this->getPowerCode();

            switch ($powerCode){
                case 1:
                    api_return(0,'房主不能上其它麦位');
                    break;
                case 2:
                case 3:
                    if ($post['wheat_id'] == 1){
                        api_return(0,'房主位不能上麦');
                    }
                    break;
                default:
                    if (self::$free){
                        if (in_array($post['wheat_id'],self::$generalNotUp)){
                            if (!$powerCode){
                                api_return(0,'非管理员不能上麦');
                            }
                        }
                    }else{
                        if ($post['wheat_id'] != 9){
                            api_return(0,'观众只能上老板位,其它麦位需申请');
                        }
                    }
                    break;
            }
            $ret = $wheat->on($this->user_id,self::$room_id,$post['wheat_id'],$powerCode);
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
     * 选项
     */
    public function option()
    {
        $wheat = new Wheat();
        $wheatInfo = $wheat->getWheat(self::$room_id);
        $data['show_house'] = $wheatInfo[0]['is_show'];
        $data['show_admit'] = $wheatInfo[1]['is_show'];
        $data['show_boss']  = $wheatInfo[8]['is_show'];
        $data['show_rank']  = 1;
        $data['is_free']    = self::$free;
        api_return(1,'获取成功',$data);
    }

    public function changeOption()
    {

        //TODO 改变选项卡状态


    }
    
    
    







}