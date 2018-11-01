<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6 0006
 * Time: 10:22
 */

namespace app\api\controller;


use think\Request;

class Message extends User
{
    //系统消息
    public function systemMessage()
    {
        $model = new \app\common\model\Message();
        $data = $model->getSysMessageList();
        if(empty($data)){
            api_return(0,'没有数据');
        }
        api_return(1,'获取成功',$data);
    }



}