<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/31 0031
 * Time: 14:10
 */

namespace app\common\model;


use think\Model;

class NewsReply extends Model
{

    public function newsReplyList($where=array())
    {
        return $this
            ->where($where)
            ->order('reply_id desc')
            ->paginate('',false,['query'=>request()->param()]);

    }


    public function getReplyStatus($news_id,$user_id){
        $res = $this->field('type')->where('news_id',$news_id)->where('user_id',$user_id)->where('status=1')->find();
        if(!empty($res)){
            $res->toArray();
            return $res['type'];
        }else{
            return 0;
        }
    }




}