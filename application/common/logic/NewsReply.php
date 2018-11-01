<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/31 0031
 * Time: 14:24
 */

namespace app\common\logic;


use think\Model;

class NewsReply extends Model
{
    public function saveChange($data){
        //判断是否已经评论
        $res = $this->where('news_id',$data['news_id'])->where('user_id',$data['user_id'])->find();
        if(!empty($res)){
            $reply_id = $res->getAttr('reply_id');
            return $this->validate(true)->allowField(true)->isUpdate(true)->save($data,['reply_id'=>$reply_id]);
        }else{
            return $this->validate('news_reply.add')->allowField(true)->save($data);
        }
    }

}