<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/31 0031
 * Time: 14:24
 */

namespace app\common\logic;


use think\Model;

class NewsCollect extends Model
{

    public function addCollect($user_id,$news_id)
    {
        $this->validate(true)->save(['user_id'=>$user_id,'news_id'=>$news_id]);
        return $this->collect_id;
    }


}